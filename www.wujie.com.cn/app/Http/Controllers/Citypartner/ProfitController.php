<?php

namespace App\Http\Controllers\Citypartner;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\City\Partner as CityPartner;
use App\Models\City\PartnerIncome as CityPartnerIncome;
use App\Models\City\PartnerAchievement as CityPartnerAchievement;
use App\Http\Controllers\Citypartner\CommonController as BaseController;
class ProfitController extends BaseController
{
    public function getList(Request $request)
    {
        $uid = $this->mid;
        $peroid = getPeroid(time(), time());
        $month = date("m",time());
//        if(isset($peroid[1]))
//        {
//            $peroid = strrpos($peroid[1],$month) ? $peroid[1]:$peroid[0];
//        }else{
//            $peroid =$peroid[0];
//        }
        if($month > 6){
            $peroid =$peroid[1];
        }else{
            $peroid = $peroid[0];
        }
        $peroid2 = explode('--', $peroid);
        $btime =mktime(0, 0, 0, substr($peroid2[0], -2), 1, substr($peroid2[0], 0, 4)) ;
        $etime =mktime(0, 0, 0, substr($peroid2[1], -2)+1, 1, substr($peroid2[1], 0, 4)) ;
        $partner = CityPartner::where('uid', $uid)->firstOrFail();
        //账号注册至今的个人业绩
        $totalAmount = CityPartnerAchievement::where('partner_uid', $uid)->sum('amount');
        //账号注册至今的收益
        $totalProfit = CityPartnerIncome::where('partner_uid', $uid)->sum('amount');
        //当前周期个人业绩列表
        $currentAchievement = CityPartnerAchievement::with('cityPartner')->where('partner_uid', $uid)->whereBetween('arrival_at', [$btime, $etime])
            ->orderBy('arrival_at','desc')->sum('amount');
        $achievement = CityPartnerAchievement::with('cityPartner')->where('partner_uid', $uid)->whereBetween('arrival_at', [$btime, $etime])
            ->orderBy('arrival_at','desc')->paginate(20);
        $achievement->appends(['peroid' => $peroid, 'uid' => $uid]);
        $proportion = $this->getProportion($currentAchievement);
        return view('citypartner.profit.income', compact('currentAchievement','achievement', 'peroid', 'totalAmount', 'totalProfit', 'uid', 'proportion','partner'));
    }

    public function getRule(Request $request)
    {
        $uid = $this->mid;
        $partner = CityPartner::where('uid', $uid)->firstOrFail();
        return view('citypartner/profit/income_rule', compact('partner'));
    }

    public function getIncome(Request $request)
    {
        $uid = $this->mid;
        $partner = CityPartner::with('partnerIncome')->where('uid', $uid)->firstOrFail();
        $peroid = getPeroid($partner->created_at->getTimestamp(), time());
        $peroid = array_reverse($peroid);
        $currentMonth = date("m",$partner->created_at->getTimestamp());
        if((int)$currentMonth > 6){
            unset($peroid[count($peroid)-1]);
        }
        $incomes = CityPartnerIncome::where('partner_uid', $uid);
        if (!$request->has('peroid')) {
            //当前周期开始和结束月份
            $month = explode('--', $peroid[0]);
            $month_profit = array();
            $extra_profit =array();
            $team_profit =array();
            $special_profit=array();
            for ($i = $month[0]; $i <= $month[1]; $i++) {
                $month_profit[] = CityPartnerIncome::where('type','base')->where('partner_uid', $uid)->where('start_month', $this->doFormatDate($i))->where('end_month', $this->doFormatDate($i + 1))->sum('amount');
                $extra_profit[] = CityPartnerIncome::where('type','extra')->where('partner_uid', $uid)->where('start_month', $this->doFormatDate($i))->where('end_month', $this->doFormatDate($i + 1))->sum('amount');
                $team_profit[] = CityPartnerIncome::where('type','team')->where('partner_uid', $uid)->where('start_month', $this->doFormatDate($i))->where('end_month', $this->doFormatDate($i + 1))->sum('amount');
                $special_profit[] = CityPartnerIncome::where('type','special')->where('partner_uid', $uid)->where('start_month', $this->doFormatDate($i))->where('end_month', $this->doFormatDate($i + 1))->sum('amount');
            }
            return view('citypartner.profit.my_income', compact('partner', 'peroid', 'month', 'incomes', 'month_profit', 'team_profit', 'special_profit', 'extra_profit'));
        }
        $month = explode('--', $request->get('peroid'));
        $month_profit = array();
        for ($i = $month[0]; $i <= $month[1]; $i++) {
            if(substr($i,-2) == '12'){
                $year = date("Y",time());
                $year = ($year+1).'-01';
                $month_profit[] = CityPartnerIncome::where('type','base')->where('partner_uid', $uid)->where('start_month', $this->doFormatDate($i))->where('end_month', $year)->sum('amount');
            }
            $month_profit[] = CityPartnerIncome::where('type','base')->where('partner_uid', $uid)->where('start_month', $this->doFormatDate($i))->where('end_month', $this->doFormatDate($i + 1))->sum('amount');
        }
        $search = substr($month[0],-2,2);
        $pbegin = substr_replace($month[0],'-'.$search,-2);
        $search = substr($month[1],-2,2);
        $pend = substr_replace($month[1],'-'.$search,-2);
        $extra_profit =  CityPartnerIncome::where('type','extra')->where('partner_uid', $uid)->where('start_month',$pbegin)->where('end_month',$pend)->first();
        $extra_profit = isset($extra_profit) ?  $extra_profit->amount:0;
        $team_profit = CityPartnerIncome::where('type','team')->where('partner_uid', $uid)->where('start_month',$pbegin)->where('end_month',$pend)->first();
        $team_profit = isset($team_profit) ?  $team_profit->amount:0;
        $special_profit= CityPartnerIncome::where('type','special')->where('partner_uid', $uid)->where('start_month',$pbegin)->where('end_month',$pend)->first();
        $special_profit = isset($special_profit) ?  $special_profit->amount:0;
        return view('citypartner.profit.my_income', compact('partner', 'peroid', 'month', 'incomes', 'month_profit', 'team_profit', 'special_profit', 'extra_profit'));
    }

    public function getAchievement(Request $request)
    {
        $uid = $this->mid;
        //注册至今我的业绩额
        $myAchievement = CityPartnerAchievement::where('partner_uid', $uid)->where('range', 'personal')->sum('amount');
        //注册至今我的团队成员业绩额
        $teamAchievement = CityPartnerAchievement::where('partner_uid', $uid)->where('range', 'team')->sum('amount');
        //总业绩额
        $totalAchievement = $myAchievement + $teamAchievement;
        $flag = $uid==$request->get('member');
        if($flag || !$request->has('search')) {

            $member='';
            $partner = CityPartner::where('uid', $uid)->first();
            $peroid = getPeroid($partner->created_at->getTimestamp(), time());
            $peroid = array_reverse($peroid);
            $currentMonth = date("m",$partner->created_at->getTimestamp());
            if((int)$currentMonth > 6){
                unset($peroid[count($peroid)-1]);
                $calperoid = $peroid[0];
                $begin_end = explode('--', $calperoid);
            }else{
                $month = date("m",time());
                $calperoid = strrpos($peroid[1],$month) ? $peroid[1]:$peroid[0];
                $begin_end = explode('--', $calperoid);
            }

            if($flag){
                $calperoid = $request->get('peroid');
                $begin_end = explode('--', $calperoid);
                $selPeroid =$calperoid;
            }
            $begin = $begin_end[0];
            $end  =$begin_end[1];
            $tsBegin =mktime(0, 0, 0, substr($begin, -2), 1, substr($begin, 0, 4));
            $tsEnd =mktime(0, 0, 0, substr($end, -2)+1, 1, substr($end, 0, 4));
            $monthAchievement =array();
            for($i=$begin;$i<=$end;$i++)
            {
                $tsmBegin = mktime(0, 0, 0, substr($i, -2), 1, substr($i, 0, 4));
                $tsmEnd = mktime(0, 0, 0, substr($i+1, -2), 1, substr($i+1, 0, 4));
              $monthAchievement[] = CityPartnerAchievement::whereBetween('arrival_at',[$tsmBegin,$tsmEnd])->where('range','personal')->where('partner_uid', $uid)->sum('amount');
            }
            $peroidAchievement = array_sum($monthAchievement);
            $members = CityPartner::where('p_uid',$uid)->get();
            $lists = CityPartnerAchievement::with('citypartner')->whereBetween('arrival_at',[$tsBegin,$tsEnd])->where('range','personal')->where('partner_uid',$uid)->paginate(20);
            $lists->appends(['peroid'=>$peroid[0],'uid'=>$uid]);
            $member = $uid;
            return view('citypartner.profit.total_performance', compact('selPeroid','member','partner','lists','monthAchievement','peroidAchievement','uid', 'begin','end', 'totalAchievement', 'myAchievement', 'teamAchievement', 'peroid', 'members','member'));
        }else{
            $data =$request->all();
            $uid =$this->mid;
            $member=$data['member'];
            $partner = CityPartner::where('uid', $uid)->first();
            $peroid = getPeroid($partner->created_at->getTimestamp(), time());
            $peroid = array_reverse($peroid);
            $currentMonth = date("m",$partner->created_at->getTimestamp());
            if((int)$currentMonth > 6){
                unset($peroid[count($peroid)-1]);
            }
            $month = date("m",time());
            $calperoid = $data['peroid'];
            $begin_end = explode('--', $calperoid);
            $begin = $begin_end[0];
            $end  =$begin_end[1];
            $tsBegin =mktime(0, 0, 0, substr($begin, -2), 1, substr($begin, 0, 4));
            $tsEnd =mktime(0, 0, 0, substr($end+1, -2), 1, substr($end, 0, 4));
            $monthAchievement =array();
            if($member == -2){
                //所所有成员
                for($i=$begin;$i<=$end;$i++)
                {
                    $tsmBegin = mktime(0, 0, 0, substr($i, -2), 1, substr($i, 0, 4));
                    $tsmEnd = mktime(0, 0, 0, substr($i+1, -2), 1, substr($i+1, 0, 4));
                    $monthAchievement[] = CityPartnerAchievement::where('p_uid', $uid)
                        ->whereBetween('arrival_at',[$tsmBegin,$tsmEnd])
                        ->orWhere(function($query) use($uid,$tsmBegin,$tsmEnd){
                            $query->where('partner_uid',$uid)->where('range','<>','team')
                                ->whereBetween('arrival_at',[$tsmBegin,$tsmEnd]);
                        })
                        ->sum('amount');
                }
                $peroidAchievement = array_sum($monthAchievement);
                $name = '所有成员';
                $lists = CityPartnerAchievement::with('citypartner')->where('p_uid',$uid)
                    ->whereBetween('arrival_at',[$tsBegin,$tsEnd])
                    ->orWhere(function($query) use($uid,$tsBegin,$tsEnd){
                        $query->where('partner_uid',$uid)->where('range','<>','team')
                            ->whereBetween('arrival_at',[$tsBegin,$tsEnd]);
                    })->paginate(20);
            }elseif($member==-1){
                //团队
                for($i=$begin;$i<=$end;$i++)
                {
                    $tsmBegin = mktime(0, 0, 0, substr($i, -2), 1, substr($i, 0, 4));
                    $tsmEnd = mktime(0, 0, 0, substr($i+1, -2), 1, substr($i+1, 0, 4));
                    $monthAchievement[] = CityPartnerAchievement::where('partner_uid', $uid)->whereBetween('arrival_at',[$tsmBegin,$tsmEnd])->where('range','team')->sum('amount');
                }
                $peroidAchievement = array_sum($monthAchievement);
                $name = '我的团队';
                $lists = CityPartnerAchievement::with('citypartner')->where('partner_uid',$uid)->where('range','team')->whereBetween('arrival_at',[$tsBegin,$tsEnd])->paginate(20);
            }else{
                //个人业绩
                for($i=$begin;$i<=$end;$i++)
                {
                    $tsmBegin = mktime(0, 0, 0, substr($i, -2), 1, substr($i, 0, 4));
                    $tsmEnd = mktime(0, 0, 0, substr($i+1, -2), 1, substr($i+1, 0, 4));
                    $monthAchievement[] = CityPartnerAchievement::where('partner_uid', $member)->whereBetween('arrival_at',[$tsmBegin,$tsmEnd])->sum('amount');
                }
                $peroidAchievement = array_sum($monthAchievement);
                $name =CityPartner::where('uid',$member)->first()->realname;
                $lists = CityPartnerAchievement::with('citypartner')->where('partner_uid',$member)->whereBetween('arrival_at',[$tsBegin,$tsEnd])->paginate(20);
            }
            $members = CityPartner::where('p_uid',$uid)->get();
            $selPeroid=$data['peroid'];
            $lists->appends(['peroid'=>$data['peroid'],'uid'=>$uid,'member'=>$member,'search'=>1]);
            $membername = $this->getMemberName($member);
            return view('citypartner.profit.total_performance', compact('membername','name','partner','selPeroid','lists','monthAchievement','peroidAchievement','uid', 'begin','end', 'totalAchievement', 'myAchievement', 'teamAchievement', 'peroid', 'members','member'));
        }
    }

    public function getCurrent(Request $request)
    {
        $data=$request->all();
        $uid = $this->mid;
        $member=isset($data['member'])?  $data['member']:$uid;
        $partner = CityPartner::where('uid', $uid)->first();
        $peroid =$data['peroid'];
        $begin_end = explode('--', $peroid);
        $begin = $begin_end[0];
        $end  =$begin_end[1];
        $tsBegin =mktime(0, 0, 0, substr($begin, -2), 1, substr($begin, 0, 4));
        $tsEnd =mktime(0, 0, 0, substr($end, -2)+1, 1, substr($end, 0, 4));
        $monthAchievement = array();
        for($i=$begin,$j=0;$i<=$end&&$j<6;$i++,$j++)
        {
            $tsmBegin = mktime(0, 0, 0, substr($i, -2), 1, substr($i, 0, 4));
            $tsmEnd = mktime(0, 0, 0, substr($i+1, -2), 1, substr($i+1, 0, 4));
            $monthAchievement[$j]['month'] = $i;
            $monthAchievement[$j]['my'] = CityPartnerAchievement::where('partner_uid',$uid)->where('range','personal')->whereBetween('arrival_at',[$tsmBegin,$tsmEnd])->sum('amount');
            $monthAchievement[$j]['team'] = CityPartnerAchievement::where('partner_uid',$uid)->where('range','team')->whereBetween('arrival_at',[$tsmBegin,$tsmEnd])->sum('amount');
        }
        $lists =  CityPartnerAchievement::where('partner_uid',$uid)->whereBetween('arrival_at',[$tsBegin,$tsEnd])->where('range','personal')->paginate(20);
        //团队成员业绩额
        $totalTeamAchievement =CityPartnerAchievement::where('partner_uid',$uid)->where('range','team')->whereBetween('arrival_at',[$tsBegin,$tsEnd])->sum('amount');
        //我的业绩额
        $totalMyAchievement =CityPartnerAchievement::where('partner_uid',$uid)->where('range','personal')->whereBetween('arrival_at',[$tsBegin,$tsEnd])->sum('amount');
        $members = CityPartner::where('p_uid',$uid)->get();
        $lists->appends(['peroid'=>$data['peroid'],'uid'=>$uid]);
        $name = CityPartner::where('uid',$uid)->first()->realname;
        return view('citypartner.profit.performance_detail',compact('name','member','partner','lists','begin','name','end','members','totalTeamAchievement','totalMyAchievement','monthAchievement','uid','peroid'));
    }
    public function postDetail(Request $request)
    {
        $data = $request->all();
        $member =$data['member'];
        $peroid = $data['peroid'];
        $uid = $this->mid;
        $begin_end = explode('--', $peroid);
        $begin = $begin_end[0];
        $end  =$begin_end[1];
        $tsBegin =mktime(0, 0, 0, substr($begin, -2), 1, substr($begin, 0, 4));
        $tsEnd =mktime(0, 0, 0, substr($end, -2)+1, 1, substr($end, 0, 4));
        $monthAchievement = array();
        if($member == $this->mid){
            for($i=$begin,$j=0;$i<=$end&&$j<6;$i++,$j++)
            {
                $tsmBegin = mktime(0, 0, 0, substr($i, -2), 1, substr($i, 0, 4));
                $tsmEnd = mktime(0, 0, 0, substr($i+1, -2), 1, substr($i+1, 0, 4));
                $monthAchievement[$j]['month'] = $i;
                $monthAchievement[$j]['my'] = CityPartnerAchievement::where('partner_uid',$member)->where('range','personal')->whereBetween('arrival_at',[$tsmBegin,$tsmEnd])->sum('amount');
                $monthAchievement[$j]['team'] = CityPartnerAchievement::where('partner_uid',$member)->where('range','team')->whereBetween('arrival_at',[$tsmBegin,$tsmEnd])->sum('amount');
            }
            $totalTeamAchievement =CityPartnerAchievement::where('partner_uid',$member)->where('range','team')->whereBetween('arrival_at',[$tsBegin,$tsEnd])->sum('amount');
            $totalMyAchievement =CityPartnerAchievement::where('partner_uid',$member)->where('range','personal')->whereBetween('arrival_at',[$tsBegin,$tsEnd])->sum('amount');
            $members = CityPartner::where('p_uid',$uid)->get();
            $lists = CityPartnerAchievement::whereBetween('arrival_at',[$tsBegin,$tsEnd])->where('range','personal')->where('partner_uid',$member)->paginate(20);
            $name = CityPartner::where('uid',$member)->first()->realname;
            $name = '自己';
            $html = view('citypartner/profit/_part',compact('name','totalMyAchievement','totalTeamAchievement','monthAchievement','begin','end','lists','member','peroid','members','uid'))->render();
            return AjaxCallbackMessage($html,true);
        }
        for($i=$begin,$j=0;$i<=$end&&$j<6;$i++,$j++)
        {
            $tsmBegin = mktime(0, 0, 0, substr($i, -2), 1, substr($i, 0, 4));
            $tsmEnd = mktime(0, 0, 0, substr($i+1, -2), 1, substr($i+1, 0, 4));
            $monthAchievement[$j]['month'] = $i;
            $monthAchievement[$j]['my'] = CityPartnerAchievement::where('partner_uid',$member)->where('range','personal')->whereBetween('arrival_at',[$tsmBegin,$tsmEnd])->sum('amount');
            $monthAchievement[$j]['team'] = CityPartnerAchievement::where('partner_uid',$member)->where('range','team')->whereBetween('arrival_at',[$tsmBegin,$tsmEnd])->sum('amount');
        }
        $totalTeamAchievement =CityPartnerAchievement::where('partner_uid',$member)->where('range','team')->whereBetween('arrival_at',[$tsBegin,$tsEnd])->sum('amount');
        $totalMyAchievement =CityPartnerAchievement::where('partner_uid',$member)->where('range','personal')->whereBetween('arrival_at',[$tsBegin,$tsEnd])->sum('amount');
        $members = CityPartner::where('p_uid',$uid)->get();
        $lists = CityPartnerAchievement::whereBetween('arrival_at',[$tsBegin,$tsEnd])->where('partner_uid',$member)->paginate(20);
        $name = CityPartner::where('uid',$member)->first()->realname;
        $html = view('citypartner/profit/_part',compact('name','totalMyAchievement','totalTeamAchievement','monthAchievement','begin','end','lists','member','peroid','members','uid'))->render();
        return AjaxCallbackMessage($html,true);
    }
    private function doFormatDate($date)
    {
        return str_replace($date,substr($date,0,4).'-'.substr($date,-2),$date);
    }
    private function getProportion($totalAmount)
    {

        if($totalAmount >= 5000000)
        {
            return 33;
        }elseif($totalAmount >= 2500000)
        {
            return 30;
        }elseif($totalAmount >= 1000000)
        {
            return 27;
        }elseif($totalAmount >= 500000)
        {
            return 24;
        }elseif($totalAmount >= 250000)
        {
            return 21;
        }elseif($totalAmount >= 100000)
        {
            return 18;
        }else
        {
            return 15;
        }
    }
    private function getMemberName($member)
    {
        switch($member){
            case '-1':return '我的团队';
            case '-2':return '所有成员';
            case $member==$this->mid:return '自己';
            default:return CityPartner::where('uid',$member)->first()->realname;
        }
    }
}
