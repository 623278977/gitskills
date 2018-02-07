<?php namespace App\Services\Chat;

use App\Services\Chat\Methods\User;
use App\Services\Chat\Methods\Message;
use App\Services\Chat\SendRequest;

/*include 'SendRequest.php';
include 'methods/User.php';
include 'methods/Message.php';
include 'methods/Wordfilter.php';
include 'methods/Group.php';
include 'methods/Chatroom.php';
include 'methods/Push.php';
include 'methods/SMS.php';*/

class RangCloud
{
    public static $instance = null;
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 参数初始化
     * @param $appKey
     * @param $appSecret
     * @param string $format
     */
    public function __construct($appKey, $appSecret, $format = 'json') {
        $this->SendRequest = new SendRequest($appKey, $appSecret, $format);
    }

    //生成获取用户token
    public function User() {
        $User = new User($this->SendRequest);
        return $User;
    }

    //发送用户消息
    public function Message() {
        $Message = new Message($this->SendRequest);
        return $Message;
    }

    public function Wordfilter() {
        $Wordfilter = new Wordfilter($this->SendRequest);
        return $Wordfilter;
    }

    public function Group() {
        $Group = new Group($this->SendRequest);
        return $Group;
    }

    public function Chatroom() {
        $Chatroom = new Chatroom($this->SendRequest);
        return $Chatroom;
    }

    public function Push() {
        $Push = new Push($this->SendRequest);
        return $Push;
    }

    public function SMS() {
        $SMS = new SMS($this->SendRequest);
        return $SMS;
    }

}