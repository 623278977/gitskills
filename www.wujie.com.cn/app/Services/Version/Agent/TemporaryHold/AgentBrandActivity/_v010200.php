<?php namespace App\Services\Version\Agent\TemporaryHold\AgentBrandActivity;

use App\Models\Agent\AgentCustomerLog;
use App\Services\Version\VersionSelect;
use Illuminate\Support\Str;

class _v010200 extends VersionSelect
{
    /**
     * author zhaoyf
     *
     * 壹Q鲜|台湾奶茶黑马品牌——临时活动
     * 获取所有加盟该品牌的所有投资人
     */
    public function postTemporaryBrandActivitys($param)
    {
        $confirm_result = array();

        $result = AgentCustomerLog::with(['hasOneBrand' => function($query) {
             $query->where('name', 'like', '%壹Q鲜%');
        }, 'user' => function($query) {
             $query->where('status', 1)->select('uid', 'nickname', 'realname');
        }])
         ->where(['action' => 11, 'is_delete' => 0])
         ->limit(10)
         ->select('brand_id', 'uid', 'agent_id')
         ->get();

        //对结果进行处理
        if ($result) {
            foreach ($result as $key => $vls) {
               if (!is_null($vls->hasOneBrand) && !is_null($vls->user)) {
                    $confirm_result[] = [
                        'user_id'    => $vls->user->uid,
                        'user_name'  => Str::limit($vls->user->nickname, 2, '**'),
                        'brand_logo' => getImage($vls->hasOneBrand->logo, 'logo', ''),
                    ];
               }
            }
        }

        //获取后台添加的假数据
        $fake_data_result = \DB::table('temporary_brand_activity')
                            ->where('status', 1)->get();

        //对结果进行处理
        if ($fake_data_result) {
            foreach ($fake_data_result as $key => $vls) {
                $confirm_result[] = [
                    'user_id'    => $vls->uid,
                    'user_name'  => Str::limit($vls->user_name, 2, '**'),
                    'brand_logo' => getImage($vls->brand_logo, 'logo', '')
                ];
            }
        }

        //返回结果
        return ['message' => $confirm_result, 'status' => true];
    }
}