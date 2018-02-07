<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Bus\Queueable;
use App\Jobs\SendRemindSMS;

class SmsController extends Controller
{
    use DispatchesJobs, Queueable;

    /**
     * 发送短信
     * @param $strMobile
     * @param $content
     * @param $type
     * @param string $sendType
     * @param int $delay
     */
    public function sendSMS($strMobile, $content,$type,$sendType = '',$delay = 0)
    {
        $param = [
            'strMobile' => $strMobile,
            'content' => $content['name'],
            'type' => $type,
            'tag' => $content['tag'],
            'sendType' => $sendType,
        ];
        $job = (new SendRemindSMS($param))->delay($delay);
        $this->dispatch($job);
    }

    /**
     * @param $param
     */
    public function createSMS($param)
    {
        $is_md5 = array_get($param, 'is_md5', false);
//        SendSMS($param['strMobile'],$param['content'],$param['type'],$param['sendType']);
        SendTemplateSMS($param['content'],$param['strMobile'],$param['type'],$param['tag'], 86, 'wjsq', $is_md5);
    }

}
