<?php

namespace App\Http\Controllers\Citypartner;

use App\Models\Business\Audit;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\City\Partner as CityPartner;
use App\Models\Business\Entity as Business;
use App\Models\Business\Payment as Payment;
use App\Models\Attachment\Entity as Attachment;
use App\Http\Controllers\Citypartner\CommonController as BaseController;
class BusinessController extends BaseController
{
    function getList()
    {
        $uid = $this->mid;
        $partner = CityPartner::with('business')->where('uid',$uid)->firstOrFail();
        $business = Business::orderBy('id','asc')->where('partner_uid',$uid)->paginate(20)->appends(['partner_uid'=>$uid]);
        $businessReview =  Business::orderBy('id','asc')->where('status',1)->where('partner_uid',$uid)->paginate(20)->appends(['status'=>1,'partner_uid'=>$uid]);
        $businessPay =  Business::orderBy('id','asc')->where('status',2)->where('partner_uid',$uid)->paginate(20)->appends(['status'=>2,'partner_uid'=>$uid]);
        $businessFinish =  Business::orderBy('id','asc')->where('status',3)->where('partner_uid',$uid)->paginate(20)->appends(['status'=>3,'partner_uid'=>$uid]);
        $businessReturn =  Business::orderBy('id','asc')->where('status',-1)->where('partner_uid',$uid)->paginate(20)->appends(['status'=>-1,'partner_uid'=>$uid]);
        $num = [];
        $num['total'] =  Business::where('partner_uid',$uid)->count();
        $num['review'] =  Business::where('partner_uid',$uid)->where('status',1)->count();
        $num['pay'] = Business::where('partner_uid',$uid)->where('status',2)->count();
        $num['finish'] =  Business::where('partner_uid',$uid)->where('status',3)->count();
        $num['back'] =  Business::where('partner_uid',$uid)->where('status',-1)->count();
        return view('citypartner.business.business',compact('num','partner','businessPay','businessFinish','businessReturn','businessReview','business','uid'));
    }
    function getDetail(Request $request)
    {
        $id = $request->get('id');
        $business = Business::with('cityPartner','businessFactor','businessPayment','businessAudit')->where('id',$id)->firstOrFail();
        $partner = CityPartner::with('business')->where('uid',$id)->first();
        $audits = Audit::where('business_id',$id)->orderBy('created_at','asc')->get();
        $where = ['model'=>'business','post_id'=>$id];
        $attachments =Attachment::where($where)->get();
        $payments = Payment::with('admin')->where('business_id',$id)->orderBy('created_at','asc')->get();
        $paynum  = Payment::where('business_id',$id)->count();
        $payed  = Payment::where('business_id',$id)->sum('amount');
        return view('citypartner.business.business_detail',compact('paynum','payed','payments','attachments','business','partner','audits'));
    }
    function getDownload(Request $request)
    {
        $path = $request->get('path');
        $name = substr($path,strrpos($path,'/')+1);
        $dir = str_replace($name,'',$path);
        download($name,$dir);
    }
}
