<?php

namespace App\Services\GeTui;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/*
 * 个推处理
 */
include_once app_path('Http/Libs/igetui_sdk/IGt.Push.php');

abstract class GeTui {

    protected $config;

    /*
     * 作用：初始化处理消息基本信息
     * 参数：无
     * 返回值：无
     */
    public function __construct($is_agent) {
        if(!$is_agent){
            $this->config = config('system.igexin');
        }else{
            $this->config = config('system.agent_igexin');
        }
    }

    /*
     * 作用：发送点击打开应用模板消息
     * 参数：$title     通知栏标题  string
     *      $text      通知栏内容  string
     *      $content   透传内容，供APP接收处理 string
     *      $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
     *      $users     要发的用户或用户列表，为空时发送全部用户
     * 返回值：array|string|false
     */
    public static function sendNotification($title, $text, $content, $duration, $users = null, $is_agent=false) {
        $_template = new Template($is_agent);
        $notification = $_template->notification($title, $text, $content, $duration);//通知
        $transmission = $_template->transmission($title, $text, $content, $duration);//透传
        //设置apn形式
        $alertMsg = $_template->getAlertMsg($text, '', '', [], '', $title, '', []);
        $_template->setAPN($transmission, $alertMsg, ['content' => $content]);
        $message = new Message(3600000, 0, $is_agent);
        if (is_null($users)) {//全推
            $result = $message->app($notification, [], [], ['ANDROID']);
            $android = isset($result['result']) && $result['result'] == 'ok' ? $result['contentId'] : false;
            $result = $message->app($transmission, [], [], ['IOS']);
            $ios = isset($result['result']) && $result['result'] == 'ok' ? $result['contentId'] : false;
            return compact('android', 'ios');
        } elseif ($users instanceof Model) {//指定用户推送
            if ($users['platform'] == 'ios') { //推的是通知却要使用透传的模板？
                return self::send($message, $transmission, $users);
            }
            return self::send($message, $notification, $users);
        } elseif ($users instanceof Collection) {//指定用户列表推送
            $users = $users->groupBy('platform');
            if ($users->has('android'))
                $android = last(self::send($message, $notification, $users->get('android')));
            if ($users->has('ios'))
                $ios = last(self::send($message, $transmission, $users->get('ios')));
            return compact('android', 'ios');
        }
    }

    /*
     * 作用：发送透传消息
     * 参数：$content   透传内容，供APP接收处理 string
     *      $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
     *      $users     要发的用户或用户列表，为空时发送全部用户
     * 返回值：array|string|false
     */
    public static function sendTransmission($content, $duration, $users = null, $is_agent = false) {
        $_template = new Template($is_agent);
        $template = $_template->transmission($content, $duration);
        $alertMsg = $_template->getAlertMsg('', '', '', [], '', '', '', []);
        $_template->setAPN($template, $alertMsg, ['content' => $content], 0, 'com.gexin.ios.silence', true);
        $message = new Message(3600000, 0, $is_agent);
        return self::send($message, $template, $users);
    }


    /*
     * 作用：发送透传消息和通知
     * 参数：$content   透传内容，供APP接收处理 string
     *      $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
     *      $users     要发的用户或用户列表，为空时发送全部用户
     * 返回值：array|string|false
     */
    public static function sendTransAndNotice($content, $duration, $users = null, $is_agent = false) {
        $_template = new Template($is_agent);
        $template = $_template->transmission($content, $duration);

        //判断是否需要发离线消息
        $temp = json_decode($template->transmissionContent);
        //取出相关配置通知信息
        if ($temp->type){
            $parameters = TransmissonType::templateType($temp);
            $body = trans('transmisson.' . $temp->type, $parameters['res']);
            $content = $parameters['new_item'];
        }else{
            $body = '';
            $content = [];
        }

        //对内容进行处理
        $content = json_encode($content);

        $alertMsg = $_template->getAlertMsg($body, '', '', [], '', '', '', []);
        $_template->setAPN($template, $alertMsg, ['content' => $content], 0, 'com.gexin.ios.silence', true);

        $message = new Message(0.5, 0, $is_agent);//离线时间1秒
        return self::send($message, $template, $users, 1);
    }

    /*
     * 作用：发送模板消息
     * 参数：$message   透传内容，供APP接收处理 string
     *      $template  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
     *      $users     要发的用户或用户列表，为空时发送全部用户 Illuminate\Database\Eloquent\Model|Illuminate\Database\Eloquent\Collection|null
     *      $is_trans 是否是透传，0是通知 1是透传
     * 返回值：array|string|false
     */
    public static function send(Message $message, \IGtBaseTemplate $template, $users = null, $is_trans = 0) {
        if (is_null($users)) {//全推
            $result = $message->app($template);
            return isset($result['result']) && $result['result'] == 'ok' ? $result['contentId'] : false;
        } elseif ($users instanceof Model) {//指定用户推送
//            if (!static::filter($users)) {
//                return false;
//            }
            $result = $message->single($template, $users, null, $is_trans);


            return $result;
//            return isset($result['result']) && $result['result'] == 'ok' ? $result['taskId'] : false;
        } elseif ($users instanceof Collection) {//指定用户列表推送
            $result = $message->lists($template, $users->filter(function($user) {
                        return static::filter($user);
                    })->groupBy('platform'));

            return array_map(function($result) {
                return isset($result['result']) && $result['result'] == 'ok' ? $result['contentId'] : false;
            }, $result);
        }
    }

    /*
     * 作用：过滤用户处理
     * 参数：$user 要过滤的用户 Illuminate\Database\Eloquent\Model
     * 返回值：boolean
     */
    private static function filter(Model $user) {

        return in_array($user['platform'], ['ios', 'android'], true) && !empty($user['identifier']);
    }

}
