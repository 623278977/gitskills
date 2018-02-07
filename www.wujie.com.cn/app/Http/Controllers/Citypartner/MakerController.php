<?php
/**
 * 城市合伙人ovo中心   --数据中心 整个弃用  不处理   yaokai
 */

namespace App\Http\Controllers\Citypartner;
use App\Models\Activity\Entity;
use App\Models\Activity\Maker;
use App\Models\CityPartner\Network;
use App\Models\User\Industry;
use App\Models\User\Ticket;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Psy\Exception\RuntimeException;
use App\Models\User\Ticket as UserTicket;

class MakerController extends CommonController
{
    const pageSize=10;
    private $maker;

    public function __construct(Request $request){
        parent::__construct($request);
        $this->beforeFilter(function(){
            $this->maker=\App\Models\Maker\Entity::getRow(array(
                'partner_uid'=>$this->mid
            ));
            $this->userinfo['maker_id']=isset($this->maker->id)?$this->maker->id:0;
            \View::composer('citypartner.layouts.default', function($view)
            {
                $view->with('user', $this->userinfo);
            });
            if(!$this->userinfo['maker_id']){
                return view('citypartner.maker.default');
            }
        });
    }

    /**
     * 我的ovo中心 全部活动
     * 我合办 未合办 activity_maker->maker_id
     * 我创建 activity->partner_uid
     */
    public function getIndex(Request $request){
        $page=$request->input('page')?:1;
        $type=$request->input('type');
        $where='';
        if(!isset($type)){//全部活动
            $where=Entity::getAllactivitywhere($this->mid,$this->userinfo['maker_id']);
        }elseif($type==1){//合办活动
            $activity_id=Maker::where('maker_id',$this->userinfo['maker_id'])->where('status',1)->lists('activity_id')->toArray();
            if(count($activity_id)){
                $activity_id_str=implode(',',$activity_id);
                $where="id in ($activity_id_str)";
            }

        }elseif($type==2){//未合办活动
            $activity_id=Maker::where('maker_id',$this->userinfo['maker_id'])->where('status',0)->lists('activity_id')->toArray();
            if(count($activity_id)){
                $activity_id_str=implode(',',$activity_id);
                $where="id in ($activity_id_str)";
            }
        }elseif($type==3){//我创建的
            $where="partner_uid=$this->mid";
        }
        $data=array();
        if(!$where)
            $where='1!=1';
        $pageSize=12;
        $activitys=Entity::whereRaw($where)->skip(($page-1)*$pageSize)->take($pageSize)->get();
        $total=Entity::whereRaw(Entity::getAllactivitywhere($this->mid,$this->userinfo['maker_id']))->count();
        $activitysCount=Entity::whereRaw($where)->count();
        $totalPage=ceil($activitysCount/$pageSize);
        if(count($activitys)){
            foreach($activitys as $k=>$v){
                $data[$k]=Entity::getBase($v);
                //只显示现场票的价格
                $tickets=array_filter($v->activity_tickets->toArray(), function($value){
                    return $value['type']==1;
                });
                $prices=array_pluck($tickets, 'price');
                $data[$k]['price'] = count($prices) ? (max($prices)?:-1) : -1;
                $data[$k]['detailurl']=Entity::getdetailurl($v,0,0,$type);
                $data[$k]['status']=Entity::getStatus($v,$this->userinfo['maker_id']);
            }
        }
        return view('citypartner.maker.index')
            ->with('type',$type)
            ->with('data',$data)
            ->with('total',$total)
            ->with('activitysCount',$activitysCount)
            ->with('totalPage',$totalPage)
            ->with('currentPage',$page);

    }

    /**
     * @param Request $request
     * 我要合办
     */
    public function postJoint(Request $request){
        $num=$request->input('num');
        $activity_id=$request->input('activity_id');
        if(empty($num)||empty($activity_id))
            return AjaxCallbackMessage('参数有误',false);
        Maker::where('activity_id',$activity_id)
            ->where('maker_id',$this->userinfo['maker_id'])
            ->update(array(
                'status'=>1,
                'num'=>$num
            ));
        return AjaxCallbackMessage('合办状态修改成功',true);
    }

    /**ovo中心会员
     * @param Request $request
     * @return string
     */
    public function postShowpanel(Request $request){
        $activity_id=$request->input('activity_id');
        if(empty($activity_id))
            return AjaxCallbackMessage('参数有误',false);
        $members=\App\Models\Maker\Entity::getMembers(array(
            'maker_member.maker_id'=>$this->userinfo['maker_id']
        ));
        if(count($members)){
            foreach($members as $k=>$v){
                $members[$k]['dealtel']=dealTel($v['username']);
            }
        }
        $activity=Entity::getBase(Entity::where('id',$activity_id)->first());
        $activity['short_url']=$activity['url'].'&makerid='.$this->userinfo['maker_id'];//get_tiny_url()
        $maker=\App\Models\Maker\Entity::getBase($this->maker);
        return AjaxCallbackMessage(compact('members','activity','maker'),true);
    }

    /**
     * @param Request $request
     * @return string
     * 发送合办活动信息给会员
     */
    public function postSendmessage(Request $request){
        $username=$request->input('username');
        $uid=$request->input('uid');
        if(!count($username)||!count($uid))
            return AjaxCallbackMessage('请选择会员',false);
        $message=$request->input('message');
        if(!isset($message))
            return AjaxCallbackMessage('请填写短信内容',false);
//        foreach(\App\Models\User\Entity::find($uid,['nickname','username']) as $item){
//            @SendSMS($item->username,str_replace('{{username}}',$item->nickname,$message),'heban_activity',3);
//        }
//        foreach($username as $k=>$v){
//            @SendSMS($v,$message,'heban_activity',3);
//            $uid=isset($uid[$k])?$uid[$k]:0;
//            $ticket_no=Ticket::ticketNo($this->userinfo['maker_id']);
//            Ticket::create(array(
//                'uid'=>$uid,
//                'order_id'=>0,
//                'ticket_id'=>0,
//                'ticket_no'=>$ticket_no,
//                'status'=>1,
//                'is_check'=>0,
//                'maker_id'=>$this->userinfo['maker_id'],
//                'qrcode'=>Ticket::createQrcode($uid,0,0,$ticket_no,$this->userinfo['maker_id']),
//                'activity_id'=>$request->input('activity_id')
//            ));
//        }
        return AjaxCallbackMessage('发送成功',true);
    }

    /**
     * @param Request $request
     * 活动详情
     id 活动id
     type 1未合办   2未举办  3已结束
     */
    public function getActivitydetail(Request $request){
        $id=$request->input('id');
//        $flag=$request->input('flag');
        $type=$request->input('type'); //全部活动 | 合办的活动 | 未合办的活动 | 创建的活动 分辨从哪里跳转过来的
        $obj=Entity::where('id',$id)->first();
        if(!$obj){
            return redirect('/citypartner/maker/index');
        }
        if($this->userinfo['maker_id'] && Entity::checkJoint($id, $this->userinfo['maker_id'], 1) || $obj->partner_uid==Auth::id()){
            $flag=2;
        }else{
            $flag=1;
        }
        $activity=Entity::getBase($obj);
        //只显示现场票的价格
        $tickets=array_filter($obj->activity_tickets->toArray(), function($value){
            return $value['type']==1;
        });
        $prices=array_pluck($tickets, 'price');
        $activity['price'] = count($prices) ? (max($prices)?:-1) : -1;
        $activity['description']=$obj->description;
        $activity['publisher']=$obj->publisher?$obj->publisher->nickname:($obj->partner?$obj->partner->realname:'');
//        $activity['activity_count']=Ticket::applyCount($obj->id);
        $activity['activity_count']=(int)\App\Models\Activity\Ticket::where(array(
            'activity_id'=>$obj->id,
            'status'=>1,
            'type'=>1,
        ))->pluck('num');
        $myOvoApplyCount=0;
        $total=Entity::whereRaw(Entity::getAllactivitywhere($this->mid,$this->userinfo['maker_id']))->count();
        $status='';
        if($flag==1){
            $status=Entity::getStatus($obj,$this->userinfo['maker_id']);
        }elseif($flag==2||$flag==3){
//            $myOvoApplyCount=Ticket::myMakerApplyCount($this->userinfo['maker_id'],$id);
            $myOvoApplyCount=Ticket::getCount(array(
                'maker_id'=>$this->userinfo['maker_id'],
                'activity_id'=>$id,
                'status'=>1,
                'type'=>1,
            ));
        }
        return view('citypartner.maker.detail')
            ->with('activity',$activity)
            ->with('flag',$flag)
            ->with('type',$type)
            ->with('total',$total)
            ->with('status',$status)
//            ->with('totalPage',ceil($total/self::pageSize))
            ->with('myOvoApplyCount',$myOvoApplyCount)
            ->with('maker_id',$this->userinfo['maker_id']);
    }

    /**
     * @param Request $request
     * ajax 获取报名情况
     */
    public function postAjaxgetapplyusers(Request $request){
        $id=$request->input('id');
        $flag=$request->input('flag');
        $page=$request->input('page')?:1;
//        $total=Entity::getApplyusersCount(array(
//            'user_ticket.activity_id'=>$id,
//            'user_ticket.maker_id'=>$this->userinfo['maker_id'],
//        ));
        $applyusers=[];
        if($flag==2||$flag==3){
//            $applyusers=Entity::getApplyusers(array(
//                'user_ticket.activity_id'=>$id,
//                'user_ticket.maker_id'=>$this->userinfo['maker_id']
//            ),$page,self::pageSize);
            $lists=UserTicket::where(['user_ticket.activity_id'=>$id,'user_ticket.maker_id'=>$this->userinfo['maker_id']])
                    ->leftJoin('activity_sign as sa', function($join){
                        $join->on('sa.uid', '=', 'user_ticket.uid')
                                ->on('sa.ticket_no', '=', 'user_ticket.ticket_no')
                                ->on('sa.activity_id', '=', 'user_ticket.activity_id');
                    })
                    ->with('user')
                    ->where('user_ticket.uid','>','0')
                    ->where('user_ticket.status','=','1')
                    ->where('user_ticket.type','=','1')
                    ->paginate(10,['user_ticket.is_check','user_ticket.created_at as apply_time','user_ticket.uid','user_ticket.id','sa.created_at as sign_time']);
            foreach($lists as $v){
                if(!$v->user){
                    continue;
                }
                $applyusers[]=[
                    'is_check'=>$v->is_check,
                    'nickname'=>$v->user->nickname,
                    'username'=>$v->user->username,
                    'apply_time'=>date('Y.m.d H:i',$v['apply_time']),
                    'sign_time'=>$v['sign_time'] && $v->is_check?date('Y.m.d H:i',$v['sign_time']):'-',
                ];
            }
            $pageHtml=loadPage($lists->lastPage(),$lists->currentPage(),$lists->total());
        }
        return AjaxCallbackMessage(compact('applyusers','pageHtml'),true);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 创建活动页面
     */
    public function getStoreactivity(){
        return view('citypartner.maker.storeactivity');
    }
    /**
     * @param Request $request
     * 发布本地活动
     */
    public function postStoreactivity(Request $request){
        if(Auth::id()!=$request->input('partner_uid'))
            return AjaxCallbackMessage('账号异常',false);
        $activity=$request->only('partner_uid','list_img','subject','begin_time','end_time','description');
        $activity['list_img'] = ltrim($activity['list_img'],'/');
        $activity['begin_time'] = strtotime($activity['begin_time']);
        $activity['end_time'] = strtotime($activity['end_time']);
        $activity['status'] = 1;
        if($activity['begin_time']<time()){//开始时间不能小于当前时间
            return AjaxCallbackMessage('开始时间不能小于当前时间',false);
        }
        if($activity['begin_time']>$activity['end_time']){//开始时间不能小于当前时间
            return AjaxCallbackMessage('开始时间不能大于结束时间',false);
        }
        if(empty($activity['list_img']) || !file_exists(public_path($activity['list_img']))){
            return AjaxCallbackMessage('活动海报图片必填',false);
        }
        if(empty($activity['subject'])){
            return AjaxCallbackMessage('活动名称不能为空',false);
        }
        if(empty($activity['description'])){
            return AjaxCallbackMessage('活动详情不能为空',false);
        }
        if($request->input('num')<0){
            return AjaxCallbackMessage('门票人数必填',false);
        }
        $obj=Entity::create($activity);
        //写入主会场数据
        Maker::create([
            'activity_id'=>$obj->getKey(),
            'maker_id'=>  $this->userinfo['maker_id'],
            'type'=>'organizer',
            'status'=>'1'
        ]);
        $activity_ticket=$request->only('num','price','intro','remark');
        $activity_ticket['activity_id']=$obj->id;
        \App\Models\Activity\Ticket::create($activity_ticket);
        return AjaxCallbackMessage('活动发布成功',true,'/citypartner/maker/index?type=3');
    }

    /**
     * @param Request $request
     * @return mixed
     * 我的会员
     */
    public function getMember(Request $request)
    {
        $page=$request->input('page')?:1;
        if(!$this->maker){
            return redirect('/citypartner/public/index');
        }
        $memberCount=count($this->maker->users);
        $data=array();
        $users=array_slice($this->maker->users->toArray(), ($page-1)*self::pageSize,self::pageSize);
        $objs=$this->maker->users;
        if($memberCount){
            foreach($users as $k=>$v){
                $data[$k]['uid']=$v['uid'];
                $data[$k]['nickname']=$v['nickname'];
                $data[$k]['username']=$v['username'];
                $data[$k]['gender']=\App\Models\User\Entity::getGender($v['gender']);
                $data[$k]['industry']=implode(' ',Industry::getUserIndustry($objs[$k],'name'));
            }
        }
        $total=Entity::whereRaw(Entity::getAllactivitywhere($this->mid,$this->userinfo['maker_id']))->count();
        $pageHtml=loadPage(ceil($memberCount/self::pageSize),$page,$memberCount);
        return view('citypartner.maker.member')
            ->with('data',$data)
            ->with('memberCount',$memberCount)
            ->with('total',$total)
            ->with('pageHtml',$pageHtml);
    }

    /**
     * @param Request $request
     * @return string
     * ajax 获取会员列表
     */
    public function postAjaxgetusertickets(Request $request){
        $page=$request->input('page')?:1;
        $uid=$request->input('uid');
        $userTickets=Ticket::getRows(array(
            'uid'=>$uid,
            'status'=>1
        ),$page,self::pageSize);
        $applyCount=Ticket::getCount(array(
            'uid'=>$uid,
            'status'=>1
        ));
        $data=array();
        if(count($userTickets)){
            foreach($userTickets as $k=>$v){
                $data[$k]['created_at']=date('Y.m.d H:i',$v->created_at);
                $data[$k]['price']=(isset($v->ticket_obj->price)&&$v->ticket_obj->price)?$v->ticket_obj->price:'免费';
                $activity=DB::table('activity')->where(array(
                    'id'=>$v->activity_id
                ))->first();
                $data[$k]['subject']=isset($activity->subject)?$activity->subject:'';
            }
        }
        $pageHtml=loadPage(ceil($applyCount/self::pageSize),$page,$applyCount,true);
        return AjaxCallbackMessage(array(
            'users'=>$data,
            'pageHtml'=>$pageHtml
        ),true);
    }
    /**
     * @param Request $request
     * 会员详情页
     */
    public function getMemberdetail(Request $request){
        $uid=$request->input('uid');
        $total=Entity::whereRaw(Entity::getAllactivitywhere($this->mid,$this->userinfo['maker_id']))->count();
        $obj=\App\Models\User\Entity::getRow(array('uid'=>$uid));
        $user=\App\Models\User\Entity::getUser($obj);
        $is_maker=\App\Models\Maker\Entity::where('uid','=',$uid)->where('status','=','1')->count();
        $user['industry']=implode(' ',Industry::getUserIndustry($obj,'name'));
        $user['zone']=Zone::getZone($obj->zone_id);
        $applyCount=Ticket::getCount(array(
            'uid'=>$user['uid'],
            'status'=>1
        ));
        $price=Ticket::where(array(
            'uid'=>$uid,
            'status'=>1
        ))->sum('price');
        $lists=Ticket::where(array(
                    'uid' => $uid,
                    'status' => 1
                ))->with('activity')
                ->orderBy('created_at','desc')
                ->paginate(15)
                ->appends($request->all());
        return view('citypartner.maker.memberdetail')
            ->with('user',$user)
            ->with('lists',$lists)
            ->with('is_maker',$is_maker)
            ->with('applyCount',$applyCount)
            ->with('price',$price)
            ->with('total',$total);
    }

    /**
     * @param Request $request
     * @return mixed
     * 我的网点
     */
    public function getNetwork(Request $request){
        $total=Entity::whereRaw(Entity::getAllactivitywhere($this->mid,$this->userinfo['maker_id']))->count();
        $obj=Network::where('id',$this->userinfo['network_id'])->first();

        return view('citypartner.maker.network')
            ->with('user',$this->userinfo)
            ->with('data',$obj)
            ->with('total',$total);
    }
}
