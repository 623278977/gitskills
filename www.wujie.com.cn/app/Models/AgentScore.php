<?php namespace App\Models;

use App\Models\Comment\Entity as Comment;
use App\Models\Brand\Entity as Brand;
use App\Models\Zone\Entity as Zone;
use App\Models\Agent\Agent;
use Illuminate\Database\Eloquent\Model;

class AgentScore extends Model
{
    protected $table = 'agent_score';
    public static $instance = null;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 品牌等级
     * 1：差评
     * 2：中评
     * 3：好评
     */
    public static $brandLevel = [
        '0' => '',
        '1' => '差评',
        '2' => '中评',
        '3' => '好评'
    ];

    /**
     * 经济人性别
     * 0： 女
     * 1： 男
     * -1: 不明
     */
    public static $AgentGender = [
        '0'  => '女',
        '1'  => '男',
        '-1' => '不明'
    ];

    /**
     * 关联：评分（品牌评分：comment）
     */
    public function hasOneComment()
    {
        return $this->hasOne(Comment::class, 'post_id', 'brand_id');
    }

    /**
     * 关联：品牌
     */
    public function hasOneBrand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    /**
     * 关联：经纪人
     */
    public function hasOneAgent()
    {
        return $this->hasOne(\App\Models\Agent\Agent::class, 'id', 'agent_id');
    }

    /**
     * 对经纪人和品牌评分的数据返回
     * @param $param
     *
     * @return data_gather|array
     */
    public function AgentBrandScore($param)
    {
       //查询数据集合
       $result_gather = self::with(['hasOneBrand' => function($query) { //查询产品信息
           $query->select('id', 'name', 'logo');
       }, 'hasOneComment' => function($query) use($param) {                         //查询评论信息
           $query->where('type', 'brand')
                 ->where('uid', $param['customer_id'] )
                 ->select('id', 'post_id', 'created_at', 'content', 'level');
       }, 'hasOneComment.hasManyCommentImages' => function($query) {
           $query->select('id' ,'comment_id', 'url');
       }, 'hasOneAgent'   => function($query) {                         //查询经纪人信息
           $query->select('id', 'is_public_realname', 'nickname', 'realname', 'gender', 'avatar', 'zone_id', 'agent_level_id');
       }, 'hasOneAgent.hasOneZone' => function($query) {                //查询经纪人地区信息
           $query->select('id', 'upid', 'name');
       }, 'hasOneAgent.hasOneAgentLevel' => function($query) {          //查询经纪人级别信息
           $query->select('id', 'name');
       }])
           ->where('customer_id', $param['customer_id'])
           ->where('agent_id',    $param['agent_id'])
           ->where('brand_id',    $param['brand_id'])
           ->first();

       if (empty($result_gather))  return ['message' => '没有查询到相关信息',  'status' => false];

        //组合数据
        $confirm_data = [
            'brand_id'          => $result_gather['hasOneBrand']['id'],
            'brand_title'       => $result_gather['hasOneBrand']['name'],
            'brand_logo'        => getImage($result_gather['hasOneBrand']['logo']),
            'brand_created_at'  => $result_gather['hasOneComment']['created_at']->getTimestamp(),
            'brand_comment_content' => $result_gather['hasOneComment']['content'],
            'brand_comment_imags'   => $result_gather['hasOneComment']['hasManyCommentImages'] ? $result_gather['hasOneComment']['hasManyCommentImages'] : [],
            'brand_level'       => self::$brandLevel[$result_gather['hasOneComment']['level']] == "" ?  '好评' : self::$brandLevel[$result_gather['hasOneComment']['level']],
            'agent_id'          => $result_gather['hasOneAgent']['id'],
            'is_public_realname' => $result_gather['hasOneAgent']['is_public_realname'],
            'agent_realname'    => $result_gather['hasOneAgent']['realname'],
            'agent_nickname'    => $result_gather['hasOneAgent']['nickname'],
            'agent_avatar'      => !empty($result_gather['hasOneAgent']['avatar']) ?  getImage($result_gather['hasOneAgent']['avatar'], 'avatar', '') : getImage(''),
            'agent_level'       => $result_gather['hasOneAgent']['hasOneAgentLevel']['name'],
            'agent_level_id'    => $result_gather['hasOneAgent']['hasOneAgentLevel']['id'],
            'agent_gender'      => self::$AgentGender[$result_gather['hasOneAgent']['gender']],
            'agent_city'        => Zone::pidNames([$result_gather['hasOneAgent']['hasOneZone']['id']]),
            'service_score'     => $result_gather['service_score'],
            'ability_score'     => $result_gather['ability_score'],
            'timely_score'      => $result_gather['timely_score'],
        ];

        return ['message' => $confirm_data, 'status' => true];
    }

    /**
     * 初始化经纪人和品牌的数据返回
     * @param $param
     *
     * @return data_gather|array
     */
    public function AgentInfoInitializeShow($param)
    {
        //查询数据集合
        $result_gather = Agent::with(['hasOneZone' => function($query) {
            $query->select('id', 'upid', 'name');      //查询经纪人地区信息
        }, 'hasOneAgentLevel' => function($query) {    //查询经纪人级别信息
            $query->select('id', 'name');
        }])
         ->where('id',  $param['agent_id'])
         ->select('id', 'is_public_realname', 'nickname', 'realname', 'gender', 'avatar', 'zone_id', 'agent_level_id')
         ->first();

        //判断投资人ID是否存在
        if (!is_numeric($param['uid']) || empty($param['uid'])) {
            return ['message' => '缺少用户ID：uid', 'status' => false];
        }

        //对查询的结果进行处理
        if (empty($result_gather)) {
            return ['message' => '没有查询经纪人的信息', 'status' => false];
        }

        //获取品牌数据
         $brand_result = Brand::where('id', $param['brand_id'])->first();
         $is_comment_result = AgentScore::where('agent_id', $param['agent_id'])
            ->where('customer_id', $param['uid'])
            ->where('brand_id', $param['brand_id'])
            ->first();

        //组合数据
        $confirm_data = [
            'agent_id'        => $result_gather['id'],
            'is_public_realname' => $result_gather['is_public_realname'],
            'agent_realname'  => $result_gather['realname'],
            'agent_nickname'  => $result_gather['nickname'],
            'agent_level_id'  => $result_gather['hasOneAgentLevel']['id'],
            'agent_avatar'    => getImage($result_gather['avatar'], 'avatar', ''),
            'agent_gender'    => self::$AgentGender[$result_gather['gender']],
            'agent_level'     => $result_gather['hasOneAgentLevel']['name'],
            'agent_city'      => Zone::pidNames([$result_gather['hasOneZone']['id']]),
            'brand_id'        => $brand_result['id'],
            'brand_title'     => $brand_result['name'],
            'brand_logo'      => getImage($brand_result['logo']),
            'is_comment'      => $is_comment_result ?  1 : 0,
        ];

        return ['message' => $confirm_data, 'status' => true];
    }
}

