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
class TicketController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    //门票未完成 票券详情
    public function getNotticket(Request $request){
        $data = $request->input();
        return view('ticket.notTicket',array('id'=>$data['id']));  
    }

    //现场门票 票券详情
    public function getSceneticketdetial(Request $request){
        $data = $request->input();
        return view('ticket.sceneTicketDetial',array('id'=>$data['id']));  
    }

    //直播门票 票券详情
    public function getZbticketdetial(Request $request){
        $data = $request->input();
        return view('ticket.zbTicketDetial',array('id'=>$data['id']));
    }
    //活动门票
    public function getActticket(Request $request){
        $data = $request->input();
        return view('ticket.actTicket',array('id'=>$data['id']));
    }
    //活动报名
    public function getActsign(Request $request){
        $data = $request->input();
        return view('ticket.actSign')->with('activity_id',$data['activity_id'])->with('ticket_id',$data['ticket_id']);
    }

    //活动报名（针对免费）
    public function getActapply(Request $request){
        $data = $request->input();
        // return view('ticket.actApply')->with('activity_id',$data['activity_id'])->with('ticket_id',$data['ticket_id']);
        return view('ticket.actApply',array('id'=>$data['id']));
    }

    //报名成功
    public function getApplysuccess(Request $request){
        $data = $request->input();
        return view('ticket.applySuccess')->with('id',$data['id'])->with('maker_id',$data['maker_id']);
    }
     //报名成功里跳转到百度地图
    public function getTicketmap(Request $request){
        $data = $request->input();
        return view('ticket.ticketmap')->with('id',$data['id'])->with('maker_id',$data['maker_id']);
    }
}
