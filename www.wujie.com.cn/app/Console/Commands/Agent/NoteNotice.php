<?php namespace App\Console\Commands\Agent;

use App\Models\Agent\Contract;
use Illuminate\Console\Command;
use App\Models\Agent\Invitation;

class NoteNotice extends Command
{
    protected $signature = 'Agent:NoteNotice';

    private $user_data   = array();  //记录投资人ID

    //离直播开播还有5天左右（如果使用了红包抵扣邀请函，还未加盟品牌下）发送通知
    public function handle()
    {
        $this->gainRedPayinviteDatas();
    }

    //获取邀请函表使用红包支付定金的数据信息
    public function gainRedPayinviteDatas()
    {
        $result = Invitation::with('hasOneUsers', 'hasOneStore.hasOneBrand')
            ->where([
                'type'           => 2,      //考察邀请函类型
                'status'         => 1,      //已接受状态
                'use_red_packet' => 1,      //红包抵扣
            ])->get();

        //对结果进行处理
        if ($result) {
            foreach ($result as $key => $vls) {

                $day = ceil((strtotime("+1 months", $vls->pay_time) - time()) / 86400);

                //在五天之间发送短信
                if ($day > 4 && $day < 6) {

                    //去合同表里获取是否签订了合同（以付过首付为准）
                    $gain_result = Contract::where([
                        'status'    => 1,
                        'uid'       => $vls->hasOneUsers->uid,
                        'brand_id'  => $vls->hasOneStore->hasOneBrand->id
                    ])->first();

                    if (is_null($gain_result)) {
                        $this->_sendNoteInforms([
                            'uid'        => $vls->hasOneUsers->uid,
                            'username'   => $vls->hasONeUsers->username,
                            'brand_name' => $vls->hasOneStore->hasOneBrand->name
                        ]);
                    }
                }
            }
        }
    }

    /**
     * 发送短信通知
     *
     * @param   param [
     *  'uid'         => '投资人ID号' int ,
     *  'username'    => '投资人手机号' int ,
     *  'brand_name'  => '品牌名称' string
     * ]
     *
     */
    private function _sendNoteInforms(array $param)
    {
        //发送短信提示信息
        SendTemplateSMS(
            'time_soon_info_notice',                    //短信提示模板
            $param['username'],                         //投资人手机号
            'red_time_soon_notice_customer',            //短信提示类型
            ['brand_names' => $param['brand_name']]     //品牌名称
        );
    }
}