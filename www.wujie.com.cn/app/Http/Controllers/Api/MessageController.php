<?php
/**
 * 消息中心控制器
 */
namespace App\Http\Controllers\Api;

use App\Http\Requests\Agent\CustomerRequest;
use App\Models\Activity\Entity;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\Invitation;
use App\Models\AgentScore;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Message;
use App\Http\Controllers\Api\CommentController;
use App\Models\Zone;
use App\Models\Comment\Entity as Comment;
use App\Models\Orders\Items as OrdersItems;
use DB;

class MessageController extends CommonController
{
    /**
     * 消息中心
     * @param Request $request
     * @return string
     */
    public function postIndex(Request $request)
    {
        $uid = isset($uid) ? $uid : $request->input('uid');
        $uid = (int) $uid;
        $time = time();
        $myMessage = DB::select("
SELECT
    count(t.content) AS count,
	t.*
FROM
	(
		SELECT
			content,
			type,
			created_at,
			title
		FROM
			`lab_my_message`
		WHERE
			`uid` = $uid
		AND `is_read` = 0
		AND `send_time` < $time
		AND `type` IN (1, 2, 3 ,7 ,8 ,9)
		ORDER BY
			`send_time` DESC
	) t
GROUP BY
	`type`
        ");


        //转化type=7,8为type=1,并且格式化content
        $data = $this->toTypeOne($myMessage);

        //未读取的评论
        $unread = self::getComments($uid);

        //无该类型消息,返回空消息
        $unExistType = self::formatReturn($data);
        array_push($data, $unread);
        $data = array_merge($unExistType, $data);

        foreach ($data as &$item) {
            $item['created_at'] = date('H:i:s', $item['created_at']);
            //$item['type'] = self::getTypeName($item['type']);
            $item['content'] = self::formatType($item);
            if ($item['type'] == 1) $item['title'] = $item['content'];
        }
        $data = collect($data)->sortBy('type');

        return AjaxCallbackMessage($data, true);
    }

    /**
     * 将type=7或type=8的消息转为type=1类型
     * @param $rawMessage
     * @return array
     */
    private function toTypeOne(&$rawMessage){
        if(empty($rawMessage)) return [];
        foreach($rawMessage as $item){
            if($item->type == 7){
                $item->content = json_decode($item->content)->term_name;
                $message[] = $item;
            }
            if($item->type == 8){
                $item->content = unserialize($item->content)->name;
                $message[] = $item;
            }
            if($item->type == 1){
                $message[] = $item;
            }
            if($item->type == 9){
                $rawContent = unserialize($item->content);
                $title = '';
                if($rawContent['type'] == 1){
                    $title = '专版下有直播预告';
                }elseif($rawContent['type'] == 2){
                    $title = '专版下直播马上开始';
                }elseif($rawContent['type'] == 3){
                    $title = '专版下有新活动发布';
                }
                $item->content = empty($item->title) ? $title:$item->title;
            }
            if($item->type == 2){
                $item->content = $item->title;
            }
        }
        $myMessage = objToArray($rawMessage);
        if(empty($message)){
            return $myMessage;
        }
        //数组排序
        $message = arraySort('created_at',objToArray($message),'SORT_DESC');
        $count = 0;
        foreach($message as $k){
            $count += $k['count'];
        }
        foreach($myMessage as &$obj){
            //没有官方消息的情况
            if(!in_array($obj['type'],[1])){
                $message[0]['count'] = $count;
                $message[0]['type'] = 1;
                $myMessage[] = $message[0];
                break;
            }
            //有官方消息的情况
            if($obj['type'] == 1){
                $obj['count'] = $count;
                $obj['content'] = $message[0]['content'];
                $obj['created_at'] = $message[0]['created_at'];
                $obj['title'] = $message[0]['title'];
                break;
            }
        }
        foreach($myMessage as $item){
            if(!in_array($item['type'],[7,8])){
                $data[] = $item;
            }
        }
        return $data;
    }

    /**
     * 消息类型
     * @param $item
     * @return int|string
     */
    private function getTypeName($item)
    {
        $types = [
            '官方通知' => 1,
            '活动消息' => 2,
            '直播消息' => 3,
            '评论回复' => 4,
            '会员消息' => 9,
        ];
        foreach ($types as $code => $type) {
            if ($item == $type) return $code;
        }
    }

    /**
     * 无1:官方通知 2:活动提醒 3:直播提醒新消息返回空
     * @param $message
     * @return array
     */
    private function formatReturn($message)
    {
        $param = $messageType = [];
        $types = [1, 2, 3 ,9];//1:官方通知 2:活动提醒 3:直播提醒 9:会员消息
        foreach ($message as $item) {
            $messageType[] = $item['type'];
        }
        $unExistType = array_diff($types, $messageType);
        foreach ($unExistType as $item) {
            $param[] = [
                'count' => 0,
                'content' => '暂无新的'.$this->getTypeName($item),
                'type' => $item,
                'created_at' => time(),
                'title' => '',
            ];
        }
        return $param;
    }

    /**
     * 未读取的评论
     * @param $uid
     * @return array
     */
    private function getComments($uid)
    {
        $count = Message::where('uid', $uid)
            ->where('is_read', 0)
            ->whereIn('type', [4, 5, 6])
            ->get()
            ->count();
        $param = [
            'count' => isset($count) ? $count : 0,
            'content' => $count ? "有{$count}条新的评论回复等你查看" : '暂无新的评论回复',
            'type' => 4,
            'created_at' => time(),
            'title' => '评论回复',
        ];
        return $param;
    }

    /**
     * 格式化不同类型的消息内容
     * @param $item
     * @return string
     */
    private function formatType($item)
    {
//        dd($item);
        if ($item['count'] >= 1) {
            if ($item['type'] == 1) {
                $item['content'] = "有{$item['count']}条新的通知,赶快来查看吧";
            }
            if ($item['type'] == 3) {
                $item['content'] = $item['content'];
            }
            if ($item['type'] == 2) {
                $item['content'] = $item['content'];
            }
            if ($item['type'] == 9) {
                $item['content'] = $item['content'];
            }
        }
        return $item['content'];
    }

    /**
     * 读取消息
     * @param Request $request
     * @return string
     */
    public function postReadmessage(Request $request)
    {
        $uid = isset($uid) ? $uid : $request->input('uid');
        $type = $request->input('type');

        if(!$type){
            $res = Message::where('uid', $uid)
                ->where('send_time','<',time())
                ->update(['is_read' => 1]);
        }else{
            if(!is_array($type)){
                $type = [$type];
            }
            if($type == [4]) $type = [4,5,6];
            if($type == [1]) $type = [1,7,8];
            $res = Message::whereIn('type', $type)
                ->where('uid', $uid)
                ->where('send_time','<',time())
                ->update(['is_read' => 1]);
        }

        if ($res) return AjaxCallbackMessage('success', true);
        return AjaxCallbackMessage('fail', false);
    }

    /**
     * 官方消息
     * @param Request $request
     * @return string
     */
    public function postOfficialmessage(Request $request ,$version = null)
    {
        $uid = isset($uid) ? $uid : $request->input('uid');
        $page = $request->input('page', 1);
        $pageSize = $request->input('page_size', 15);
        $officialMessageBuilder = DB::table('my_message')
            ->where('uid', $uid)
            ->whereIn('type', [1,7,8])
            ->where('send_time', '<', time())
            //->where('title','not like','%入驻%')
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->select('id', 'title', 'content', 'created_at','type','post_id','send_time');

        $totalBuilder = DB::table('my_message')
            ->where('uid', $uid)
            ->whereIn('type', [1,7,8])
            ->where('send_time', '<', time())
            ->orderBy('created_at', 'desc');

        if(in_array($version,['_v020500'])){
            $officialMessageBuilder = $officialMessageBuilder
                ->where('title','not like','%入驻%');
            $totalBuilder = $totalBuilder
                ->where('title','not like','%入驻%');
        }

        $officialMessage = $officialMessageBuilder->get();

        $total = $totalBuilder->count();


        foreach ($officialMessage as $item) {
            //标记消息已读
            $item->is_read = 1;
            \DB::table('my_message')->where('id',$item->id)->where('is_read',1)->first();

            $item->created_at = date('Y-m-d H:i', $item->send_time);
            if ($item->type == 7) {
                //专版购买成功
                $message = json_decode($item->content, true);
                $item->vip_time         = $message['period'];
                $item->term_name        = $message['term_name'];
                $item->vip_name         = $message['vip_name'];
                $item->deadline         = $message['expire_time'];
                $item->vip_id           = $message['vip_id'];
                unset($item->content, $item->post_id, $item->title,$item->send_time);
            } elseif ($item->type == 8) {
                //专版过期消息
                $message = unserialize($item->content);
                $post_id = $item->post_id;
                //获取续费记录
                $new = self::getVipRenew($message);
                //判断是否续费过
                $hasRenewed  =  $new->end_time > $message->max_end_time ? 1 : 0;
                $available_time                 = ceil(($message->max_end_time - time()) / 86400);
                //如果续费过时间写死
                if($hasRenewed){
                    $available_time             = ceil(($message->max_end_time - $new->created_at) / 86400);
                }
                $item->vip_name                 = $message->name;
                $item->deadline                 = date('Y-m-d', $message->max_end_time);
                $item->available_time           = $available_time > 0 ? '(还剩' . $available_time . '天)' : '(已过期)';
                $item->vip_id                   = $message->id;
                $item->is_over                  = $available_time >= 0 ? 0 : 1;
                unset($item->content, $item->post_id, $item->title,$item->send_time);
            }
        }
        if (empty($officialMessage)) return self::AjaxCallbackMessage2($officialMessage, $total,false);
        return self::AjaxCallbackMessage2($officialMessage, $total,true);
    }

    function AjaxCallbackMessage2($msg = '', $total = 0 ,$code = true, $forwardUrl = '') {
        $array = array(
            "message" => $msg,
            "total" => $total,
            "status" => $code,
            "forwardUrl" => $forwardUrl
        );
        return  json_encode($array);
    }

    /**
     * 格式化门票类型
     * @param $type
     * @return mixed
     */
    private function humenType($type)
    {
        $param = [
            0 => '免费票',
            1 => '现场票',
            2 => '直播票',
            3 => 'vip票',
        ];
        foreach ($param as $code => $value) {
            if ($type == $code) return $value;
        }
    }

    /**
     * 评论回复列表
     * @param Request $request
     * @return string
     */
    public function postMessagelist(Request $request)
    {
        $uid = isset($uid) ? $uid : $request->input('uid');
        $page = $request->input('page', 0);
        $pageSize = $request->input('page_size', 10);
        $data = DB::table('my_message as mm')
            ->leftjoin('user as u', 'mm.reply_uid', '=', 'u.uid')
            ->where('mm.uid', $uid)
            ->whereIn('mm.type', [4, 5, 6])
            ->orderBy('mm.created_at', 'desc')
            ->skip($page * $pageSize)
            ->take($pageSize)
            ->select('mm.title', 'mm.content', 'mm.created_at', 'u.nickname', 'u.avatar', 'u.uid','mm.url','mm.post_id')
            ->get();
        foreach ($data as $item) {
            $item->created_at = date('Y', $item->created_at) . '年' . date('m', $item->created_at) . '月' . date('d', $item->created_at) . '日';
            $item->avatar = getImage($item->avatar, 'avatar');
        }


        return AjaxCallbackMessage($data, true);
    }

    /**
     * 未读消息数
     * @param Request $request
     * @return string
     */
    public function postUnreadcounts(Request $request)
    {
        $uid = isset($uid) ? $uid : $request->input('uid');
        $data = Message::where('uid', $uid)
            ->where('is_read', 0)
            ->where('send_time', '<', time())
            ->whereIn('type', [1, 2, 3 ,7 ,8, 9])
            ->count();
        //未读取的评论
        $unread = self::getComments($uid);
        $data = (int)$data + $unread['count'];
        return AjaxCallbackMessage($data, true);
    }

    /**
     * 生成系统信息
     * @param $uid
     * @param $title
     * @param $content
     * @param $ext
     * @param $end
     * @param $type
     * @param $delay
     */
    public function createMessage($uid, $title, $content, $ext, $end, $type, $delay = 0, $post_id = 0)
    {
        Message::create(array(
            'uid' => $uid,
            'title' => $title,
            'type' => $type,
            'content' => '<section>
	   	  <div>' . $content . '</div>
	   <div>' . $ext . '</div>
	   <div>' . $end . '</div>
        </section>',
            'created_at' => time(),
            'updated_at' => time(),
            'send_time' => time() + $delay,
            'post_id' => $post_id
        ));
    }

    /**
     * 直播提醒
     * @param Request $request
     * @return string
     */
    public function postLiveremind(Request $request)
    {
        $uid = $request->input('uid', '');
        $page = $request->input('page', 1);
        $pageSize = $request->input('page_size', 15);
        $data = Message::LiveMessage($uid, $page, $pageSize);
        $data = objToArray($data);
        $data = mult_unique($data);
//        $recommendList = Message::recommendList($uid);
//        if ($recommendList) {
//            $data['recommend'] = $recommendList;
//        }
        return AjaxCallbackMessage($data, true);
    }

    /**
     * 活动提醒
     * @param Request $request
     * @return string
     */
    public function postActivityremind(Request $request)
    {
        $uid = $request->input('uid', '');
        $page = $request->input('page', 1);
        $pageSize = $request->input('page_size', 10);
        $activityMessage = Message::activityMessage($uid, $page, $pageSize);
        foreach ($activityMessage as $item) {
            $item->is_over = 0;
            $item->created_at = date('Y-m-d', $item->created_at);
            $item->begin_time = date('m/d H:i:s', $item->begin_time);
            $item->list_img = getImage($item->list_img);
            $item->type = self::humenType($item->type);
            $item->zone_id = Zone::getZone($item->zone_id);
            if ($item->title == '抱歉,你报名的活动被临时取消') {
                $item->is_over = 1;
            }
        }
        $activityMessage = self::formatMessage([], $activityMessage);
//        $organizerMessage = Message::organizerMessage($uid);
//        foreach ($organizerMessage as $item) {
//            $item->created_at = date('Y-m-d', $item->created_at);
//            $item->begin_time = date('m/d', $item->begin_time);
//            $item->list_img = getImage($item->list_img);
//            $item->type = self::humenType($item->type);
//        }
//        $organizerMessage = self::formatMessage(['title', 'content', 'created_at'], $organizerMessage);
//        $ovoMessage = Message::ovoMessage($uid);
//        foreach ($ovoMessage as $item) {
//            $item->created_at = date('Y-m-d', $item->created_at);
//            $item->begin_time = date('m/d', $item->begin_time);
//            $item->list_img = getImage($item->list_img);
//            //$item->type = self::humenType($item->type);
//        }
//        $ovoMessage = self::formatMessage(['title', 'content', 'created_at'], $ovoMessage);
//        foreach ($activityMessage as $activity) {
//            $return[] = $activity;
//        }
//        $return['organizer'] = $organizerMessage;
//        $return['ovo'] = $ovoMessage;
        return AjaxCallbackMessage($activityMessage, true);
    }

    /*
    * 作用:获取会员消息
    * 参数:
    * 
    * 返回值:
    */
    public function postVipremind(Request $request)
    {
        $uid            = $request->get('uid','');
        $page           = $request->get('page',1);
        $pageSize       = $request->get('pageSize',15);

        if($uid == ''){
            return AjaxCallbackMessage('用户ID必须', false);
        }
        $responseData =Message::getVipMessages($uid,$pageSize);

        return AjaxCallbackMessage($responseData, true);
    }
    /**
     * 格式化消息
     * @param $keys
     * @param $data
     * @return array
     */
    private function formatMessage($keys, $data)
    {
        $data = objToArray($data);
        $return = [];
        foreach ($data as $k => $item) {
            foreach ($keys as $key) {
                if (array_key_exists("$key", $item)) {
                    $return[$key] = $item[$key];
                    unset($item[$key]);
                }
            }
            $return[$k] = $item;
        }
        return $return;
    }

    /*
    * 作用:活动提醒消息
    * 参数:
    * 
    * 返回值:
    */
    public function postActivityremindmessages(Request $request , $version = null)
    {
        //参数校验
        $uid            = $request->get('uid','');
        $page           = $request->get('page',1);
        $pageSize       = $request->get('pageSize',15);
        if($uid == ''){
            return AjaxCallbackMessage('用户uid必须',false);
        }
        //获取活动提醒消息
        $responseData = Message::getMessagesOfActivityRemind($uid, $page, $pageSize ,$version);
        //返回
        return AjaxCallbackMessage($responseData,true);
    }

    /*
    * 作用:直播提醒消息
    * 参数:
    * 
    * 返回值:
    */
    public function postLiveremindmessages(Request $request, $version = null)
    {
        //参数校验
        $uid            = $request->get('uid','');
        $page           = $request->get('page','');
        $pageSize       = $request->get('pageSize',15);
        if($uid == ''){
            return AjaxCallbackMessage('用户uid必须',false);
        }
        //获取的直播提醒消息
        $responseData = Message::getMessagesOfLiveRemind($uid, $page, $pageSize ,$version);

        return AjaxCallbackMessage($responseData, true);
    }
    
    /*
    * 作用:获取最新的续费记录
    * 参数:
    * 
    * 返回值:
    */
    public static function getVipRenew($message)
    {
//        //获取老的缴费记录
//        $old = \DB::table('user_vip')->where('id',$id)->first();
        //获取最新的续费记录
        $new = \DB::table('user_vip')->where('uid',$message->uid)->where('vip_id',$message->id)->where('end_time','>=',$message->max_end_time)->orderBy('end_time','desc')->first();
        return $new;
    }

    /**
     * 查询是否对某经纪人公开手机号
     *
     * @param CustomerRequest $request
     * @param null $version
     * @return string
     */
    public function postIfPublicMobile(CustomerRequest $request, $version = null)
    {
        $results = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result         = $versionService->bootstrap($results, ['request' => $request]);

            return AjaxCallbackMessage($result['message'], $result['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * 向经纪人公开手机号
     * @param CustomerRequest $request
     * @return string
     */
    public function postPublicMobile(CustomerRequest $request, $version = null)
    {
        $results = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result         = $versionService->bootstrap($results, ['request' => $request]);

            return AjaxCallbackMessage($result['message'], $result['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * 初始化显示经纪人和品牌的数据信息
     *
     * @internal param $agent_id     经纪人ID
     * @internal param $customer_id  客户ID
     * @paeam    $brand_id           品牌ID
     *
     * @param Request $request
     * @param null $version
     * @return data_list
     */
    public function postAgentInfoInitializeShow(Request $request, $version = null)
    {
        $results = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result         = $versionService->bootstrap($results, ['request' => $request]);

            return AjaxCallbackMessage($result['message'], $result['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * 对品牌和经纪人进行评价
     *
     * @param Requests\CustomerAgentRequest $request
     *
     * @return string
     */
    public function postAddComment(Requests\CustomerAgentRequest $request, $version = null)
    {
        $results = $request->input();
        if ($version) {
             $versionService = $this->init(__METHOD__, $version);
             $result         = $versionService->bootstrap($results, ['request' => $request]);

             return AjaxCallbackMessage($result['message'], $result['status']);
         } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * 查询客户对经纪人和品牌的评价
     *
     * @internal param $agent_id     经纪人ID
     * @internal param $customer_id  客户ID
     * @paeam    $brand_id           品牌ID
     *
     * @param CustomerRequest $request
     * @param null $version
     * @return data_list
     */
    public function postComment(CustomerRequest $request, $version = null)
    {
        $results = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result         = $versionService->bootstrap($results, ['request' => $request]);

            return AjaxCallbackMessage($result['message'], $result['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * 客户--多动作操作（活动邀请、考察邀请，合同）zhaoyf
     *
     * @param Requests\NoticeRequest|Request $request
     * @param null $version
     * @return bool
     * @internal param   $invite_id 邀请函ID
     * @string   param   $remark    评价内容
     *
     */
    public function postMultipleAction(Requests\NoticeRequest $request, $version = null)
    {
        $results = $request->input();
        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result         = $versionService->bootstrap($results, ['request' => $request]);

            return AjaxCallbackMessage($result['message'], $result['status']);
        } else {
            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     *  author zhaoyf
     *
     * 邀请函动作（拒绝 | 接受；活动直接为拒绝）
     * @param   Requests\MessageRequest|Request $request
     * @param   null $version
     * @return  data_list
     */
    public function postInviteAction(Requests\MessageRequest $request, $version = null)
    {
        $results = $request->input();

        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result         = $versionService->bootstrap($results, ['request' => $request]);

            return AjaxCallbackMessage($result['message'], $result['status']);
        } else {

            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

    /**
     * author zhaoyf
     *
     * 发送融云消息--发送品牌
     *
     * @param CustomerRequest|Request $request
     * @param null $version
     * @return string
     */
    public function postSendRongBrandInfo(CustomerRequest $request, $version = null)
    {
        $results = $request->input();

        if ($version) {
            $versionService = $this->init(__METHOD__, $version);
            $result         = $versionService->bootstrap($results, ['request' => $request]);

            return AjaxCallbackMessage($result['message'], $result['status']);
        } else {

            return AjaxCallbackMessage('该接口不存在', false);
        }
    }

}
