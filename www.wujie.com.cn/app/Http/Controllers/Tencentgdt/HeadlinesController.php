<?php
namespace App\Http\Controllers\Tencentgdt;

use App\Models\Headlines\Headlines;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HeadlinesController extends Controller
{
    /**
     *今日头条接口一
     */
    public function getHead(Request $request)
    {

        $aid = $request->adid?:'';               //广告计划 id
        $cid = $request->cid?:'';                //广告创意 id
        $convert_id = $request->convert_id?:'';  //转化跟踪 id
        $mac = $request->mac?:'';                //用户终端的eth0 接口的MAC 地址
        $idfa = $request->idfa?:'';              //iOS IDFA 适用iOS6 及以上
        $os = $request->os?:'';                  //客户端操作系统的类型
        $ts = $request->timestamp?:'';           //客户端触发监测的时间
        $callback_url = $request->callback_url?:'';//激活回调地址
        //参数缺失
        if(empty($os) || empty($callback_url)){
            return json_encode(['ret'=>'-1', 'msg'=>"missing params"]);
        }

        $result = Headlines::create([
            'aid' => $aid,
            'cid' => $cid,
            'convert_id' => $convert_id,
            'mac' => $mac,
            'idfa' => $idfa,
            'os' => $os,
            'ts' => $ts,
            'status'=> '0',
            'callback_url' => $callback_url,
        ]);

        return json_encode(['ret'=>'0', 'msg'=>"success"]);
    }


}
