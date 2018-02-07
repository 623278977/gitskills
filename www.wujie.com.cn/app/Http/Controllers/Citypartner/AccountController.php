<?php

namespace App\Http\Controllers\Citypartner;

use App\Http\Requests\AccountRequest;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\City\Partner as CityPartner;
use App\Models\Partner\BankAccount as PartnerBankAccount;
use App\Models\Zone\Entity as Zone;
use Captcha;
use App\Http\Controllers\Citypartner\CommonController as BaseController;
use Illuminate\Support\Facades\Session;
use Hash;
class AccountController extends BaseController
{
    public function getList()
    {
        $uid = $this->mid;
        $partner = CityPartner::with('zone', 'pPartner', 'partnerBankAccount')->where('uid', $uid)->firstOrFail();
        return view('citypartner.account.account', compact('partner'));
    }

    public function getPassword(Request $request)
    {
        $uid = $this->mid;
        $partner = CityPartner::with('zone', 'pPartner', 'partnerBankAccount')->where('uid', $uid)->firstOrFail();
        return view('citypartner.account.account_psw',compact('uid','partner'));
    }

    /*
     * 修改密码
     */
    public function postPassword(AccountRequest $request)
    {

        $uid = $this->mid;
        $oldPassword = $request->input('oldPassword');
        $password = Hash::make($request->input('password'));
        $cp = CityPartner::where('uid',$uid)->first();
        if(! Hash::check($oldPassword,$cp->password)){
            return AjaxCallbackMessage('当前输入的旧密码有误',false);
        }else
        {
            CityPartner::where('uid',$uid)->update(['password'=>$password]);
            return AjaxCallbackMessage('密码修改修改成功！',true,url('citypartner/account/list'));
        }
    }
    public function getEdit(Request $request)
    {
        $uid = $this->mid;
        $zones = Zone::cache(0);
        $zoneTree = toTree($zones, 'id', 'upid', 'children');
        $partner = CityPartner::with('pPartner')->where('uid',$uid)->first();
        $family = familyTree($zones, $partner->zone_id);
        return view('citypartner.account.account_edit',compact('partner','zone','zoneTree','family'));
    }
    /*
     *提交编辑
     */
    public function postEdit(AccountRequest $request)
    {
        if(!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/',$request->input('idcard'))){
            return AjaxCallbackMessage("持卡人身份证格式错误",false);
        }
        $uid = $this->mid;
        $partner = $request->only('realname','zone_id','email','avatar','bank_account','bank','deposit_bank','cardholder_name','idcard');
        $partner['avatar'] = ltrim($partner['avatar'],'/');
//        $account = $request->only('account','bank','deposit_bank','holder_name','identity_number');
        $res = CityPartner::where('uid',$uid)->update($partner);
        if($res !== false){
            $userinfo = CityPartner::where('uid',$uid)->first();
            Session::put('userinfo',$userinfo);
        }
//        $pba = PartnerBankAccount::where('uid',$uid)->first();
//        if(! $pba){
//            $account['uid'] = $uid;
//            PartnerBankAccount::create($account);
//        }else{
//            PartnerBankAccount::where('uid',$uid)->update($account);
//        }

        return AjaxCallbackMessage('编辑成功',true,url('citypartner/account/list?uid='.$uid));
    }
    public function getNewcpt()
    {
        return Captcha::create('default');
    }
}
