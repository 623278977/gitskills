<?php namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use App\Models\Agent\AgentAchievementLog;
use \DB;

class AgentTicket extends Model
{
    protected $table = 'agent_ticket';

    protected $dateFormat = 'U';

    /**
     * 黑名单
     */
    protected $guarded = [];


    public static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    public function user()
    {
        return $this->hasOne('App\Models\User\Entity', 'uid', 'uid');
    }

    public function activity()
    {
        return $this->hasOne(\App\Models\Activity\Entity::class, 'id', 'activity_id');
    }

    public function ticket_obj()
    {
        return $this->hasOne('App\Models\Activity\Ticket', 'id', 'ticket_id');
    }

    static function getRow($where)
    {
        return self::where($where)->first();
    }

    static function getRows($where, $page = 1, $pageSize = 10)
    {
        return self::where($where)->skip(($page - 1) * $pageSize)->take($pageSize)->get();
    }

    static function getCount($where)
    {
        return self::where($where)->count();
    }

    /**
     * @param $activity_id
     * @return mixed
     * 一场活动的报名总数
     */
    static function applyCount($activity_id)
    {
        return self::getCount(array(
            'activity_id' => $activity_id,
            'status' => 1
        ));
    }

    /**
     * @param $maker_id
     * @param $activity_id
     * @return mixed
     * 我的ovo下面报名人总数
     */
    static function myMakerApplyCount($maker_id, $activity_id)
    {
        return self::getCount(array(
            'maker_id' => $maker_id,
            'activity_id' => $activity_id,
            'status' => 1
        ));
    }

    /**
     * @param $where
     * @return mixed
     * 根据条件获取门票
     */
    static function getTicketsList($where, $page, $pageSize)
    {
        $query = self::findTicket($where);
        $data = array();
        $query = $query->select(
            'agt.qrcode',
            'agt.ticket_no',
            'agt.status',
            'agt.agent_id',
            'agt.id',
            'a.list_img',
            'a.subject',
            'a.id as activity_id',
            'a.begin_time',
            'a.end_time',
            'm.subject as maker_subject',
            'm.address',
            'm.tel',
            'agt.type',
            'at.price',
            'agt.score_price',
            'agt.is_check',
            'agt.maker_id',
            'z.name as city',
            'z.upid',
            'at.id as aid'
        );


        $n_query = clone $query;

        $starting_query = clone $query;
        $end_query = clone $query;

        //正在发生
        $starting_data = $starting_query
            ->where('a.begin_time', '<', time())
            ->where('a.end_time', '>', time())
            ->orderBy('a.begin_time', 'desc')->orderBy('at.type', 'asc')->orderBy('agt.created_at')
            ->get();
        foreach ($starting_data as $item) {
            $item->group = 'starting';
        }

        //还未开始
        $no_start_data = $n_query
            ->where('a.begin_time', '>=', time())
            ->orderBy('a.begin_time', 'asc')->orderBy('at.type', 'asc')->orderBy('agt.created_at')
            ->get();
        foreach ($no_start_data as $item) {
            $item->group = 'no_start';

        }

        //已经使用或过期
        $end_data = $end_query
            ->where('a.begin_time', '<', time())
            ->where('a.end_time', '<', time())
            ->orderBy('a.begin_time', 'desc')->orderBy('at.type', 'asc')->orderBy('agt.created_at')
            ->get();
        foreach ($end_data as $item) {
            $item->group = 'end';
        }


        //如果是第一页，就把正在进行的和未开始的以及pagesize的过期的票全部返回
        if ($page==1) {
            $end_data = array_slice($end_data, ($page - 1) * $pageSize, $pageSize);
            $tickets = array_merge($starting_data, $no_start_data, $end_data);
        }else{
            $tickets = array_slice($end_data, ($page - 1) * $pageSize, $pageSize);
        }



        foreach ($tickets as $k => $v) {
            $data[$k] = self::getData($v);
            $data[$k]['order_status'] = $v->order_status;
            $data[$k]['tel'] = $v->tel;
            $data[$k]['city'] = $v->city;
            $data[$k]['upid'] = $v->upid;
            $data[$k]['order_lefttime'] = $v->deadline - time();
        }


        return $data;
    }

    /**
     * @param $where
     * 获取票券详细
     */
    static function getDetail($where, $type = 1)
    {
        if ($type == 1) {
            //购买票详情
            $ticket = self::findTicket($where)
                ->join('order as o', 'o.id', '=', 'ut.order_id')
                ->select(
                    'ut.status',
                    'ut.qrcode',
                    'ut.ticket_no',
                    'ut.activity_id',
                    'ut.order_id',
                    'ut.id',
                    'a.list_img',
                    'a.subject',
                    'a.id as activity_id',
                    'a.begin_time',
                    'a.end_time',
                    'm.subject as maker_subject',
                    'm.address',
                    'm.id as maker_id',
                    'at.type',
                    'ut.price',
                    'ut.is_check',
                    'ut.maker_id',
                    'ut.form'
                )
                ->first();
        } else {
            //免费票详情
            $ticket = self::findTicket($where)
                ->select(
                    'ut.status',
                    'ut.qrcode',
                    'ut.ticket_no',
                    'ut.activity_id',
                    'ut.order_id',
                    'ut.id',
                    'a.list_img',
                    'a.subject',
                    'a.id as activity_id',
                    'a.begin_time',
                    'a.end_time',
                    'm.subject as maker_subject',
                    'm.address',
                    'm.id as maker_id',
                    'at.type',
                    'at.price',
                    'ut.is_check',
                    'ut.maker_id',
                    'ut.form'
                )
                ->first();
        }
        $data = self::getData($ticket);
        $data['order_status'] = $ticket->order_status;
        $data['order_lefttime'] = $ticket->deadline - time();

        return $data;
    }

    private static function findTicket($where)
    {
        return DB::table('agent_ticket as agt')->where($where)
            ->where('agt.status', '!=', -4)
            ->leftjoin('activity_ticket as at', 'at.id', '=', 'agt.ticket_id')
            ->leftjoin('activity as a', 'a.id', '=', 'at.activity_id')
            ->leftjoin('maker as m', 'm.id', '=', 'agt.maker_id')
            ->leftjoin('zone as z', 'm.zone_id', '=', 'z.id');
    }

    private static function getData($ticket)
    {
        $data = array();
        if (isset($ticket->group)) {
            $data['group'] = $ticket->group ?: '';

        }
        $data['begin_time_raw'] = $ticket->begin_time;
        $data['end_time_raw'] = $ticket->end_time;
        $data['list_img'] = getImage($ticket->list_img, 'activity', '');
        $data['id'] = $ticket->id;
        $data['activity_id'] = $ticket->activity_id;
        $suffix = isset($ticket->city) ? '-' . \App\Models\Activity\Entity::getCityWithSuffix($ticket->city) : '';

        if ($ticket->type === 1) {
            $data['subject'] = $ticket->subject . $suffix;
        } else {
            $data['subject'] = $ticket->subject;
        }


        $data['begin_time'] = date('Y-m-d H:i', $ticket->begin_time);
        $data['maker_subject'] = $ticket->maker_subject;
        $data['maker_id'] = $ticket->maker_id;
        $data['address'] = $ticket->address;
        $data['is_over'] = $ticket->end_time > time() ? 0 : 1;
        $data['type'] = \App\Models\Activity\Ticket::getType($ticket->type);
        $data['is_check'] = $ticket->is_check;
        $data['ticket_no'] = $ticket->ticket_no;

        $data['is_send'] = $ticket->form == 'order' ? 0 : 1;

        $data['ticket_url'] = self::getTicketUrl($ticket);
        $data['aid'] = $ticket->aid;
        return $data;
    }

    static function getTicketUrl($ticket)
    {
        if ($ticket->status == 1) {
            //已完成
            if ($ticket->type == 2) {
                //现场
                $url = createUrl(
                    'ticket/scene-ticket-detial',
                    array(
                        'id' => $ticket->id,
                        'pagetag' => config('app.activity_detail')
                    )
                );
            } else {
                //直播
                $url = createUrl(
                    'ticket/zb-ticket-detial',
                    array(
                        'id' => $ticket->id,
                        'pagetag' => config('app.activity_detail')
                    )
                );
            }
        } else {
            //未完成
            $url = createUrl(
                'ticket/not-ticket',
                array(
                    'id' => $ticket->id,
                    'pagetag' => config('app.activity_detail')
                )
            );
        }

        return $url;
    }

    static function createQrcode($uid, $ticket_id, $ticket_no, $maker_id)
    {
        $file_name = unique_id() . '.png';
        $value = 'ticket_no=' . $ticket_no;
        return img_create($value, $file_name, config('upload.uploadDir'));
    }


    /**
     * 生成一张会员购票记录
     */
    static function produce($agent_id, $ticket_id, $maker_id, $type, $status = 0, $score_price = 0)
    {
        $ticket = DB::table('activity_ticket')->where('id', $ticket_id)->first();
        $ticket_no = self::ticketNo($maker_id);

        $qrcode_path = self::createQrcode($agent_id, $ticket_id, $ticket_no, $maker_id);

        $user_ticket = self::create(
            [
                'agent_id' => $agent_id,
                'ticket_id' => $ticket_id,
                'ticket_no' => $ticket_no,
                'maker_id' => $maker_id,
                'qrcode' => $qrcode_path,
                'status' => $status,
                'activity_id' => $ticket->activity_id,
                'type' => $type,
                'score_price' => $score_price,
            ]
        );

        return $user_ticket;
    }

    /**
     * 生成不重复的票号
     */
    static function ticketNo($maker_id)
    {
        $nos = self::lists('ticket_no');
        $ticket_no = date('md') . str_pad($maker_id, 4, "0", STR_PAD_LEFT) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        if (in_array($ticket_no, $nos->toArray())) {
            self::ticketNo($maker_id);
        }

        return $ticket_no;

    }

    /*
    * 作用:获取活动分享票
    * 参数:活动ID
    *
    * 返回值:array
    */
    public static function getShareTickets($activity_id)
    {
        return self::where([
            'activity_id' => $activity_id,
            'status' => 1,
        ])->get()->toArray();
    }

    /*
    * 作用:创建分享票
    * 参数:用户ID $uid , 活动ID $activity_id, ovo中心ID $maker_id
    *
    * 返回值:
    */
    public static function createShareTicket($uid, $activity_id, $maker_id, $type)
    {
        $ticketNo = self::ticketNo($maker_id);
        $ticket_id = DB::table('activity_ticket')->where([
            'activity_id' => $activity_id,
            'type' => $type,
        ])->first()->id;
        $qrcode = self::createQrcode(1, 1, 1, $ticketNo, $maker_id);
        //查看是否已经领取过门票了
        $ticket = DB::table('user_ticket')
            ->where([
                'uid' => $uid,
                'activity_id' => $activity_id,
                'maker_id' => $maker_id,
                'status' => 1,
                'type' => $type,
                'form' => 'share'
            ])->first();
        if (is_object($ticket)) {
            //已经领取过门票了
            return $ticket;
        }
        return self::create([
            'uid' => $uid,
            'activity_id' => $activity_id,
            'ticket_id' => $ticket_id,
            'maker_id' => $maker_id,
            'ticket_no' => $ticketNo,
            'qrcode' => $qrcode,
            'type' => $type,
            'status' => 1,
            'form' => 'share',
        ]);
    }


    /**
     * 更新某些字段
     */
    public static function updateBy(Array $where, Array $update)
    {
        $result = self::where($where)->update($update);

        return $result;
    }

    /*
     * 判断指定经纪人在两天内是否有活动要参加
     *
     * */
    public static function isHaveActiveTwoDay($agentId){
        $nowTime = time();
        $isHave = self::whereHas('activity',function($query)use($nowTime){
            $query->where('begin_time','<=',$nowTime + 86400 * 2);
            $query->where('end_time','>',$nowTime);
        })->where('status',1)->where('is_check',0)->where('agent_id',$agentId)->get()->toArray();
        if(count($isHave)){
            return true;
        }
        return false;
    }


}