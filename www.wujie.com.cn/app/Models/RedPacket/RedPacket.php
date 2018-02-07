<?php namespace App\Models\RedPacket;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\RedPacketAgent;

class RedPacket extends Model
{
    protected  $table =  'red_packet';
    protected $dateFormat = 'U';
    protected $guarded = [];

    public static $_TYPE=[
        '1' => '品牌全场红包',
        '2' => '品牌专属红包',
        '3' => '邀请红包',
        '4' => '奖励红包',
        '5' => '福字红包',
        '6' => '新年开门大吉不定额现金红包',
        '7' => '春节活动不定金额现金红包',
        '8' => '新年活动经纪人答题红包',
    ];

    public function brand(){
        return $this->hasOne(Brand::class , 'id' , 'post_id');
    }


    //显示红包 用的status包装器
    public static function showWhere(){
        $nowTime = time();
        $builder = self::where('status',1)
            ->where(function ($query)use($nowTime){
                $query->where('start_distribute_at',-1);
                $query->orWhere('start_distribute_at','<=',$nowTime);
            })
            ->where(function($query)use($nowTime){
                $query->where('end_distribute_at',-1);
                $query->orWhere('end_distribute_at','>',$nowTime);
            })->where(function ($query){
                $query->where('total', -1)->orWhere(function ($query){
                    $query->whereRaw('total > gives');
                });
            });
        return $builder;
    }






    //根据红包金额，返回红包额度级别

    /**
     * @param $id 红包id
     */
    public static function redPacketLevel($id){
        $levelStr = '';
        $redPacketInfo = self::find($id);
        $amount = intval($redPacketInfo['amount']).'';
        $level = strlen($amount) - 1;
        switch ($level){
            case 2 : $levelStr = '百元'; break;
            case 3 : $levelStr = '千元'; break;
            case 4 : $levelStr = '万元'; break;
            case 6 : $levelStr = '百万元'; break;
            case 7 : $levelStr = '千万元'; break;
            case 8 : $levelStr = '亿元'; break;
        }
        //其他情况返回空
        return $levelStr;
    }

    /*
     * 领取红包时获取红包的失效时间戳
     * 专门写一个方法，是为了防止以后添加其他红包类型，过期计算方法发生变化
     * */
    public static function getExpireTime($id){
        $nowTime = time();
        $expireAt = "";
        $redPacketInfo = self::where('id',$id)->first();

        if($redPacketInfo['expire_at'] == -1){
            $expireAt = '-1';
        }
        else if ($redPacketInfo['expire_type'] == 0){
            //按照固定时间过期
            $expireAt = trim($redPacketInfo['expire_at']);
        }
        else if($redPacketInfo['expire_type'] == 1){
            //根据领取时间点，往后推固定时长后过期
            $expireAt = $nowTime + $redPacketInfo['expire_at'];
        }
        return $expireAt;
    }

    /**
     * author zhaoyf
     *
     * 根据经纪人答题次数返回相应次数额度的通用红包
     *
     * @param   $answer_num     答题次数
     * @param   $agent_id       经纪人ID
     *
     * @return array
     */
    public static function assignAgentAnswerNumReturnRedDatas($answer_num, $agent_id)
    {
        $confirm_data = array();

        $get_result = self::showWhere()
            ->where('type', 8)
            ->orderBy('succ_num', 'desc')
            ->first();

        //对结果进行处理
        if ($get_result) {
            if ($get_result->succ_num == $answer_num) {
                $confirm_data = self::_tissuesNeedAddOfDatas($get_result, $agent_id);
            } elseif ($get_result->succ_num < $answer_num) {
                $confirm_data = self::_tissuesNeedAddOfDatas($get_result, $agent_id);
            } elseif ($get_result->succ_num > $answer_num) {
                $reality_answer_get_red_result = self::showWhere()
                    ->where(['type' => 8, 'succ_num' => $answer_num])->first();

                //对结果进行处理
                if ($reality_answer_get_red_result) {
                    $confirm_data = self::_tissuesNeedAddOfDatas($reality_answer_get_red_result, $agent_id);
                }
            }
        }

        //添加数据，同时返回结果
        return RedPacketAgent::instance()->addDatas($confirm_data, 'create');
    }

    /**
     * 组织需要添加给经纪人的红包数据
     *
     * @param $data_source     数据来源 object | array
     * @param $agent_id        经纪人ID
     *
     * @return bool
     */
    private static function _tissuesNeedAddOfDatas($data_source, $agent_id, $source = 0)
    {
        $confirm_data = array();

        //对传递数据来源进行判断
        if (is_object($data_source)) {
            $confirm_data = [
                'agent_id'      => $agent_id,
                'red_packet_id' => $data_source->id,
                'expire_at'     => time() + $data_source->expire_at,
                'created_at'    => strtotime($data_source->created_at),
                'updated_at'    => time(),
                'type'          => $data_source->type,
                'amount'        => $data_source->amount,
                'source'        => $source
            ];
        } elseif (is_array($data_source)) {
            $confirm_data = [
                'agent_id'      => $agent_id,
                'red_packet_id' => $data_source['id'],
                'expire_at'     => time() + $data_source['expire_at'],
                'created_at'    => strtotime($data_source['created_at']),
                'updated_at'    => time(),
                'type'          => $data_source['type'],
                'amount'        => $data_source['amount'],
                'source'        => $source
            ];
        }

        return $confirm_data;
    }

}
