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
use App\Models\Activity\Entity as Activity;
class ActivityController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getDetail(Request $request)
    {
        $data = $request->input();
        return view('activity.detail')->with('position_id',$request->input('position_id',0))->with('id',$data['id'])->with('makerid',$data['makerid'])->with('uid',$request->input('uid',0));
    }
    public function getDetailDescription(Request $request)
    {
        $data = $request->input();
        return view('activity.detaildescription')->with('id',$data['id']);  //更多详情
    }
    public function getDockpool(Request $request)
    {
        $data = $request->input();
        return view('activity.dockpool')->with('uid',$data['uid']);  //我的--申请对接池
    }

    //确认订单（针对免费）
    public function getFreecheck(Request $request)
    {
        $data = $request->input();
        return view('freecheck.detail')->with('id',$data['id'])->with('ticket_id',$data['ticket_id']); 
    }
    //地址跳转地图
    public function getBmap(Request $request)
    {
        $data = $request->input();
        return view('freecheck.bmap')->with('id',$data['id'])->with('maker_id',$data['maker_id']); 
    }
}
