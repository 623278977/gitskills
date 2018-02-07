<?php

namespace App\Services\Version;

use Closure;

abstract class VersionSelect
{
    static $enable = TRUE;  //版本是否启用
    public $className;      //当前service名称
    public $controllerName; //关联的控制器名称
    public $methodName;     //关联方法名称
    public $validateData = [];  //验证后的数据
    public $param = [];

    public function __construct($controllerName = NULL , $controllerMethod = NULL)
    {
        $this->className = get_class($this);
        if($controllerName) $this->controllerName = $controllerName;
        if($controllerMethod) $this->controllerMethod = explode('::', $controllerMethod)[1];
    }


    /*
     * 数据验证->业务逻辑->数据格式化->响应数据
     */
    public function bootstrap(array $rawParam = [], array $otherParam = [], Closure $logicCallback = NULL, Closure $formatCallback = NULL)
    {
        $param = [];

        foreach ($rawParam as $key => $val) {
            $val === NULL || $param[ $key ] = $val;
        }

        foreach ($otherParam as $key => $val) {
            $val === NULL || $param[ $key ] = $val;
        }

        //参数验证
        $res = $this->validate($param);

        //参数验证不通过,is_break=1,返回提示信息
        if ($res['is_break']) {
            return $res['message'];
        }

        //逻辑处理
        if ($logicCallback) {
            $data = $this->logicTask($param ,$logicCallback);
        } else {
            $data = $this->logicTask($param);
        }

        //格式化数据
        if ($formatCallback) {
            $data = $this->dataFormat($data, $formatCallback);
        } else {
            $data = $this->dataFormat($data);
        }

        //响应数据
        return $this->response($data);
    }

    /*
     * 输入参数验证
     */
    public function validate($rawParam = [])
    {
        $methodName = $this->controllerMethod . 'Validate';

        /*
         * 如果没有在service里定义了验证方法,返回0
         */
        if (method_exists($this->className, $methodName)) {
            return  $this->$methodName($rawParam);
        }

        return 0;
    }

    /*
     * 响应数据
     */
    public function response($data)
    {
        return $data;
    }


    /*
     * 业务逻辑
     */
    public function logicTask($param = [] ,Closure $callback = NULL)
    {
        if (method_exists($this->className, $this->controllerMethod)) {

            $return = $this->{$this->controllerMethod}($this->validateData ?: $param);

            if ($callback) {
                return $callback($return);
            }

            return $return;
        }

        return 0;
    }


    /*
     * 返回数据格式化
     */
    public function dataFormat($data, Closure $callback = NULL)
    {
        if ($callback) {
            return $callback($data);
        }

        //去除不符合规范的数据
        return $this->clearData($data);
    }


    /*
     * 把数据中的null全部转化成空字符串
     */
    private function clearData($data)
    {
        return nullToString($data);
    }


    /*
     * 错误输出
     */
    public function error()
    {
        return '接口停用请升级APP';
    }


}