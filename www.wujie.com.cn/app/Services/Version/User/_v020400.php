<?php

namespace App\Services\Version\User;

use App\Http\Requests\Order\OrderSignRequest;
use App\Models\Agent\Agent;
use App\Models\Agent\AgentBrand;
use App\Models\Agent\AgentCurrencyLog;
use App\Models\Agent\AgentCustomer;
use App\Models\User\Browse;
use App\Services\Version\VersionSelect;
use App\Models\Activity\Entity as Activity;
use App\Models\Activity\Sign;
use App\Models\ScoreLog;
use App\Models\User\Ticket;
use Validator;
use App\Models\Orders\Entity as Orders;
use App\Models\Brand\Entity as Brand;
use App\Models\User\Entity as User;
use \DB;

class _v020400 extends VersionSelect
{

    /**
     * 作用:我的意向品牌
     * 参数:$data
     *
     * 返回值:
     */
    public function postIntentBrands($data)
    {
        //获取和该用户相关的品牌id
        $brand_ids = $this->intentBrandIds($data['uid'], $data['page'], $data['page_size'], $data['keywords']);
        $brand = new \App\Services\Brand;
        //获取品牌
        $brands = $brand->brandList($brand_ids, 1);

        return $brands;
    }

    /**
     * 作用:我的意向品牌
     * 参数:$data
     *
     * 返回值:
     */
    public function intentBrandIds($uid, $page, $page_size, $keywords)
    {
        //1.用户已购买的品牌商品
        $order_goods = DB::table('orders_items')
            ->leftJoin('live_brand_goods', 'live_brand_goods.id', '=', 'orders_items.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'orders_items.order_id')
            ->leftJoin('brand', 'live_brand_goods.brand_id', '=', 'brand.id')
            ->where(
                function ($query) use ($keywords) {
                    if (isset($keywords) && $keywords != '') {
                        $query->where('brand.name', 'like', '%' . $keywords . '%');
                    }
                }
            )
            ->where('orders_items.type', 'brand')->where('orders_items.status', 'pay')
            ->where('orders.uid', $uid)
            ->select('orders_items.updated_at', 'live_brand_goods.brand_id');

        //2.用户已加盟品牌商品  合并
        $order_brand = DB::table('orders_items')
            ->leftJoin('brand_goods', 'brand_goods.id', '=', 'orders_items.product_id')
            ->leftJoin('orders', 'orders.id', '=', 'orders_items.order_id')
            ->leftJoin('brand', 'brand_goods.brand_id', '=', 'brand.id')
            ->where(
                function ($query) use ($keywords) {
                    if (isset($keywords) && $keywords != '') {
                        $query->where('brand.name', 'like', '%' . $keywords . '%');
                    }
                }
            )
            ->where('orders_items.type', 'brand_goods')
            ->where('orders_items.status', 'pay')
            ->where('orders.uid', $uid)
            ->select('orders_items.updated_at', 'brand_goods.brand_id')
            ->union($order_goods)
            ->orderBy('updated_at', 'desc')
            ->get();
        $order_brand_ids = array_unique(getValueFromDb($order_brand, 'brand_id'));

        //3.留言和提问
        $intent_brand = DB::table('brand_consult')
            ->leftJoin('brand', 'brand_consult.brand_id', '=', 'brand.id')
            ->where(
                function ($query) use ($keywords) {
                    if (isset($keywords) && $keywords != '') {
                        $query->where('brand.name', 'like', '%' . $keywords . '%');
                    }
                }
            )
            ->where('brand_consult.uid', $uid)
            ->whereIn('brand_consult.type', ['quiz', 'intent'])
            ->whereNotIn('brand_consult.brand_id', $order_brand_ids)
            ->orderBy('brand_consult.created_at', 'desc')
            ->select('brand_consult.brand_id')->get();
        $intent_brand_ids = array_unique(getValueFromDb($intent_brand, 'brand_id'));

        //4.参加过品牌推介会
        $ticket_brands = DB::table('user_ticket')
            ->leftJoin('activity_brand', 'activity_brand.activity_id', '=', 'user_ticket.activity_id')
            ->leftJoin('brand', 'activity_brand.brand_id', '=', 'brand.id')
            ->where(
                function ($query) use ($keywords) {
                    if (isset($keywords) && $keywords != '') {
                        $query->where('brand.name', 'like', '%' . $keywords . '%');
                    }
                }
            )
            ->where('user_ticket.status', 1)
//            ->where('user_ticket.type', 2)
            ->where('user_ticket.uid', $uid)
            ->whereNotIn('activity_brand.brand_id', $order_brand_ids)
            ->whereNotIn('activity_brand.brand_id', $intent_brand_ids)
            ->orderBy('user_ticket.updated_at','desc')
            ->select('activity_brand.brand_id')
            ->get();

        $ticket_brands_ids = array_unique(getValueFromDb($ticket_brands, 'brand_id'));
        $brand_ids = array_merge($order_brand_ids, $intent_brand_ids, $ticket_brands_ids);

        return array_slice($brand_ids, ($page - 1) * $page_size, $page_size);
    }

    /**
     * 作用:我的品牌浏览记录(30天内)
     * 参数:$data
     *
     * 返回值:
     */
    public function postBrandsBrowse($data)
    {
        if ($data['type'] == 'brand') {
            //获取30天内浏览的品牌id 去重
            $brand_ids = \DB::table('user_browse')
                ->leftJoin('brand', 'brand.id', '=', 'user_browse.relation_id')
                ->where('user_browse.uid', $data['uid'])
                ->where('user_browse.relation', 'brand')
                ->where(
                    function ($query) use ($data) {
                        if (isset($data['keywords']) && $data['keywords'] != '') {
                            $query->where('brand.name', 'like', '%' . $data['keywords'] . '%');
                        }
                    }
                )
                ->where('user_browse.created_at', '>', (time() - 3600 * 24 * 30))
                ->orderBy('user_browse.created_at', 'desc')
                ->lists('user_browse.relation_id');
            $brand_ids = array_unique($brand_ids);
            $brand_ids = array_slice($brand_ids, ($data['page'] - 1) * $data['page_size'], $data['page_size']);
            $brand = new \App\Services\Brand;
            //获取品牌
            $brands = $brand->brandList($brand_ids);

            return $brands;
        }
    }

    /**
     * 作用:添加浏览记录
     * 参数:$data
     *
     * 返回值:
     */
    public function postAddBrowse($data)
    {
        $browse = Browse::create(['uid' => $data['uid'], 'relation' => $data['relation'], 'relation_id' => $data['relation_id']]);

        //经纪人团队发展
        $agent_id = AgentCustomer::where('uid', $data['uid'])->whereIn('source', [1,6])->value('agent_id');

        $user = User::where('uid', $data['uid'])->first();
        if(!$user){
            return ['data' => '数据异常', 'status' => false];
        }

        //至少存在两天点击品牌
        $days = Browse::select(DB::raw('from_unixtime(created_at,"%Y-%m-%d") as day'))
            ->where('relation', 'brand')->where('uid', $data['uid'])->groupBy('day')->get()->toArray();


        //且至少有3次
        $count = Browse::where('relation', 'brand')->where('uid', $data['uid'])->count();

        $agent = Agent::find($agent_id);

        //他的邀请经纪人至少要代理成功一个品牌，该客户前两天每天都要浏览品牌，且总共至少要浏览三次以上
        if($agent_id &&count($days)>=2&& $count>=3){
            AgentCurrencyLog::addCurrency($agent_id, 30, 10, $data['uid'], 1);
            $agentBrands = AgentBrand::where('agent_id', $agent_id)->where('status',4)->count();
            if($agentBrands>=1 && isset($agent->pAgent->id)){
                 AgentCurrencyLog::addCurrency($agent->pAgent->id, 80, 9, $agent_id, 1);
            }
        }


        if (is_object($browse)) {
            return ['data' => $browse, 'status' => true];
        } else {
            return ['data' => '创建失败', 'status' => false];
        }
    }




    /**
     * 作用:分享次数加1
     * 参数:$data
     *
     * 返回值:
     */
    public function postAddShareCount($data)
    {
        if($data['share_type']=='activity'){
            $res = Activity::where(['id' => $data['post_id']])->increment('share_num', 1);
            if($res){
                return ['data' => '分享次数新增成功', 'status' => true];
            }else{
                return ['data' => '分享次数新增失败', 'status' => false];
            }
        }

        // 其他情况 todo

    }


}