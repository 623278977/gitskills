<?php
/**
 * 活动控制器
 * @author Administrator
 *
 */
namespace App\Http\Controllers\Webapp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;

class BaseController extends CommonController
{



    static public function init($class,$method,$version)
    {
        $param = \Request::all();
        $class = strtolower($class);
        $method = strtolower($method);
        $version = strtolower($version);

        $path = base_path("resources/views/".$class."/".$method);
        $template = $version ? "$class.$method.$version" : "$class.$method";

        //有version参数
        if($version){
            $temp_path = $path."/".$version.".blade.php";

            //模板存在
            if (File::exists($temp_path)){

                return self::bindData($template,$param);
            }

            //version参数无效,返回最新的模板
            if (File::exists($path)) {

                $files = File::files($path)?:[];
                $res = self::getUseableVersion($files,$version);
                $template = $res ? "$class.$method.$res" : "$class.$method";

                return self::bindData($template,$param);
            }else{
                //没有最新的模板,返回老版本
                goto newestVersion;
            }

        }else{

            newestVersion:
            //目录不存在,默认值
            $temp_path = $path.".blade.php";
            if (File::exists($temp_path)) {
                $template = "$class.$method";
                return self::bindData($template, $param);
            }

            return AjaxCallbackMessage('接口不存在',FALSE);
        }

    }


    //经纪人端页面自动寻址
    static public function agentInit($class,$method,$version)
    {
        $param = \Request::all();
        $class = strtolower($class);
        $method = strtolower($method);
        $version = strtolower($version);

        $path = base_path("resources/views/agent/".$class."/".$method);
        $template = $version ? "agent.$class.$method.$version" : "agent.$class.$method";

        //有version参数
        if($version){
            $temp_path = $path."/".$version.".blade.php";

            //模板存在
            if (File::exists($temp_path)){

                return self::bindData($template,$param);
            }

            //version参数无效,返回最新的模板
            if (File::exists($path)) {

                $files = File::files($path)?:[];
                $res = self::getUseableVersion($files,$version);
                $template = $res ? "agent.$class.$method.$res" : "agent.$class.$method";

                return self::bindData($template,$param);
            }else{
                //没有最新的模板,返回老版本
                goto newestVersion;
            }

        }else{

            newestVersion:
            //目录不存在,默认值
            $temp_path = $path.".blade.php";
            if (File::exists($temp_path)) {
                $template = "agent.$class.$method";
                return self::bindData($template, $param);
            }

            return AjaxCallbackMessage('接口不存在',FALSE);
        }

    }

    /*
     * 绑定变量
     */
    static private function bindData($template , $data)
    {
        error_reporting(E_ALL^E_NOTICE^E_WARNING);

        //绑定默认值
        $data['uid'] = Input::get('uid',0);
        $data['position_id'] = Input::get('position_id',0);
        $data['code'] = Input::get('code',1);
        $data['id'] = Input::get('id',0);//headline id 默认值为0


        //如果是进入直播或者视频页面就发送一个cookie过去。
        if(preg_match('/live.detail|vod.detail/i', $template)){
            return \Response::view(strtolower($template),$data)
                ->withCookie(cookie()->forever('begin_time', time()));

//            $view = view(strtolower($template),$data);
        }else{
            $view = view(strtolower($template),$data);
        }

        return $view;
    }


    /*
     * 获取最新的模板
     */
    static private function getNewestVersion($files)
    {
        if(empty($files)){
            return '' ;
        }

        $return = [];
        foreach($files as $k => $v){
            $name = substr($v,-15,5);
            $return[$name] = (int)str_replace('v','',$name);
        }

        if($return){
            arsort($return);
            $return = array_flip($return);
        }

        return $return ? array_shift($return):'';
    }

    /*
     * 获取最近可用的模板
     */
    static private function getUseableVersion($files,$version)
    {
        if(empty($files)){
            return '' ;
        }

        $return = [];
        foreach($files as $k => $v){
            $name = basename($v,'.blade.php');
            if(preg_match('/^_v\d{6,10}$/i', $name)){
                $return[] = $name;
            }
        }
        rsort($return);
        foreach($return as $value){
            if(strcasecmp($version, $value)>0){
                return $value;
            }
        }
        return '';
    }
}
