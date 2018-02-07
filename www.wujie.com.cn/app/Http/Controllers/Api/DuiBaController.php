<?php

/*
 * 兑吧对接
 */

namespace App\Http\Controllers\Api;

use App\Models\Ad;
use App\Models\DuiBa;
use App\Models\ScoreLog;
use Illuminate\Http\Request;
use App\Models\User\Entity;

class DuiBaController extends CommonController {

//    private $appKey = '3GARcpfE61NbDhGHDLxvdewQm4J9';
//    private $appSecret = '3caaQzpW3nQJiNBVt1VGTA4SERBh';


    private $appKey;
    private $appSecret;


    public function __construct()
    {
        parent::__construct();
        $this->appKey = config('system.duiba.app_key');
        $this->appSecret = config('system.duiba.app_secret');
    }


    //登录处理
    public function postLogin(Request $request) {
        $uid = $request->input('uid', '');
        if (!($user = Entity::where('uid', $uid)->first())) {
            return AjaxCallbackMessage('非法的uid', false);
        }
        $query = [
            'uid' => $user->uid,
            'credits' => $user->score,
            'appKey' => $this->appKey,
            'timestamp' => time() . '000'
        ];
        if ($request->has('dbredirect')) {
            $query['redirect'] = $request->get('dbredirect');
        }
        $query['sign'] = $this->sign($query);
        return AjaxCallbackMessage('http://www.duiba.com.cn/autoLogin/autologin?' . http_build_query($query), true);
    }


    //签名处理
    private function sign($data) {
        $data['appSecret'] = $this->appSecret;
        if (isset($data['sign'])) {
            unset($data['sign']);
        }
        ksort($data);
        return md5(implode('', $data));
    }

    //扣除积分处理
    public function getPay(Request $request) {
        $uid = $request->input('uid', '');
        if (!($user = Entity::where('uid', $uid)->first())) {
            return [
                'status' => 'fail',
                'errorMessage' => '用户不存在',
                'credits' => '0',
            ];
        }
        $sign = $request->get('sign');
        if ($sign !== $this->sign($request->input())) {
            return [
                'status' => 'fail',
                'errorMessage' => '签名失败',
                'credits' => $user->score,
            ];
        } else {
            $duiba = DuiBa::create(array_filter($request->only(['uid', 'credits', 'itemCode', 'description', 'orderNum', 'type', 'facePrice', 'actualPrice', 'ip', 'params'])));
            $credits = (int) $request->get('credits');
            if ($credits > 0 && !ScoreLog::add($user->uid, $credits, 'duiba_pay', '兑换' . $request->get('description'), -1,false, 'duiba', $duiba->id)) {
                $duiba->update(['status' => 'fail']);
                return [
                    'status' => 'fail',
                    'errorMessage' => '积分不足',
                    'credits' => $user->score,
                ];
            }
            return [
                'status' => 'ok',
                'errorMessage' => '',
                'bizId' => $duiba->id,
                'credits' => $user->score,
            ];
        }
    }

    //通知处理结果
    public function getNotify(Request $request) {
        $sign = $request->get('sign');
        if ($sign === $this->sign($request->input())) {//签名成功
            $duiba = DuiBa::where('orderNum', '=', $request->get('orderNum'))->first();
            if (!$duiba) {//订单不存在
                return 'ok';
            }
            if ($request->get('success')) {//兑换成功
                $duiba->update(['status' => 'success']);
            } else {
                if ($duiba->status === 'wait')//退回积分
//                    Entity::where('uid', $duiba->uid)->increment('score', $duiba->credits);
                ScoreLog::add($duiba->uid, $duiba->credits, 'duiba_return', '兑吧退回积分', -1, false, 'duiba', $duiba->id);

                $duiba->update(['status' => 'fail', 'errorMessage' => $request->get('errorMessage')]);
            }
        }
        return 'ok';
    }



    /*
    *  生成直达商城内部页面的免登录地址
    *  通过此方法生成的免登陆地址，可以通过redirect参数，跳转到积分商城任意页面
    */
    function postAdduiba(Request $request)
    {
        $uid = $request->input('uid', '');
        if (!($user = Entity::where('uid', $uid)->first())) {
            return AjaxCallbackMessage('非法的uid', false);
        }
        $array = [
            'uid' => $user->uid,
            'credits' => $user->score,
            'appKey' => $this->appKey,
            'timestamp' => time() . '000'
        ];
        $url = "http://www.duiba.com.cn/autoLogin/autologin?";

        if ($request->has('id')) {
            $id = $request->get('id');
            $link_url = Ad::where('id', $id)->value('link_url');//获取地址
            $cut_num = stripos($link_url, 'dbredirect=');
            if ($cut_num) {
                $link = substr($link_url, $cut_num + 11);
                $array['redirect'] = urldecode($link);
            }
        }
        $sign = $this->sign($array);
        $array['sign'] = $sign;
        $url = $this->AssembleUrl($url, $array);
        // $url = $url . "uid=" . $uid . "&credits=" . $credits . "&appKey=" . $appKey . "&sign=" . $sign . "×tamp=" . $timestamp;
        return AjaxCallbackMessage($url, true);
    }

    /*
   *构建免登陆URL
   */
    function AssembleUrl($url, $array)
    {
        unset($array['appSecret']);
        foreach ($array as $key=>$value) {
            $url=$url.$key."=".urlencode($value)."&";
        }
        return $url;
    }


}
