<?php
/**
 * 对接申请池控制器
 */
namespace App\Http\Controllers\Api;

use App\Models\User\Entity;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User\Apply;
use App\Models\Activity\Organizer;
use App\Http\Controllers\Api\CommonController;
use DB, Auth;
use App\Models\ScoreLog;


class UserApplyController extends CommonController
{
    /**
     * 对接申请池
     * @param Request $request
     * @return string
     */
    public function postIndex(Request $request)
    {
        $uid = isset($uid) ? $uid : $request->input('uid');
        $page = $request->input('page', 1);
        $pageSize = $request->input('page_size', 10);
        //DB::connection()->enableQueryLog(); // 开启查询日志
        $data = DB::table('user_apply as ua')
            ->join('user as u', 'ua.uid', '=', 'u.uid')
            ->join('maker as m', 'ua.maker_id', '=', 'm.id')
            ->join('opportunity as o', 'ua.opportunity_id', '=', 'o.id')
            ->leftjoin('admin as a', 'ua.admin_uid', '=', 'a.id')
            ->where('ua.uid', $uid)
            ->orderBy('ua.created_at', 'desc')
            ->skip(($page-1) * $pageSize)
            ->take($pageSize)
            ->select('ua.id', 'ua.type', 'u.nickname as realname', 'u.username', 'm.subject', 'ua.company_name', 'ua.content', 'ua.remark', 'ua.created_at', 'ua.can_send', 'ua.status', 'ua.updated_at', 'o.subject as investinfo', 'a.nickname', 'a.avatar as logopath')
            ->get();
        //$queries = DB::getQueryLog(); // 获取查询日志
        //dd($queries); // 即可查看执行的sql，传入的参数等等
        if (empty($data)) return AjaxCallbackMessage($data, true);
        foreach ($data as $obj) {
            $obj->content = strip_tags($obj->content);
            $obj->logopath = getImage($obj->logopath, 'avatar');
            $obj->updated_at = $obj->status == 1 ? '' : date('Y-m-d H:i:s', $obj->updated_at);
            if ($obj->status == 2) {
                $obj->remind = '已提醒';
            } else if ($obj->can_send == 1 && $obj->status == 1) {
                //12小时后提醒
                if ((time() - $obj->created_at) > 12 * 60 * 60) {
                    $obj->can_send = 1;
                    $obj->remind = '提醒答复';
                } else {
                    $obj->remind = '';
                    $obj->can_send = -1;
                }
            } else if ($obj->can_send == 0 && ($obj->status == 3 || $obj->status == 1)) {
                $obj->remind = '';
            }
            if (!$obj->remark) {
                $obj->remark = '';
            }
            $obj->created_at = date('Y-m-d H:i:s', $obj->created_at);
            $obj->type = 'OVO跨域对接申请';//暂时先写死
        }
        return AjaxCallbackMessage($data, true);
    }


    /**
     * 发送提醒
     * @param Request $request
     * @return string
     */
    public function postRemindreply(Request $request)
    {
        $uid = isset($uid) ? $uid : $request->input('uid');
        $applyId = $request->input('id');
        $obj = Apply::where('id', $applyId)->where('uid', $uid)->where('status', 1)->first();
        //可以发送提醒
        if ($obj->can_send == 1) {
            $param = [
                'uid' => $uid,
                'can_send' => 0,
                'status' => 2,
            ];
            $res = Apply::where('id', $applyId)->update($param);
            if ($res) return AjaxCallbackMessage('发送成功', true);
        }
        return AjaxCallbackMessage('发送失败', false);
    }

    /**
     *跨域对接申请
     */
    public function postStore(Request $request)
    {
        $uid = $request->input('uid');
        if (!Entity::checkAuth($uid))
            return AjaxCallbackMessage('账号异常', false);
        $nickname = $request->input('nickname');
        if (empty($nickname))
            return AjaxCallbackMessage('请完善姓名', false);
        $tel = $request->input('tel');
        if (empty($tel) || !checkMobile($tel))
            return AjaxCallbackMessage('手机号格式有误', false);
        $opportunity_id = $request->input('opportunity_id');
        if (empty($opportunity_id))
            return AjaxCallbackMessage('商机信息有误', false);
        //if (Apply::getCount(array('tel' => $tel, 'opportunity_id' => $opportunity_id)))
        if (Apply::where(['tel' => $tel, 'opportunity_id' => $opportunity_id])->where('created_at','>',time()-86400)->count())
            return AjaxCallbackMessage('24小时内请勿重复申请', false);
        $maker_id = $request->input('maker_id');
        if (empty($maker_id))
            return AjaxCallbackMessage('请完善ovo中心信息', false);
        $company_name = $request->input('company_name');
        $remark = $request->input('remark', '未填写相关备注');
        $apply = Apply::create(compact('uid', 'nickname', 'tel', 'maker_id', 'opportunity_id', 'company_name', 'remark'));

        //这个接口已经不用了，所以这里不加发送国际短信逻辑， todo 但是需要确认 2017-06-06
        @SendTemplateSMS('ovouserapply',$tel,'ovouserapply');
        $opp_obj = DB::table('opportunity')->where('id', $opportunity_id)->first();
        $maker_obj = DB::table('maker')->where('id', $maker_id)->first();
        createMessage(
            $uid,
            $title = '你的跨域对接申请已成功提交',
            $content = '你的跨域对接申请已经提交至后台，之后会有运营人员与你取得联系。届时请保持联系方式的畅通。',
            $m_ext = '<div>
	   	            <p>对接对象：<span><a style="color:#1e8cd4" href="'."https://".$_SERVER['HTTP_HOST']."/webapp/business/goverment?id={$opportunity_id}&uid={$uid}&pagetag=12-2".'">' . $opp_obj->subject . '</a></span></p>
	   	            <p>姓名：<span>' . $nickname . '</a></span></p>
	   	            <p>手机：<span>' . $tel . '</a></span></p>
	   	            <p>入驻OVO中心：<span>' . $maker_obj->subject . '</a></span></p>
	   	            <p>公司：<span>' . $company_name . '</a></span></p>
	   	            <p>备注信息：<span>' . $remark . '</span></p>
	                </div>
	                <div></div>',
            $end = '',
            $type = 1,
            $delay = 60
        );

        return AjaxCallbackMessage('申请对接成功', true);
    }
}
