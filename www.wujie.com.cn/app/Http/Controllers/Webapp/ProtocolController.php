<?php
namespace App\Http\Controllers\Webapp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use App\Models\Activity\Entity as Activity;
class ProtocolController extends CommonController
{
    //创业基金
    public function getVenture()
    {
        return view('protocol.venture');
    }
    //新手指导
    public function getGuide()
    {
        return view('protocol.guide');
    }
    //积分体系
    public function getIntegral()
    {
        return view('protocol.integral');
    }
    //无界币提取
    public function getWjbtq()
    {
        return view('protocol.wjbtq');
    }
    //了解更多分享机制
    public function getMoreshare()
    {
        return view('protocol.moreshare');

    }
    //邀请好友
    public function getContract()
    {
        return view('protocol.contract');

    }
}
