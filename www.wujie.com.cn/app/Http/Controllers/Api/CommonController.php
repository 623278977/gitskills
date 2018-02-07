<?php

namespace App\Http\Controllers\Api;

use App\Models\User\Entity;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User\Entity as User;
use Illuminate\Support\Facades\File;
use phpDocumentor\Reflection\Types\Null_;

class CommonController extends Controller
{
    //登录用户的uid
    public $uid;
    public $data = [];

    function __construct()
    {
        //debug 开启
        //\Debugbar::enable();
        //debug 关闭
        //\Debugbar::disable();
        error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
        if (Auth::check()) {
            $uid = Auth::id();
        } else {
            $uid = Input::get('uid', 0);
        }
        $this->uid = $uid;
        $this->data = Input::all();
    }

    /**编辑的公共方法
     *
     * @param Request $request
     * @param unknown_type $model 模型
     * @param unknown_type $primary 主键
     * @param unknown_type $uid 主键值
     * @param unknown_type $list 修改的字段
     * @return boolean
     */
    static function editList(Request $request, $model, $primary, $uid, $list)
    {
        $data = [];
        foreach ($list as $k => $v) {
            if ($request->input($v) !== NULL) {
                $data[ $v ] = $request->input($v);
            }
        }
        if (count($data)) {
            if (is_object($model)) {
                $model->where($primary, $uid)->update($data);
            } else {
                $model::where($primary, $uid)->update($data);
            }
        }

        return TRUE;
    }

    static function getUser(Request $request)
    {
        if (Auth::check()) {
            $uid = Auth::user()->uid;
        }
        $uid = isset($uid) ? $uid : $request->input('uid');
        $user = User::where('uid', $uid)->first();
        if (is_object($user)) {
            return $user;
        } else {
            return FALSE;
        }
    }

    /*
     * 根据version参数选择对应的versionService
     */
    protected function versionSelect($class, $method, $version = NULL , $selectVersion = NULL, $subfolder= NULL)
    {
        $class = explode('\\', $class);
        $className = str_replace('Controller', '', array_pop($class));


        if ($version) {
            if($subfolder){
                $version = 'App\\Services\\Version\\'.$subfolder.'\\' . $className . '\\' . ucfirst($version);
            }else{
                $version = 'App\\Services\\Version\\' . $className . '\\' . ucfirst($version);
            }


            //对应的service存在
            if (class_exists($version)) {
                //对应的service启用状态,返回实例
                if ($version::$enable == TRUE) {
                    return new $version($className, $method);
                } else {
                    //找最新的可用的service  todo 这个逻辑是有问题的
                    goto newestVersion;
                }

            } else {//没有就取比该版本号小的最新的版本
                //需要选择特定的service,但是这个service不存在,返回0
                if($selectVersion){
                    return 0;
                }

                //对应service不存在,找最新的service
                newestVersion:
                //默认值,最近的版本service
                if($subfolder){
                    $path = base_path("app/Services/Version/".$subfolder.'/'.$className);
                }else{
                    $path = base_path("app/Services/Version/$className");
                }


                if (File::exists($path)) {
                    //读取该文件夹下所有的文件
                    $files = File::files($path)?:[];
                    $version = $this->getNewestVersion($files ,$className, substr($version,-5), $subfolder);

                    return $version ? new $version($className, $method) : 0 ;
                } else {
                    return 0;
                }
            }

        } else {

            return 0;

        }

    }

    /*
     * 获取最新可用的service，但是这个service的版本号不能比app版本号大
     */
    private function getNewestVersion($files , $className, $appVersion, $subfolder=null)
    {
        if(empty($files)){
            return 0 ;
        }

        $return = [];
        foreach($files as $k => $v){
            $name = substr($v,-12,8);
            $return[$name] = (int)str_replace('_v','',$name);
        }
        arsort($return);
        $return = array_flip($return);

        foreach($return as $key=>$item){
            if($subfolder){
                $version = 'App\\Services\\Version\\' . $subfolder.'\\'.$className . '\\' . ucfirst($item);
            }else{
                $version = 'App\\Services\\Version\\' . $className . '\\' . ucfirst($item);

            }

            if($version::$enable == TRUE && $key<=$appVersion){
                return $version;
            }
        }

        return 0;
    }

    /*
     * 获得类名
     */
    public function getClassName()
    {
        return get_class($this);
    }

    /*
     * 接口初始化
     */
    public function init($method, $version , $selectVersion = null, $subfolder = null)
    {
        $version = $this->getVersion($version , $selectVersion);

        //选择版本
        $versionService = $this->versionSelect($this->getClassName(), $method, $version , $selectVersion, $subfolder);

        if ($versionService) {
            return $versionService;
        }
        return 0;
    }

    /*
     * 获取版本号参数
     */
    private function getVersion($version , $selectVersion)
    {
        if($selectVersion){
            return $selectVersion;
        }

        return $version;
    }
}
