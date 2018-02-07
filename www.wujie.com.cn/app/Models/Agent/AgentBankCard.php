<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentBankCard extends Model
{
    protected $table    = 'agent_bank_card';
    //protected $fillable = ['agent_id','bank_name','card_no', 'card_type'];

    public $timestamps  = false;

    const TYPE_LIST     = 'list';       //显示列表
    const TYPE_DELETE   = 'delete';     //指定删除
    const NUMBER_0      = 0;            //数字 0
    const NUMBER_1      = 1;            //数字 1

    public static $instance = null;

    public static function instances()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * author zhaoyf
     *
     * 根据指定类型执行不同的操作
     *
     * @param $agent_id 经纪人ID
     * @param $type     类型（list, delete）
     * @param $bank_id  银行卡ID（类型等于 delete 时需要）array
     *
     * @return list | bool
     */
    public function gainDifferentTypeDatas($agent_id, $type, $bank_id = null)
    {
        if ($type == self::TYPE_LIST) {
            $result = self::where([
                'agent_id'  => $agent_id,
                'is_delete' => self::NUMBER_0
            ])->get();
        } elseif ($type == self::TYPE_DELETE) {
           $result = self::where('agent_id', $agent_id)
               ->whereIn('id', $bank_id)
               ->update(['is_delete' => self::NUMBER_1]);
        }

        //对结果进行处理
        if (is_numeric($result)) {
            if ($result != self::NUMBER_0) {
                return self::NUMBER_1;
            } else {
                return self::NUMBER_0;
            }
        } elseif (is_object($result)) {

            $return_result = array();

            if ( !empty($result->toArray()) ) {

                foreach ($result as $key => $vls) {
                    $return_result[] = [
                        'id'        => $vls->id,
                        'agent_id'  => $vls->agent_id,
                        'bank_name' => $vls->bank_name,
                        'card_no'   => $vls->card_no,
                    ];
                }
            }

            return $return_result;
        }
    }
}