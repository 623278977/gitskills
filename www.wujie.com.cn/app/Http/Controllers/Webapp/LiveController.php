<?php
/**
 * 直播控制器
 * @author Administrator
 *
 */
namespace App\Http\Controllers\Webapp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
class LiveController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getDetail(Request $request)
    {
    	/*id 直播id  uid  用户id**/
        return view('live.detail',array("id"=>$request->input('id'),'is_share'=>$request->input('is_share'),'makerid'=>$request->input('makerid'),"uid"=>$request->input('uid',0)));
    }
    public function getOrders(Request $request)
    {
        /*id 直播id**/
        return view('live.order',array("id"=>$request->input('id'),"uid"=>$request->input('uid',0)));
    }
    public function getDatong(Request $request)
    {
        /*id 大通冰室 直播id**/
        return view('live.datong',array("id"=>$request->input('id'),"uid"=>$request->input('uid',0)));
    }
}
