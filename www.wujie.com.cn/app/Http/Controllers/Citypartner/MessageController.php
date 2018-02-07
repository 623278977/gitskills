<?php

namespace App\Http\Controllers\Citypartner;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\City\Partner as CityPartner;
use App\Models\Partner\Message as PartnerMessage;
use App\Http\Controllers\Citypartner\CommonController as BaseController;
class MessageController extends BaseController
{
    public function getList()
    {
        $uid = $this->mid;
        $partner = CityPartner::with('business')->where('uid',$uid)->firstOrFail();
        $message = PartnerMessage::where('uid',$uid)->orderBy('created_at','desc')->paginate(20);
        return view('citypartner.message.message',compact('partner','message'));
    }
    public function getDetail(Request $request)
    {
        $uid = $this->mid;
        $partner = CityPartner::with('business')->where('uid',$uid)->first();
        $message = PartnerMessage::with('cityPartner')->where('id',$request->get('id'))->first();
        PartnerMessage::where('id',$request->get('id'))->update(['is_read'=>1]);
        $prePage = PartnerMessage::where('uid',$uid)->where('id','<',$request->get('id'))->max('id');
        $prePage = $prePage ? : PartnerMessage::where('uid',$uid)->min('id');
        $nextPage = PartnerMessage::where('uid',$uid)->where('id','>',$request->get('id'))->min('id');
        $nextPage = $nextPage ? : PartnerMessage::where('uid',$uid)->max('id');
        return view('citypartner.message.message_detail',compact('prePage','nextPage','partner','message'));
    }
    public function postDelete(Request $request)
    {
        $uid = $this->mid;
        $currentMsgId = $request->get('currentMsgId');
        $nextMsgId = $request->get('nextMsgId');
        PartnerMessage::where([
            'uid'=>$uid,
            'id'=>$currentMsgId,
        ])->delete();
        return AjaxCallbackMessage('',true);
    }
}
