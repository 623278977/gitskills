<?php

/*
 * 个推消息处理
 * 流程梳理：
 * 
 * 1.第三方应用集成个推SDK，个推SDK运行后获取CID返回给第三方应用，由第三方应用保存至其应用服务器；
 * 
 * 2.第三方应用服务器调用推送API进行消息推送，个推SDK将接收到的推送消息回调给App进行处理。
 */

namespace App\Services\GeTui;

use App\Models\User\UserFondCate;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\Entity as User;
use App\Models\Agent\Agent;
class Message extends GeTui
{

    protected $igt;
    protected $isOffline;
    protected $offlineExpireTime;
    protected $pushNetWork;

    /*
     * 作用：初始化处理消息基本信息
     * 参数：$offlineExpireTime   离线时间，离线允许时长，小于1为禁止离线收到信息
     *      $pushNetWork         设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
     * 返回值：无
     */
    public function __construct($offlineExpireTime = 3600000/* 3600 * 12 * 1000 */, $pushNetWork = 0, $is_agent=false)
    {
        parent::__construct($is_agent);
        $this->isOffline = true;//是否离线 包括通知
        $this->offlineExpireTime = $offlineExpireTime;//离线时间
        $this->pushNetWork = $pushNetWork;
        $this->igt = new \IGeTui($this->config['host'], $this->config['app_key'], $this->config['master_secret']);
    }

    /*
     * 作用：推送单个用户
     * 参数：$template 需要推送的模板（支持：Link，Notification，NotyPopLoad，Transmission等模板）  IGtBaseTemplate
     *      $user      需要推送的用户ID或别名  Illuminate\Database\Eloquent\Model
     *      $alias     是否为用户别名推送，否为用户ID推送
     *      $is_trans  1是透传 0是通知
     * 返回值：array(sdk,apn) 如果模板中设置了APN则会返回APN结果，sdk为个推推送结果。
     */
    public function single(\IGtBaseTemplate $template, Model $user, $alias = null, $is_trans=1)
    {
        //定义"SingleMessage"
        $message = $this->createMessage(\IGtSingleMessage::class, $template);

        //推送通知 如果是2.8之后的版本就用别名
        if($user instanceof User && (substr($user->version, 3)>='20800')){
            $alias = 'c'.$user->uid;
        }

        if($user instanceof Agent){
            $alias = 'a'.$user->id;
        }


        //如果是2.8之前的版本，只能通过apn推，因为ios之前没有绑定别名，cid存了devicetoken
        if ($user['platform'] == 'ios' && !$alias && !$is_trans ) {//使用ios 专用APN方式，发送
            if (empty($template->pushInfo) || empty($user['identifier'])) {//使用ios 专用APN方式，发送
                return false;
            }
            return $this->igt->pushAPNMessageToSingle($this->config['app_id'], $user['identifier'], $message);
        }

        //接收方
        $target = $this->createTarget($user, $alias);
        try {
            return $this->igt->pushMessageToSingle($message, $target);
        } catch (\RequestException $e) {
            $requstId = $e->getRequestId();
            //失败时重发
            return $this->igt->pushMessageToSingle($message, $target, $requstId);
        }
    }

    /*
     * 作用：推送多个用户列表
     * 参数：$template 需要推送的模板（支持：Link，Notification，NotyPopLoad，Transmission等模板）  IGtBaseTemplate
     *      $users     要推送的用户列表，['ios'=>cid|alias,'android'=>cid|alias]，用户ID与用户别名二选一 Illuminate\Database\Eloquent\Collection
     *      $alias     是否为用户别名推送，否为用户ID推送
     * 返回值：array('android'=>sdk,'ios'=>apn) 如果模板中设置了APN则会返回APN结果，sdk为个推推送结果。
     */
    public function lists(\IGtBaseTemplate $template, Collection $users, $alias = null)
    {
        putenv("needDetails=true");
        //定义"ListMessage"信息体
        $message = $this->createMessage(\IGtListMessage::class, $template);
        //接收方
        $result = [];
        if (isset($users['android']) && count($users['android'])) {
            $contentId = $this->igt->getContentId($message);
            $targetList = [];
            foreach ($users['android'] as $item) {
                $targetList[] = $this->createTarget($item, $alias);
            }
            $result['android'] = $this->igt->pushMessageToList($contentId, $targetList);
        }

        if (isset($users['ios']) && count($users['ios'])) {
            $contentId = $this->igt->getContentId($message);
            $targetList = [];
            foreach ($users['ios'] as $item) {
                if($item instanceof User){
                    $alias = 'c'.$item->uid;
                }

                if($item instanceof Agent){
                    $alias = 'a'.$item->id;
                }

                $targetList[] = $this->createTarget($item, $alias);
            }


            $result['ios'] = $this->igt->pushMessageToList($contentId, $targetList);
        }

//
//        if (isset($users['ios']) && count($users['ios'])) {//使用ios 专用APN方式
//            $contentId = $this->igt->getAPNContentId($this->config['app_id'], $message);
//            $result['ios'] = $this->igt->pushAPNMessageToList($this->config['app_id'], $contentId, array_pluck($users['ios'], 'identifier'));
//        }
        return $result;
    }

    /*
     * 作用：推送用户群
     * 参数：$template   消息模板  IGtBaseTemplate
     *      $provinces   用户所在城市，省或市，中文名  [string]
     *      $tags        用户所在标签   [string]
     *      $phone       用户平台       ['ANDROID', 'IOS']
     * 返回值：array 推送结果
     */
    public function app(\IGtBaseTemplate $template, array $provinces = [], array $tags = [], array $phone = ['ANDROID', 'IOS'])
    {
        //基于应用消息体
        $message = $this->createMessage(\IGtAppMessage::class, $template);
        //$message->set_speed(100); // 设置群推接口的推送速度，单位为条/秒，例如填写100，则为100条/秒。仅对指定应用群推接口有效。,有延时意义，所以不需要
        $message->set_appIdList(array($this->config['app_id']));
        $message->set_phoneTypeList($phone);
        count($provinces) && $message->set_provinceList($provinces); //指定地区
        count($tags) && $message->set_tagList($tags); //指定标签
        return $this->igt->pushMessageToApp($message);
    }

    /*
     * 作用：获取推送结果
     * 参数：$taskId    任务标识
     * 返回值：IGtTarget
     */
    public function getPushResult($taskId)
    {
        $result = $this->igt->getPushResult($taskId);
        var_dump($result, $taskId);
        die;
    }

    /*
     * 作用：创建接收方
     * 参数：$user      需要推送的用户，Illuminate\Database\Eloquent\Model
     *       $alias    用户别名推送，另外键名
     * 返回值：IGtTarget
     */
    protected function createTarget(Model $user, $alias)
    {
        $key = $alias ?: 'identifier';

        $target = new \IGtTarget();
        $target->set_appId($this->config['app_id']);
        if ($alias) {
            $target->set_alias($alias);
        } else {
            $target->set_clientId($user[$key]);
        }
        return $target;
    }

    /*
     * 作用：创建接收方
     * 参数：$class    消息类名  string
     *      $template  模板对象  IGtBaseTemplate
     * 返回值：IGtMessage
     */
    protected function createMessage($class, \IGtBaseTemplate $template)
    {
        $message = new $class();
        $message->set_isOffline($this->isOffline); //是否离线
        $message->set_offlineExpireTime($this->offlineExpireTime); //离线时间
        $message->set_data($template); //设置推送消息类型
        $message->set_PushNetWorkType($this->pushNetWork); //设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        return $message;
    }

}
