<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Brand;

use App\Models\Agent\Agent;
use App\Models\User\Entity as User;
use Illuminate\Database\Eloquent\Model;
use \DB;
use App\Models\Categorys;
class Enter extends Model
{

    protected $dateFormat = 'U';

    protected $table = 'brand_enter';

    //黑名单
    protected $guarded = [];

    public static $_RULES = [//验证规则
        'uid' =>'required|integer',
        'brand_name'=>'required',
        'categorys1_id'=>'required',
        'realname'=>'required',
        'duties'=>'',
        'mobile'=>'required|numeric',
        'introduce'  => '',
    ];
    public static $_MESSAGES = [//验证字段说明
        'uid' => '经纪人',
        'brand_name'=> '品牌名称',
        'categorys1_id'=>'所属行业',
        'realname'=>'联系人',
        'duties'=>'联系人职位',
        'mobile'=>'联系人电话',
    ];

    public static  $_STATUS = [
        'pending'   => '等待中',
        'follow'    => 'KA已跟进',
        'success'   => '已上架',
        'sediment'  => '沉淀',
    ];

    //获取行业名称
    public static function getCategory($id)
    {
        $category= Categorys::where('id', $id)->first();
        return $category->name;
    }


    //大分类
    public function categorys1()
    {
        return $this->hasOne('App\Models\Categorys', 'id', 'categorys1_id');
    }



    //品牌
    public function brand()
    {
        return $this->hasOne('App\Models\Brand\Entity', 'id', 'brand_id');
    }


    /**
     * 入驻列表条件筛选
     * @User yaokai
     * @param $builder
     * @param array $param
     * @return mixed
     */
    static public function enterList($builder, array $param = [])
    {
        //数据来源：wjsq无界商圈，agent经纪人
        if (isset($param['from']) && !empty($param['from'])) {
            $builder->where('from',$param['from']);
        }

        //提交开始时间
        if (isset($param['created_start']) && !empty($param['created_start'])) {
            $builder->where('created_at','>=',strtotime($param['created_start']));
        }

        //提交结束时间
        if (isset($param['created_end']) && !empty($param['created_end'])) {
            $builder->where('created_at','<=',strtotime($param['created_end']));
        }

        //状态
        if (isset($param['status']) && !empty($param['status'])) {
            $builder->where('status', $param['status']);
        }

        //行业分类
        if (isset($param['categorys1_id']) && !empty($param['categorys1_id'])) {
            $builder->where('categorys1_id', $param['categorys1_id']);
        }


        //默认id排序
        $builder->orderBy('id', 'desc');

        return $builder;
    }


    /**
     * 列表格式化数据  --数据中心版
     * @param $result 要格式化的数据
     * @param $input 带入的参数
     * @param $events
     */
    static public function formatList($result, $input = [])
    {
        $data = [];
        foreach ($result['data'] as $k => $v) {
            $data[$k]['id']             = $v['id'];//id
            $data[$k]['uid']            = $v['uid'];//经纪人id或投资人id
            $data[$k]['mobile']         = $v['mobile'];//联系号码
            $data[$k]['non_reversible'] = $v['non_reversible'];//联系号码
            $data[$k]['realname']       = $v['realname'];//联系人姓名
            $data[$k]['brand_name']     = $v['brand_name'];//品牌名称
            $data[$k]['categorys_name'] = $v['categorys1']['name'];//行业分类
            $data[$k]['status']         = self::$_STATUS[$v['status']];//处理状态
            $data[$k]['introduce']      = $v['introduce'];//介绍
            $data[$k]['created_at']     = date('Y-m-d H:i:s',$v['created_at']);//添加时间
            $data[$k]['updated_at']     = $v['updated_at'];//修改时间
            $data[$k]['from']           = $v['from']=='wjsq'?'商圈C端APP':'商圈经纪人APP';//来源
            $data[$k]['duties']         = $v['duties']?:'-';//职务
            $data[$k]['remark']         = $v['remark']?:'-';//备注
            $data[$k]['submit_account'] = self::getSubmitName($v['uid'],$v['from']);//提交账户
        }

        return $data;
    }

    /**
     * 添加账户处理  --数据中心版  暂不处理
     * @User yaokai
     * @param $uid
     * @param string $from
     * @return string
     */
    public static function getSubmitName($uid, $from = 'wjsq')
    {
        if ($from == 'wjsq') {
            $user_agent = User::where('uid', $uid)->first();
        } else {
            $user_agent = Agent::where('id',$uid)->first();
        }

        return  $user_agent->nickname . '(' . $user_agent->username . ')';

    }


}