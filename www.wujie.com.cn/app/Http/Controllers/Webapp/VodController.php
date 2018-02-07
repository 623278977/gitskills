<?php
/**
 * 点播控制器
 * @author Administrator
 *
 */
namespace App\Http\Controllers\Webapp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
class VodController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getDetail(Request $request)
    {
        return view('vod.detail',array("id"=>$request->input('id'),"uid"=>$request->input('uid',0)));  //点播详情
    }
}
