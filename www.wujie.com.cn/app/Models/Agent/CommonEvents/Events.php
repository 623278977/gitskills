<?php namespace App\Models\Agent\CommonEvents;

use Illuminate\Support\Facades\Request;
use App\Events\SendInform;
use Event;

class Events implements EventInterface
{
    public $confirm_param    = array();      //最终需要返回的参数
    public $observer         = array();      //观察者实例集合
    public static $instance  = null;         //保存实例对象
    protected static $params = array();      //传递的参数记录

    public static function instance(array $param = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        //如果传递参数存在时，进行记录
        if ($param) self::$params = $param;

        return self::$instance;
    }

    //添加观察者
    //需要添加的观察者对象实例
    public function attach($observer)
    {
        $this->observer[] = $observer;

        return $this;
    }

    //删除观察者
    //需要删除的观察者对象实例
    public function detach($observer)
    {
        $index = array_search($observer, $this->observer);

        if ($index === false || !array_key_exists($index, $this->observer)) {
            return false;
        }

        //删除指定观察者对象
        unset($this->observer[$index]);
    }

    //发送通知信息
    public function sendInform()
    {
        //获取请求数据值
        $param = self::$params ?: Request::all();

        //根据不同的观察者对象调用其需要发送通知数据的方法
        foreach ($this->_gainResultValues() as $key => $observer) {
           $this->confirm_param[] = $observer::sendInform($param);
        }

        //释放静态数组里的值
        if (self::$params) {
            foreach (self::$params as $key => $vls) {
                unset(self::$params[$key]);
            }
        }

        //返回结果集
        return $this->confirm_param;
    }

    /**
     * 内部调用 -- 获取主题对象，没有传递时，走默认的
     */
    private function _gainResultValues()
    {
        return $this->observer ?  $this->observer : config('agentinform')['agentinform'];
    }
}