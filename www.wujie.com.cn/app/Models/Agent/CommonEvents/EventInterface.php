<?php namespace App\Models\Agent\CommonEvents;

interface EventInterface
{
    //添加观察者
    public function attach($observer);

    //删除观察者
    public function detach($observer);

    //统一发送消息
    public function sendInform();
}