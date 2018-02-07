<?php namespace App\Services\Version\Agent\Inspector;

use App\Models\Agent\CommonEvents\Events;

class _v010100 extends _v010002
{
    /**
     * author zhaoyf
     *
     * 发送通知
     *
     * @param $param
     * @return array|string
     */
    public function postMessageBack($param, $datas = [])
    {
        //获取通知数据
        $confirm_result = Events::instance()->sendInform();

        //将多维数组合并到一个数组里
        $new_array_data = array();
        foreach ($confirm_result as $key => $vls) {
            foreach ($vls as $keys => $vs) {
                $new_array_data[] = $vs;
            }
        }

        //兼容继承数据
        if ($datas) {
            $confirm_data = array_merge($new_array_data, $datas);
        } else {
            $confirm_data = $new_array_data;
        }

        //继承父类
        $message_result = parent::postMessageBack($param, $confirm_data);

        //返回结果数据信息
        return ['message' => $message_result['message'], 'status' => true];
    }
}