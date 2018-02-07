<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use \DB;
use App\Models\Vip\Term;
use App\Models\Vip\Entity as Vip;
use App\Services\Brand;
use App\Models\Contract\Contract;
use App\Models\Agent\Invitation;
use App\Models\News\Entity as News;
use App\Models\Video\Entity as Video;
use App\Models\Score\Goods\V020700 as Score;
use App\Models\Brand\BrandGoods;
use App\Models\Live\LiveBrandGoods;

class Items extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'orders_items';


    public function orders()
    {
        return $this->belongsTo('App\Models\Orders\Entity', 'order_id', 'id');
    }

    //关联合同
    public function belongsToContract()
    {
        return $this->belongsTo(Contract::class, 'product_id', 'id');
    }

    //关联邀请表
    public function belongsToInvitation()
    {
        return $this->belongsTo(Invitation::class, 'product_id', 'id');
    }

    //关联资讯
    public function belongsToNews()
    {
        return $this->belongsTo(News::class, 'product_id', 'id');
    }

    //关联录播
    public function belongsToVideo()
    {
        return $this->belongsTo(Video::class, 'product_id', 'id');
    }

    //关联商品积分表
    public function belongsToScore()
    {
        return $this->belongsTo(Score::class, 'product_id', 'id');
    }

    //关联品牌商品
    public function belongsToBrandGoods()
    {
        return $this->belongsTo(BrandGoods::class, 'product_id', 'id');
    }

    //关联直播品牌商品
    public function live_brand_goods()
    {
        return $this->belongsTo(LiveBrandGoods::class, 'product_id', 'id');
    }

    //黑名单
    protected $guarded = [];

    static function getRow($where)
    {
        return self::where($where)->first();
    }

    static function getRows($where)
    {
        return self::where($where)->get();
    }


    static function produce($order_id, Array $array)
    {
        $array['order_id'] = $order_id;

        //如果是合同并且有基金
        if (isset($array['fund_id']) && $array['fund_id'] && $array['type'] == 'contract') {
            Contract::where('id', $array['product_id'])->update(['fund_id' => $array['fund_id']]);
            unset($array['fund_id']);
        }


        if ($array['type'] == 'contract') {
            $contract = Contract::with('invitation')->where('id', $array['product_id'])->first();
                if($contract->invitation && $contract->invitation->status!=3 && $contract->invitation->status!=1){
                    return false;
                }
                if($contract->invitation){
                    Contract::where('id', '<>', $array['product_id'])->where('invitation_id', $contract->invitation->id)->update(['invitation_id'=>0]);
                }
        }



        $item = self::create($array);

        return $item;
    }

    /**
     *根据订单no更新 orders及orders_items状态
     */
    static function updateByNo($order_no, $status)
    {
        $result = DB::table('orders')
            ->leftJoin('orders_items', 'orders.id', '=', 'orders_items.order_id')
            ->where('orders.order_no', $order_no)
            ->update(['orders.status' => $status, 'orders_items.status' => $status, 'pay_at' => time()]);

        return $result;
    }


    /**
     *根据订单no获取orders_items状态
     */
    static function getByNo($order_no, $status = '')
    {
        $queryBuilder = DB::table('orders')
            ->leftJoin('orders_items', 'orders.id', '=', 'orders_items.order_id')
            ->where('orders.order_no', $order_no)
            ->select(
                'orders.order_no',
                'orders.id as orders_id',
                'orders_items.type',
                'orders_items.product_id',
                'orders_items.price',
                'orders_items.num',
                'orders_items.id',
                'orders.uid',
                'orders.zone_id',
                'orders.mobile',
                'orders.realname'
            );

        if ($status) {
            $queryBuilder->where('orders_items.status', $status);
        }

        $items = $queryBuilder->get();

        return $items;
    }


    /**
     *将items转化成实体
     */
    static function toEntity($type, $product_id, $id)
    {
        $arr = ['day' => '天', 'week' => '周', 'month' => '个月', 'year' => '年'];
        $entity = [];
        if ($type == 'vip') {
            $term = Term::getRow(['id' => $product_id]);
            $entity = Vip::detail($term->vip_id, 1, 1);
            $entity->term_name = $term->name;
            $expireTime = DB::table('user_vip')->where('orders_items_id', $id)->lists('end_time');
            asort($expireTime);
            $expireTime = array_map(
                function ($v) {
                    return date('Y-m-d H:i:s', $v);
                },
                $expireTime
            );
            //过期时间
            $entity->expire_time = $expireTime;
            $entity->type = $type;
        }


        if ($type == 'brand') {
            $order_item = \DB::table('orders_items')->where('id', $id)->first();
            $order = \DB::table('orders_items')
                ->leftJoin('orders', 'orders_items.order_id', '=', 'orders.id')
                ->leftJoin('user', 'user.uid', '=', 'orders.uid')
                ->leftJoin('live_brand_goods', 'live_brand_goods.id', '=', 'orders_items.product_id')
                ->select('orders.order_no', 'orders.realname', 'user.username',
                    'orders.amount', 'orders.score_money', 'orders.online_money', 'orders.created_at', 'orders.pay_at')
                ->where('orders.id', $order_item->order_id)->first();
            $order->created_at_format = date('Y-m-d H:i:s', $order->created_at);
            $order->pay_at = date('Y-m-d H:i:s', $order->pay_at);
            $order->score = ($order->score_money) * config('system.score_rate');
            $brand = new Brand();
            $goods = $brand->goods($product_id);
            $goods['detail'] = strip_tags($goods['detail']);
            $entity['order'] = $order;
            $entity['goods'] = $goods;
        }

        if ($type == 'brand_goods') {
            $order_item = \DB::table('orders_items')->where('id', $id)->first();
            $order = \DB::table('orders_items')
                ->leftJoin('orders', 'orders_items.order_id', '=', 'orders.id')
                ->leftJoin('user', 'user.uid', '=', 'orders.uid')
                ->leftJoin('zone', 'zone.id', '=', 'orders.zone_id')
                ->leftJoin('brand_goods', 'brand_goods.id', '=', 'orders_items.product_id')
                ->select('orders.order_no', 'orders.realname', 'orders.mobile', 'zone.name as zone_name',
                    'orders.amount', 'orders.score_money', 'orders.online_money', 'orders.created_at', 'orders.pay_at')
                ->where('orders.id', $order_item->order_id)->first();
            $order->created_at_format = date('Y-m-d H:i:s', $order->created_at);
            $order->pay_at = date('Y-m-d H:i:s', $order->pay_at);
            $order->score = ($order->score_money) * config('system.score_rate');
            $brand = \DB::table('brand_goods')
                ->leftJoin('brand', 'brand.id', '=', 'brand_goods.brand_id')
                ->leftJoin('categorys', 'brand.categorys1_id', '=', 'categorys.id')
                ->select('categorys.name as categorys_name', 'brand.logo', 'brand.id', 'brand.summary', 'brand.name as brand_name')
                ->where('brand_goods.id', $product_id)->first();
            $brand->logo = getImage($brand->logo, 'activity', '', 0);
            $entity['order'] = $order;
            $entity['brand'] = $brand;
        }

        if ($type == 'inspect_invite') {
            $order = \DB::table('orders_items')
                ->leftJoin('orders', 'orders_items.order_id', '=', 'orders.id')
                ->select('orders.order_no')
                ->where('orders_items.id', $id)->first();

            $entity['order'] = $order;
        }

        //todo 其他情况

        return $entity;
    }

    /**
     * 根据类型和关联id获取相关订单信息
     * @param $type 商品类型  vip专版  video_reward点播打赏 live_reward直播打赏 video点播 brand直播品牌预付加盟
     *                       brand_goods品牌商品加盟 news资讯 inspect_invite考察邀请函 score积分 contract合同
     * @param $product_id
     * @param $pays 支付方式 数组形式
     *
     * @return array
     */
    public static function getOrderInfo($type, $product_id, $pays = ['pay'])
    {
        $data = self::with('orders')
            ->where('type', $type)
            ->where('product_id', $product_id)
            ->whereIn('status', $pays) // todo 需求需要在没有支付的时候，也需要返回订单号 zhaoyf 2018-1-31
            ->first();                         // todo 这里设置成一个数组的目的是为了兼容pay和npay两种支付状态
                                               // todo 让程序选择：哪个支付状态有，返回那个状态的订单号
        return $data ? $data : [];
    }

    /**
     * 支付方式转中文
     * @param $item 相关字符串
     * reutrn string
     */
    public static function pay_way($item)
    {
        if ($item == 'weixin') {
            $way = '微信';
        } elseif ($item == 'ali') {
            $way = '支付宝';
        } elseif ($item == 'score') {
            $way = '积分';
        } elseif ($item == 'unionpay') {
            $way = '银联支付';
        } elseif ($item == 'red_packet') {
            $way = '红包支付';
        } else {
            $way = '银行卡转账';
        }
        return $way;
    }


}