<?php

/*
 * 个推模板处理
 * 
 * 说明：模板类型分为4种： 
 * 打开网页（Link） 点击打开网页
 * 打开应用（Notification） 点击打开应用
 * 弹框下载（NotyPopLoad） 点击下载
 * 透传消息（Transmission）  由APP接收处理，无直接系统栏消息提醒
 * 
 * 注：由于在iOS中只有当应用启动时才能通过个推SDK进行推送（未启动应用时通过APNS进行推送），
 * 而使用LinkTemplate（点击通知打开网页模板）和NotificationTemplate（点击通知打开应用模板）进行推送的话在客户端是以弹窗方式进行通知，
 * 因此不推荐在iOS上使用这两个推送动作模板。
 * 
 * 另外，使用个推SDK的TransmissionTemplate（透传消息模板）发送消息，其传输的数据最大为是2KB，
 * 而APNS最大只支持256Byte，因此建议iOS推送采用TransmissionTemplate（透传消息模板）。
 */

namespace App\Services\GeTui;

class Template extends GeTui
{
    /*
     * 作用：点击打开网页模板
     * 参数：$title     通知栏标题  string
     *      $text      通知栏内容  string
     *      $url       打开连接地址  string
     *      $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
     *      $logo      通知栏logo  string
     *      $logoURL   通知栏logo链接  string
     *      $ring      是否响铃  bool
     *      $vibrate   是否震动  bool
     *      $clearable 通知栏是否可清除  bool
     * 返回值：IGtLinkTemplate
     */
    public function link($title, $text, $url, $duration = null, $logo = '', $logoURL = '', $ring = true, $vibrate = true, $clearable = true)
    {
        $template = $this->createTemplate(\IGtLinkTemplate::class);
        $template->set_title($title); //通知栏标题
        $template->set_text($text); //通知栏内容
        $template->set_logo($logo); //通知栏logo
        $template->set_logoURL($logoURL); //通知栏logo链接
        $template->set_isRing($ring); //是否响铃
        $template->set_isVibrate($vibrate); //是否震动
        $template->set_isClearable($clearable); //通知栏是否可清除
        $template->set_url($url); //打开连接地址
        $this->setDuration($template, $duration); //设置ANDROID客户端在此时间区间内展示消息
        return $template;
    }

    /*
     * 作用：点击打开应用模板
     * 参数：$title     通知栏标题  string
     *      $text      通知栏内容  string
     *      $content   透传内容，供APP接收处理 string
     *      $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
     *      $logo      通知栏logo  string
     *      $logoURL   通知栏logo链接  string
     *      $ring      是否响铃  bool
     *      $vibrate   是否震动  bool
     *      $clearable 通知栏是否可清除  bool
     * 返回值：IGtNotificationTemplate
     */
    public function notification($title, $text, $content, $duration = null, $logo = '', $logoURL = '', $ring = true, $vibrate = true, $clearable = true)
    {
        $template = $this->createTemplate(\IGtNotificationTemplate::class);
        $template->set_transmissionType(2); //透传消息类型，自动打开APP应用
        $template->set_transmissionContent($content); //透传内容
        $template->set_title($title); //通知栏标题
        $template->set_text($text); //通知栏内容
        $template->set_logo($logo); //通知栏logo
        $template->set_logoURL($logoURL); //通知栏logo链接
        $template->set_isRing($ring); //是否响铃
        $template->set_isVibrate($vibrate); //是否震动
        $template->set_isClearable($clearable); //通知栏是否可清除
        $this->setDuration($template, $duration); //设置ANDROID客户端在此时间区间内展示消息
        return $template;
    }

    /*
     * 作用：点击下载模板
     * 参数：$title         通知栏弹框标题  string
     *      $text           通知栏内容  string
     *      $pop_text       弹框内容 string
     *      $load_title     下载标题 string
     *      $load_url       下载地址 string
     *      $duration       消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
     *      $logo           通知栏和下载logo  string
     *      $pop_image      弹框图片  string
     *      $ring           是否响铃  bool
     *      $vibrate        是否震动  bool
     *      $clearable      通知栏是否可清除  bool
     *      $auto_install   是否自动安装  bool
     *      $actived        是否自动打开应用
     * 返回值：IGtNotyPopLoadTemplate
     */
    public function notyPopLoad($title, $text, $pop_text, $load_title, $load_url, $duration = null, $logo = '', $pop_image = '', $ring = true, $vibrate = true, $clearable = true, $auto_install = false, $actived = true)
    {
        $template = $this->createTemplate(\IGtNotyPopLoadTemplate::class);
        //通知栏
        $template->set_notyTitle($title);                 //通知栏标题
        $template->set_notyContent($text); //通知栏内容
        $template->set_notyIcon($logo);                      //通知栏logo
        $template->set_isBelled($ring);                    //是否响铃
        $template->set_isVibrationed($vibrate);               //是否震动
        $template->set_isCleared($clearable);                   //通知栏是否可清除
        //弹框
        $template->set_popTitle($title);   //弹框标题
        $template->set_popContent($pop_text); //弹框内容
        $template->set_popImage($pop_image);           //弹框图片
        $template->set_popButton1("下载");     //左键
        $template->set_popButton2("取消");     //右键
        //下载
        $template->set_loadIcon($logo);           //弹框图片
        $template->set_loadTitle($load_title);
        $template->set_loadUrl($load_url);
        $template->set_isAutoInstall($auto_install); //自动安装
        $template->set_isActived($actived); //自动打开应用
        $this->setDuration($template, $duration); //设置ANDROID客户端在此时间区间内展示消息
        return $template;
    }

    /*
     * 作用：透传消息
     * 参数：$content  透传内容，供APP接收处理 string
     *      $duration  消息展示时间区间，开始时间与结束时间 array(Y-m-d H:i:s,Y-m-d H:i:s)
     * 返回值：IGtTransmissionTemplate
     */
    public function transmission($content, $duration = null)
    {
        $template = $this->createTemplate(\IGtTransmissionTemplate::class);
        $template->set_transmissionType(0); //透传消息类型，自动打开APP应用
        //透传内容
        $template->set_transmissionContent($content);
        $this->setDuration($template, $duration); //设置ANDROID客户端在此时间区间内展示消息
        return $template;
    }

    /*
     * 作用：IOS专用APNs消息模板
     * 参数：$alertMsg  消息内容 string|DictionaryAlertMsg
     *      $badge      应用图标数字 int
     *      $sound      设置声音  string
     *      $contentAvailable  启动报刊亭背景 bool
     *      $customMsg  自定义参数  array
     *      $category   通知类别  string
     * 返回值：IGtAPNPayload
     */
    public function anp($alertMsg, $badge = 1, $sound = 'default', $contentAvailable = false, $customMsg = [], $category = 'ACTIONABLE')
    {
        $template = new \IGtAPNTemplate();
        $this->getAPN($template, $alertMsg, $badge, $sound, $contentAvailable, $customMsg, $category);
        return $template;
    }

    /*
     * 作用：设置APN形式，ios平台专用
     * 参考地址：https://developer.apple.com/library/content/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/PayloadKeyReference.html#//apple_ref/doc/uid/TP40008194-CH17-SW1
     * 参数：$template  追加模板对象
     *      $alertMsg  消息内容 string|DictionaryAlertMsg
     *      $customMsg  自定义参数  array
     *      $badge      应用图标数字 int 显示在应用上的消息数字，不显示传0
     *      $sound      设置声音  string，如果不要声音传 com.gexin.ios.silence
     *      $contentAvailable  启动静默后台传送不会显示在系统消息栏上 bool
     *      $category   通知类别  string
     * 返回值：IGtAPNPayload
     */
    public function setAPN(\IGtBaseTemplate $template, $alertMsg, $customMsg = [], $badge = 1, $sound = 'default', $contentAvailable = false, $category = 'ACTIONABLE')
    {
        $apn = new \IGtAPNPayload();
        $apn->alertMsg = $alertMsg;
        $apn->badge = $badge; //应用icon上显示的数字
        $apn->sound = $sound; //通知铃声文件名
        $apn->customMsg = $customMsg; //增加自定义的数据
        $apn->contentAvailable = $contentAvailable; //推送直接带有透传数据
        //IOS8 支持
        $apn->category = $category; //在客户端通知栏触发特定的action和button显示
        $template->set_apnInfo($apn);
        return $apn;
    }

    /*
     * 作用：设置APN形式，ios平台专用
     * 参数：$body         通知文本消息字符串
     *      $actionLocKey  设置按钮标题，为空使用系统默认
     *      $locKey        指定Localizable.strings文件中相应的key
     *      $locArgs       设置变量的字符串值出现在LOC密钥格式说明符的地方
     *      $launchImage   指定启动界面图片名
     *      $title         通知标题
     *      $titleLocKey   对于标题指定执行按钮所使用的Localizable.strings,仅支持IOS8.2以上版本
     *      $titleLocArgs  对于标题, 如果loc-key中使用的占位符，则在loc-args中指定各参数,仅支持IOS8.2以上版本
     * 返回值：DictionaryAlertMsg
     */
    public function getAlertMsg($body, $actionLocKey, $locKey, array $locArgs, $launchImage, $title, $titleLocKey, array $titleLocArgs)
    {
        $alertmsg = new \DictionaryAlertMsg();
        $alertmsg->body = $body; //通知文本消息字符串
        $alertmsg->actionLocKey = $actionLocKey; //(用于多语言支持）指定执行按钮所使用的Localizable.strings
        $alertmsg->locKey = $locKey; //(用于多语言支持）指定Localizable.strings文件中相应的key
        $alertmsg->locArgs = $locArgs; //如果loc-key中使用的占位符，则在loc-args中指定各参数
        $alertmsg->launchImage = $launchImage; //指定启动界面图片名
//        IOS8.2 支持
        $alertmsg->title = $title; //通知标题
        $alertmsg->titleLocKey = $titleLocKey; //(用于多语言支持）对于标题指定执行按钮所使用的Localizable.strings,仅支持IOS8.2以上版本
        $alertmsg->titleLocArgs = $titleLocArgs; //对于标题, 如果loc-key中使用的占位符，则在loc-args中指定各参数,仅支持IOS8.2以上版本
        return $alertmsg;
    }

    /*
     * 作用：创建模板
     * 参数：$class 模板类名  string
     * 返回值：IGtBaseTemplate
     */
    protected function createTemplate($class)
    {
        $template = new $class();
        $template->set_appId($this->config['app_id']);
        $template->set_appkey($this->config['app_key']);
        return $template;
    }

    /*
     * 作用：设置客户端在此时间区间内展示消息
     * 参数：$template 模板  IGtBaseTemplate
     * 返回值：无
     */
    protected function setDuration(\IGtBaseTemplate $template, $duration)
    {
        if (is_array($duration) && count($duration) == 2) {
            $template->set_duration($duration[0], $duration[1]); //设置ANDROID客户端在此时间区间内展示消息
        }
    }

}
