<?php
/**用户模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\User;
use App\Http\Libs\Helper_Huanxin;
use App\Models\Activity\Sign;
use App\Models\Agent\ContractPayLog;
use App\Models\Brand\BrandContract;
use App\Models\Brand\Consult;
use App\Models\Brand\Payinfo;
use App\Models\LoginLog;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use App\Models\Agent\AgentCustomer;
use App\Models\Agent\Agent;
use App\Models\Agent\Invitation;
use App\Models\Agent\AgentCustomerLog;
use App\Models\Orders\Entity as Orders;
use App\Models\Order\Entity as Order;
use App\Models\User\Industry as UserIndustry;
use App\Models\Zone\Entity as Zone;
use App\Models\Contract\Contract;
use Illuminate\Support\Facades\Request;

class Entity extends Model implements AuthenticatableContract, CanResetPasswordContract{
    use Authenticatable, CanResetPassword;
    protected function getDateFormat()
    {
        return date(time());
    }

    protected $table = 'user';

    protected $primaryKey = 'uid';

    protected $guarded = [];

    /**
     * 投资意向说明
     * -1：近期无加盟意向
     * 0：未知
     * 1：以观望为主
     * 2：近期有加盟意向
     */
    public static $IFIntention = [
        '-1' => '近期无加盟意向',
        '0'  => '',
        '1'  => '以观望为主',
        '2'  => '近期有加盟意向'
    ];


    public static  $instance = null;
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *对应多个行业
     */
    public function industrys()
    {
        return $this->hasMany('App\Models\User\Industry','uid');
    }
    public function business_card()
    {
        return $this->hasOne('App\Models\User\BusinessCard', 'uid', 'uid');
    }
    //地区
    public function zone()
    {
        return $this->hasOne('App\Models\Zone\Entity', 'id', 'zone_id');
    }

    //关联经纪人日志表
    public function agent_customer_log(){
        return $this->hasMany(AgentCustomerLog::class,'uid','uid');
    }

    //关联经纪人投资人表
    public function agent_customer(){
        return $this->hasMany(AgentCustomer::class,'uid','uid');
    }

    /**
     * author zhaoyf
     *
     * 关联：分类表
     */
    public function  hasManyCategory()
    {
        return $this->hasMany(UserFondCate::class, 'uid','uid');
    }

    //关联合同表
    public function contract(){
        return $this->hasMany(Contract::class,'uid','uid');
    }
    //关联品牌咨询表
    public function brand_consult()
    {
        return $this->hasMany(Consult::class,'uid','uid');
    }
    /*
     *  /**
     * author zhaoyf
     *
     * 关联：分类表
     */
   /* public function  hasManyCategory()
    {
        return $this->belongsToMany(Categorys::class, 'user_fond_cate', 'cate_id', 'uid');
    }*/

    /**
     * 关联：签到表
     */
    public function hasManyActivitySign()
    {
        return $this->hasMany(Sign::class, 'uid', 'uid');
    }

    static function getCount($where){
        return self::where($where)->count();
    }
    static function getRow($where){
        return self::where($where)->where('status',1)->first();
    }
    static function getRows($where){
        return self::where($where)->where('status',1)->get();
    }
    /**
     * @param $uid
     * 判断账号是否异常
     */
    static function checkAuth($uid){
        if(!Auth::check()||Auth::id()!=$uid){
            return true;
        }
        return true;
    }
    /**
     * 检查登陆
     */
    static function checkLogin($user,$password){
        if(!isset($user->uid))
            return '账号不存在';
        if($user->password==md5($password)){
            if($user->status!=1){
                return '账号异常';
            }else{
                return self::loginSuccess($user);
            }
        }else{
            return '账号或密码错误';
        }
    }

    /**
     * @param $user
     * @param bool $remember
     * @param null $tags   说明：为空时，不需要生成token
     * @return mixed 注册登陆成功后执行
     * 注册登陆成功后执行
     * @internal param $type
     */
    static function loginSuccess($user, $remember = false, $tags = null){
        if(!isset($user->uid)) return;
        Auth::loginUsingId($user->uid,$remember);
        // 生成二维码，并存储。
        if (empty($user->qrcode)) {
            $file_name = unique_id() . '.png';
            //这里的字符串换行不要去掉 这个格式不要变化
            $uid = $user->uid;
            $info =
                <<<EOF
    BEGIN:VCARD\nVERSION:3.0\nN:id:$uid\nEND:VCARD
EOF;
            $qrcode_path = img_create($info, $file_name);
            self::where('uid', $uid)->update(array('qrcode' => $qrcode_path));
            $user=self::getRow(array("uid"=>$user->uid));
        }
        //判断是否环信用户 并注册    --已弃用  屏蔽  yaokai
//        if(empty($user->is_huanxin)){
//            $users=array();
//            $users['username'] = $user->uid;
//            $users['password'] = 'wjsq'.$user->uid;
//            $result=@Helper_Huanxin::register($users);
//            if(($json = @json_decode($result,true)) && isset($json['entities'][0]['uuid'])){
//                Entity::where('uid',$user->uid)->update(array('is_huanxin'=>1));
//            }
//        }

        //首次登陆送三次免费机会
//        if($user->last_login==0){
//            Free::create(
//                [
//                    'uid'=>$user->uid,
//                    'num'=>3,
//                    'use'=>0,
//                    'deadline'=>time()+24*3600,
//                    'source'=>'first_login',
//                    'source_id'=>0,
//                ]
//            );
//        }

//        受经纪人邀请注册无界商圈，用户首次登录无界商圈后择机推送消息
//        if($user->last_login==0 && !empty($user->register_invite)){
//            $agentInfo = Agent::where('username',$user->register_invite)->first();
//            if(is_object($agentInfo)){
//                SendTemplateNotifi('user_title', [], 'user_register', [],
//                    json_encode([
//                    'type' => 'chat',
//                    'style' => 'url',
//                    'value' => [
//                        'agent_name'=> trim($agentInfo['realname']),
//                        'agent_id'=> trim($agentInfo['id'])
//                    ]
//                ]), $user);
////                send_notification('无界商圈经纪人', "当前处于邀请投资人保护期，如有其他经纪人跟进需求，请联系您的邀请经纪人，对您解除“ 投资人保护 ”。解除后，将会为你匹配其他经纪人进行加盟跟进。",
////                    json_encode(['type'=>'chat','agent_name'=> trim($agentInfo['realname']),
////                        'agent_id'=> trim($agentInfo['id'])]),
////                    $user);
//            }
//        }
        $user->login_count+=1;
        $user->last_login=time();
        $user->save();

        self::createLog($user);
        
        return self::getBase($user, $tags);
    }


    static  function  createLog($user)
    {
        $client = getClient();
        if('iPhone'==$client){
            $client =1;
        }elseif('android'==$client){
            $client =2;
        }else{
            $client =0;
        }


        //添加登录日志
        $login_data = [
            'uid'=> $user->uid,
            'ip'=>getIP(),
            'platform'=>$client,
            'meid'=>Request::header('imei', ''),
        ];
        $res = LoginLog::create($login_data);

        return $res;
    }


    /**
     * @param $user
     * 只提供uid nickname avatar
     */
    static  function getUser($user){
        if(!isset($user->uid))
            return array();
        $data=array();
        $data['uid']=$user->uid;
        $data['username']=getRealTel($user->non_reversible, 'wjsq');
        $data['nickname']=$user->nickname;
        $data['avatar']=getImage($user->avatar,'avatar','thumb');
        $data['big_avatar']=getImage($user->avatar,'avatar','large');
        $data['firstcharter']=getFirstCharter($user->nickname);
        $data['yinpin']=Pinyin(mb_substr($user->nickname,0,1));
        if($user instanceof self){
            $data['industry']=Industry::getUserIndustry($user,'all');
        }
        return $data;
    }

    /**
     * @param $user
     * @param null $tags  说明：为空时，不需要生成token
     * @return array user表的一些基本的公用数据
     * user表的一些基本的公用数据
     */
    static function getBase($user, $tags = null){
        if(!isset($user->uid))
            return array();
        $data=self::getUser($user);
        $data['qrcode']=getImage($user->qrcode,'qrcode');
        $data['maker_id']=$user->maker_id;
        $data['realname']=$user->realname;
        $data['sign']=$user->sign;
        $data['is_huanxin']=$user->is_huanxin;
        $data['activity_remind']=$user->activity_remind;

        //数据中心获取真实手机号

        $data['username']=getRealPhone($user->non_reversible,'wjsq');

        /**ovo中心名称***/
        $maker=\App\Models\Maker\Entity::getRow(array('id'=>$user->maker_id));
        $data['subject']=isset($maker->id)?$maker->subject:'';
        $data['gender']=$user->gender;
        $data['industry']=Industry::getUserIndustry($user,'all');
        $data['institution']=isset($user->business_card->institution)?$user->business_card->institution:'';
        $data['job']=isset($user->business_card->job)?$user->business_card->job:'';
        $data['activity_count']=Ticket::where('uid',$user->uid)->where('status',1)->DISTINCT()->count('activity_id');
        $data['exist_card']=$user->business_card()->count();//是否存在电子名片
        $data['zone']= $user->zone_id ? array_get(\App\Models\Zone::find($user->zone_id),'name') : '';//地区
        $data['zone_id']= $user->zone_id;//地区
        $data['is_done_reg'] = 1;//是否完成资料填写 0:否 1:是
        $data['has_brand'] = ($brand = \App\Models\Brand\Entity::where('uid',$user->uid)->first())?1:0;//是否拥有品牌
        $data['brand_name'] = $brand?$brand->name:'';//品牌名称
        if(!$user->zone_id || !$user->nickname || (!Industry::where('uid',$user->uid)->first())){
            $data['is_done_reg'] = 0;
        }
        //如果没有我的邀请码,按规则生成不重复不含4的8位数字
        if(!$user->my_invite){
            $user->my_invite = self::generateUniqueInviteCode();
            $user->save();
        }
        $data['my_invite'] = $user->my_invite;
        $data['birth'] = $user->birth;
        $data['diploma'] = $user->diploma;
        $data['earning'] = $user->earning;
        $data['profession'] = $user->profession;

        //当tags为真时，生成token，目的是为了区不同的注册和使用场景
        if ($tags) {
            if (empty($user->token)) {
                $user_name      = $user->realname ? $user->realname : $user->nickname;
                $token['token'] = GainToken($user->uid, $user_name, $user->avatar);
                self::where('uid', $user->uid)->update($token);
            }
            $data['token'] = !empty($token['token']) ?  $token['token'] : $user->token;
        }

        return $data;
    }

    /*
     * 生成不重复不含4的随机数
     */
    static function generateUniqueInviteCode()
    {
        $numArr = ['0','1','2','3','5','6','7','8','9'];
        shuffle($numArr);
        $inviteCode = (string)rand(10000001,99999999);
        if(strpos($inviteCode,'4')!==false){
            $inviteCode = str_replace('4',$numArr[0],$inviteCode);
        }

        if (self::getCount(['my_invite' => $inviteCode])) {
            return self::generateUniqueInviteCode();
        }

        return $inviteCode;
    }

    /**
     * 修改密码  --弃用  不处理
     * @User
     * @param $username
     * @param $newpassword
     * @return bool
     */
    static function changePassword($username, $newpassword){
        $user = self::getRow(array('username'=>$username));
        if(!isset($user->uid))
            return false;
        if(md5($newpassword) == $user->password){
            return true;
        }
        if (self::where('username',$username)->update(array('password'=>md5($newpassword)))) {
            return true;
        }
        return false;
    }

    /**
     * @param $where
     * 支持uid或者username获取用户user   --展示作用  数据中心暂不处理
     */
    static function getUserByuidOrUsername($user_outh,$type){
        $uid=(Auth::check())?Auth::id():0;
        $data=array();
        foreach($user_outh as $k=>$v){
            $user=Entity::getRow(array('uid'=>$v));
            if(!isset($user->uid))
                $user=Entity::getRow(array('username'=>$v));
            if(!isset($user->uid)){
                $data[$k]['is_wjsq']=0;
                $data[$k]['username']=$v;
            }else{
                $data[$k]=($type=='basic')?Entity::getUser($user):Entity::getBase($user);
                $data[$k]['is_wjsq']=1;
                //如果没有我的邀请码就生成一个
                if(!$user->my_invite){
                    $invite = self::makeInvite();
                    $data[$k]['my_invite']=$invite;
                    $user->my_invite = $invite;
                    $user->save();
                }
                $data[$k]['is_wjsq']=1;
                $data[$k]['is_huanxin']=$user->is_huanxin;
                $data[$k]['friend_number']=0;
                if($user->is_huanxin){
                    $lists = Helper_Huanxin::getFriendList($user->uid, 'array');
                    $data[$k]['friend_number'] = is_array($lists) ? count($lists) : 0;
                }
                //$relations = Helper_Huanxin::isFriends($uid, array($user->uid));
                //$relations = json_decode($relations,true);
                //$data[$k]['relation']=isset($relations[0]['relation'])?$relations[0]['relation']:'stranger';
            }
        }
        return $data;
    }

    static public function makeInvite()
    {
        $my_invite = self::lists('my_invite')->toArray();
        $arr = [0,1,2,3,5,6,7,8,9];
        $str='';
        while($str =='' ||in_array($str, $my_invite)){
            for ($i=0; $i<8;$i++){
                $str.=$arr[array_rand($arr)];
            }
        }
        return $str;
    }

    /**
     * 随机获取用户
     */
    static function getUsersByRand(){
        $sql="SELECT t1.*
        FROM `lab_user` AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(uid) FROM `lab_user`)-(SELECT MIN(uid) FROM `lab_user`))+(SELECT MIN(uid) FROM `lab_user`)) AS uid) AS t2
WHERE t1.uid >= t2.uid
ORDER BY t1.uid LIMIT 10";
        $users=DB::select($sql);
        return $users;
    }

    /**
     * @param $gender
     * 获取性别的中文
     */
    static function  getGender($gender){
        if($gender==-1){
            return '未知';
        }elseif($gender==1){
            return '男';
        }else{
            return '女';
        }
    }

    /*
    * 作用:判段是否有uid对应的用户
    * 参数:uid 用户ID
    *
    * 返回值: false or obj
    */
    public static function hasAvailableUser($uid)
    {
        if($uid == ''){
            return false;
        }
        $user = self::where([
            'uid'=>$uid,
            'status'=>1,
        ])->first();
        return is_null($user) ? false : $user;
    }

    /*
    * 作用:统计对应分享记录的受邀用户数
    * 参数:分享ID
    *
    * 返回值:受邀人数
    */
    public static function getFollowedInvitationNum($user_share_id)
    {
        if($user_share_id == false)
            return 0;

        return self::where([
            'user_share_id'=>$user_share_id,
            'status'=>1,
        ])->count();
    }

    /**
     * 根据某个键自增或自减
     *
     * @param array $incre
     * @param array $field
     *
     */
    public static function incre(Array $incre, Array $field)
    {
        $result = self::where($field)->increment(array_keys($incre)[0], array_values($incre)[0]);
        return $result;
    }


    /**
     * 查找是否存在  再注册   --数据中心版
     * @User tangjb
     * @param $tel
     * @param $name
     * @return array
     */
    public static function findOrRegister($tel, $name)
    {
        //伪号码
        $username = pseudoTel($tel);

        //用户加密后的手机号
        $non_reversible = encryptTel($tel);

        $user = self::getRow(['non_reversible' => $non_reversible]);
        if (is_object($user)) {
            return ['user' => $user, 'is_register' => 1, 'status' => true];
        } else {

            //数据中心处理
            $url = config('system.data_center.hosts') . config('system.data_center.encrypt');
            $data = [
                'nation_code' => '86',//这里不做处理 全部默认86
                'tel' => $tel,
                'platform' => 'wjsq',//来源无界商圈注册
                'en_tel' => $non_reversible,//通过加盐后得到手机号码
            ];

            //请求数据中心接口
            $result = json_decode(getHttpDataCenter($url, '', $data));


            //如果异常则停止
            if (!$result) {
                return ['status' => FALSE, 'message' => '服务器异常！'];
            } elseif ($result->status == false) {
                return ['status' => false, 'message' => $result->message];
            }

            $user = self::create(['username' => $username,'non_reversible' => $non_reversible, 'password' => md5($tel), 'nickname' => uniqid() . mt_rand(10000, 99999), 'realname' => $name, 'source' => '5']);
            $user->nickname = 'wjsq' . $user->uid;
            $user->save();
            return ['user' => $user, 'is_register' => 0, 'status' => true];
        }
    }

    public static function getRemindList($agentId)
    {
        $newTime = time();
        $activityReminds = AgentCustomer::leftJoin('activity_sign', 'activity_sign.uid', '=', 'agent_customer.uid')
            ->leftJoin('activity', 'activity.id', '=', 'activity_sign.activity_id')
            ->where('agent_customer.agent_id', $agentId)
            ->where('activity.end_time', '>', $newTime)
            ->where('activity_sign.status', 0)
            ->select('activity_sign.uid')->distinct()
            ->count();
        $protectCustomers = AgentCustomer::where('agent_id', $agentId)->where('protect_time', '>', $newTime)
            ->count();
        $inspectReminds = Invitation::where('agent_id', $agentId)
            ->where('type', 2)->whereIn('status', [1, 2])
            ->where('invitation.inspect_time', '>', $newTime)
            ->select('invitation.uid')->distinct()->count();
        return array(
            'activity_reminds' => $activityReminds,
            'protect_customers' => $protectCustomers,
            'inspect_reminds' => $inspectReminds,
        );
    }

    //获取活动提醒用户
    public static function getActivityRemind($agentId){
        $agentInfo=Agent::where('id',$agentId)->where('status','<>','-1')->first();
        if(!is_object($agentInfo)){
            return array(
                "message"=>"请输入有效的经纪人id",
                'error'=>1
            );
        }
        $newTime=time();


        $activityReminds= Invitation::where('invitation.agent_id',$agentId)
            ->where('invitation.type',1)
            ->where('invitation.status',1)
            ->leftJoin('activity_sign','invitation.uid','=','activity_sign.uid')
            ->where('activity_id','post_id')
           ->get();



        $data=[];
        $lastId=-1;
        $k=-1;
        foreach ($activityReminds as $activityRemind){
            if($activityRemind['id']==$lastId){
                $data[$k]['list'][]=array(
                    'uid'=>trim($activityRemind['uid']),
                    'avatar'=>trim($activityRemind['avatar']),
                    'city'=>trim($activityRemind['name']),
                    'gender'=>trim($activityRemind['gender']),
                );
            }
            else{
                $k++;
                $data[$k]=array(
                    'activity_id'=>trim($activityRemind['id']),
                    'activity_title'=>trim($activityRemind['subject']),
//                    'activity_begin_time'=>trim($activityRemind['begin_time']->getTimestamp()),
                    'activity_begin_time'=>trim($activityRemind['begin_time']),
                    'list'=>array(
                        array(
                            'uid'=>trim($activityRemind['uid']),
                            'avatar'=>trim($activityRemind['avatar']),
                            'city'=>trim($activityRemind['name']),
                            'gender'=>trim($activityRemind['gender']),
                        )
                    )
                );
                $lastId=trim($activityRemind['id']);
            }
        }
        return $data;
    }

    /**
     * 需要考察提醒的客户  -- 数据中心版
     * @User shiqy
     * @param $agentId
     * @return array
     */
    public static function getInspectRemind($agentId){
        $agentInfo=Agent::where('id',$agentId)->where('status','<>','-1')->first();
        if(!is_object($agentInfo)){
            return array(
                "message"=>"请输入有效的经纪人id",
                'error'=>1
            );
        }
        $data = [];
        //思路是  获取考察邀请函后，以相同的  门店和考察时间  分组    然后展示
        $nowTime=time();
        $invitation = Invitation::with('hasOneStore.hasOneBrand','hasOneUsers.zone','hasOneUsers.agent_customer')
            ->with(['hasOneUsers.agent_customer'=>function($query)use($agentId){
                $query->where('agent_id',$agentId);
            }])
            ->where(function($query)use($agentId,$nowTime){
                $query->where('agent_id',$agentId);
                $query->where('type',2);
                $query->whereIn('status',[1,2]);
                $query->where('inspect_time','>',$nowTime);
            })->get()->toArray();
        $invitationSortGroup = collect($invitation)->groupBy(function($item){
            $inspectTimeStamp = strtotime(date('Y-m-d',$item['inspect_time']));
            return $inspectTimeStamp.$item['post_id'];
        })->sortBy(function($item){
            return $item[0]['inspect_time'];
        });
        foreach ($invitationSortGroup as $oneGroup){
            $arr = [];
            $arr['store_id'] = trim($oneGroup[0]['post_id']);
            $arr['brand_title'] = trim($oneGroup[0]['has_one_store']['has_one_brand']['name']);
            $arr['store_title'] = trim($oneGroup[0]['has_one_store']['name']);
            $arr['ins_city'] = trim(Zone::getCityAndProvince($oneGroup[0]['has_one_store']['zone_id']));
            $arr['inspect_time'] = trim(date('m月d日',$oneGroup[0]['inspect_time']));
            if(strtotime(date('Y-m-d',$oneGroup[0]['inspect_time'])) ==  strtotime('today')){
                $arr['today'] = "(今天)";
            }
            foreach ($oneGroup as $oneInvitation){
                $arr['list'][] = array(
                    'uid'=>trim($oneInvitation['uid']),
                    'avatar'=> getImage($oneInvitation['has_one_users']['avatar']),
                    'nickname'=>trim($oneInvitation['has_one_users']['nickname']),
                    'city'=>trim($oneInvitation['has_one_users']['zone']['name']),
                    'gender'=>trim($oneInvitation['has_one_users']['gender']),
                    'phone'=>trim($oneInvitation['has_one_users']['non_reversible']),
                    'non_reversible'=>trim($oneInvitation['has_one_users']['non_reversible']),
                    'is_pub_phone'=>empty($oneInvitation['has_one_users']['agent_customer'][0]['has_tel']) ? '0' : '1' ,
                );
            }
            $data[] = $arr;
        }
        return $data;
    }



    public static function getMyorders($uid,$page,$pageSize,$isComplete , $version = ''){
        $userInfo=self::where('uid',$uid)->where('status','<>',-1)->first();
        if(!is_object($userInfo)){
            return array(
                "message"=>"请输入有效的用户id",
                'error'=>1
            );
        }
        //未支付页面 请求第二页返回空
        if($isComplete == 0 && $page > 1){
            return [[ "total"=> 0, "list"=> [] ]];
        }
        $data=[];
        //本月第一天的时间戳
        $thisMonthTamp = strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
        $nowTime=time();
        $dieTime=$nowTime-1800;

        //获取orders表中有所订单相关数据
        $orderList = Orders::with('hasOneOrdersItems.belongsToContract.user_fund',
            'hasOneOrdersItems.belongsToContract.brand',
            'hasOneOrdersItems.belongsToContract.invitation',
            'hasOneOrdersItems.belongsToInvitation.hasOneStore.hasOneBrand',
            'hasOneOrdersItems.belongsToNews',
            'hasOneOrdersItems.belongsToVideo','hasOneOrdersItems.belongsToScore',
            'hasOneOrdersItems.belongsToBrandGoods.belongsToBrand',
            'hasOneOrdersItems.live_brand_goods.brandInfo'
            )
            ->with(['hasOneOrdersItems.belongsToContract.red_packet'=>function($query){
                $query->where('status', '1');
            }])
            ->where(function($query)use($isComplete,$uid){
                if($isComplete){
                    $query->where('status','pay');
                }
                else{
                    $query->whereIn('status',['npay','expire']);
                }
                $query->where('uid',$uid);
            })
            ->orderBy('created_at','desc')
            ->get()->toArray();

        $orderListCollect=collect($orderList);

        //获取order表中有所订单相关数据
        $oldOrderList=Order::with('belongsToActivityTicket.belongsToActivity.hasOneLive')
            ->where(function($query)use($isComplete,$uid){
                if($isComplete){
                    $query->where('status',1);
                }
                else{
                    $query->whereIn('status',[0,-1]);
                }
                $query->where('uid',$uid);
            })
            ->orderBy('created_at','desc')
            ->get()->toArray();
        $oldOrderCollect=collect($oldOrderList);

        if($isComplete){
            if($page == 1){
                //本月的已完成订单
                $thisMonthOrderArr=$orderListCollect->filter(function($item)use($thisMonthTamp){
                    return $item['pay_at']>=$thisMonthTamp;
                })->toArray();
                $thisMonthOldOrderArr=$oldOrderCollect->filter(function($item)use($thisMonthTamp){
                    return $item['updated_at']>=$thisMonthTamp;
                })->toArray();
                $completeOrderArr=self::getOrderArr($thisMonthOrderArr,$dieTime , $version);
                $completeOldOrderArr=self::getOldOrderArr($thisMonthOldOrderArr,$dieTime);
                $data[]=self::mergeOrder($completeOrderArr,$completeOldOrderArr);
            }

            //获取之前的已完成订单，去取出page_size条
            $beforeOrderCollect=$orderListCollect->filter(function($item)use($thisMonthTamp){
                return $item['pay_at']<$thisMonthTamp;
            });
            $beforeOldOrderArr=$oldOrderCollect->filter(function($item)use($thisMonthTamp){
                return $item['updated_at']<$thisMonthTamp;
            })->toArray();
            $mergeBeforeOrderArr=$beforeOrderCollect->merge($beforeOldOrderArr)->sortByDesc(function($item){
                if(isset($item['pay_at'])){
                    return trim($item['pay_at']);
                }
                else{
                    return trim($item['updated_at']);
                }
            })->forPage($page,$pageSize)->toArray();

            $beforeOrderArr=[];
            foreach ($mergeBeforeOrderArr as $oneBeforeOrder){
                $arr = [];
                $arr[] = $oneBeforeOrder;
                if(isset($oneBeforeOrder['pay_at'])){
                    $oneOrderArr = self::getOrderArr($arr,$dieTime , $version);
                    $beforeOrderArr[] = $oneOrderArr['list'][0];
                }
                else{
                    $oneOrderArr = self::getOldOrderArr($arr,$dieTime);
                    $beforeOrderArr[] = $oneOrderArr['list'][0];
                }
            }
            $data[]=array(
                'total'=>count($beforeOrderArr),
                'list'=> $beforeOrderArr,
            );
        }
        else{
            //未完成订单
            $notCompleteOrderArrs=$orderListCollect->filter(function($item)use($dieTime){
                if($item['has_one_orders_items']['type'] == 'contract'){
                    return $item['status'] == 'npay';
                }
                else{
                    return $item['status'] == 'npay' && $dieTime < $item['created_at'];
                }
            })->toArray();
            $notCompleteOldOrderArrs=$oldOrderCollect->filter(function($item)use($dieTime){
                return $item['status'] == '0' && $dieTime < $item['created_at'];
            })->toArray();

            $notCompleteOrderArr=self::getOrderArr($notCompleteOrderArrs,$dieTime , $version);
            $notCompleteOldOrderArr=self::getOldOrderArr($notCompleteOldOrderArrs,$dieTime);
            $data[]=self::mergeOrder($notCompleteOrderArr,$notCompleteOldOrderArr);

            //过期订单
//            $expireOrderArrs=$orderListCollect->filter(function($item)use($dieTime){
//                return ($item['status'] == 'npay' && $dieTime >= $item['created_at']) || $item['status'] == 'expire';
//            })->toArray();
//            $expireOldOrderArrs=$oldOrderCollect->filter(function($item)use($dieTime){
//                return ($item['status'] == 0 && $dieTime >= $item['created_at']) || $item['status'] == -1;
//            })->toArray();
//            $expireOrderArr=self::getOrderArr($expireOrderArrs,$dieTime);
//            $expireOldOrderArr=self::getOldOrderArr($expireOldOrderArrs,$dieTime);
//            $data[]=self::mergeOrder($expireOrderArr,$expireOldOrderArr);
        }
        return $data;
    }

    protected static function getOrderArr($orderList,$dieTime ,$version = ''){
        $newOrderList=[];
        $newOrderList['total']=count($orderList);
        $newOrderList['list']=[];
        foreach ($orderList as $oneOrder){
            $arr = [];
            $orderType = trim($oneOrder['has_one_orders_items']['type']);
            $orderStatus = trim($oneOrder['status']);
            $createTime = trim($oneOrder['created_at']);
            $arr['type'] = $orderType;
            $arr['order_no'] = trim($oneOrder['order_no']);
            $arr['create_at'] = trim($oneOrder['created_at']);


            if($orderStatus == 'pay'){
                $arr['order_status'] = '1';
            }
//            else if($orderType == 'contract' && $orderStatus == 'npay'){
//                $arr['order_status'] = '2';
//            }
            else if($orderStatus == 'npay' && $dieTime < $createTime){
                $arr['order_status'] = '2';
            }
            else{
                $arr['order_status'] = '3';
            }

//            支付方式
            $payWay=trim($oneOrder['pay_way']);
            if($payWay=='ali'){
                $arr['pay_way']='支付宝';
            }
            else if($payWay=='weixin'){
                $arr['pay_way']='微信';
            }
            else if($payWay=='unionpay'){
                $arr['pay_way']='银联';
            }
            else if($payWay=='red_packet'){
                $arr['pay_way']='邀请红包抵扣';
            }
            else if($payWay=='score'){
                $arr['pay_way']='积分';
            }
            else if($payWay=='mix'){
                $arr['pay_way']='混合支付';
            }

            if($orderType == 'contract'){
                //如果是2.9.2  之前的版本走这个
                if(empty( $version ) || $version < '_v020902'){
                    $brandName = trim($oneOrder['has_one_orders_items']['belongs_to_contract']['brand']['name']);
                    $arr['order_name'] = '品牌“'.$brandName.'”电子合同加盟费用';
                    $arr['total_pay'] = trim(floatval($oneOrder['has_one_orders_items']['belongs_to_contract']['amount']));
                    $arr['pre_pay'] = trim(floatval($oneOrder['has_one_orders_items']['belongs_to_contract']['pre_pay']));
                    $arr['final_pay'] =doFormatMoney(floatval($arr['total_pay'] - $arr['pre_pay'])) ;
                    $invitePay = $oneOrder['has_one_orders_items']['belongs_to_contract']['invitation']['default_money'];
                    $funt = $oneOrder['has_one_orders_items']['belongs_to_contract']['red_packet']['amount'];
                    $arr['reduced_price'] = trim(floatval($invitePay + $funt)) ;

                    $arr['pre_time'] = trim($oneOrder['pay_at']);
                    $contractStatus = intval($oneOrder['has_one_orders_items']['belongs_to_contract']['status']) ;
                    $arr['actual_pay'] = '0';
                    $arr['final_time'] = '';
                    if($contractStatus == 1){
                        $arr['actual_pay'] = doFormatMoney(floatval($arr['pre_pay'] - $arr['reduced_price']));
                        $arr['final_time'] = '未支付';
                        $arr['order_status'] = '0';
                    }
                    else if($contractStatus == 2){
                        $arr['actual_pay'] = doFormatMoney(floatval($arr['total_pay'] - $arr['reduced_price']));
                        $arr['final_time'] = trim($oneOrder['has_one_orders_items']['belongs_to_contract']['tail_pay_at']);
                    }
                    $arr['order_type'] = '付款协议';

                    //                转格式
                    $arr['total_pay'] = doFormatMoney($arr['total_pay']);
                    $arr['pre_pay'] = doFormatMoney($arr['pre_pay']);
                    $arr['reduced_price'] = doFormatMoney($arr['reduced_price']);
                }
                //2.9.2以后，订单有大调整，显示下面的
                else{
                    //品牌名称
                    $arr['brand_name'] = trim($oneOrder['has_one_orders_items']['belongs_to_contract']['brand']['name']);
                    $brandId = trim($oneOrder['has_one_orders_items']['belongs_to_contract']['brand']['id']);
                    //获取加盟方案名字和加盟方式
                    $brandContractId = intval($oneOrder['has_one_orders_items']['belongs_to_contract']['brand_contract_id']);
                    $brandContractInfo = BrandContract::where('id',$brandContractId)->where('is_delete',0)
                        ->select('name','league_type_id')->first();
                    $arr['league_plan_name'] = '';
                    $arr['league_type'] = '';
                    if(is_object($brandContractInfo)){
                        $arr['league_plan_name'] = trim($brandContractInfo['name']);
                        $arr['league_type'] = trim($brandContractInfo['league_type_id']);
                    }

                    //获取成单经纪人名字
                    $agentId = intval($oneOrder['has_one_orders_items']['belongs_to_contract']['agent_id']);
                    $agentInfo = Agent::where('status',1)->where('id',$agentId)->first();
                    $arr['agent_name'] = '';
                    if(is_object($agentInfo)){
                        $arr['agent_name'] = Agent::unifiHandleName($agentInfo , '','');
                    }
                    $arr['total_pay'] = doFormatMoney(floatval($oneOrder['has_one_orders_items']['belongs_to_contract']['amount']));
                    $arr['contract_id'] = trim($oneOrder['has_one_orders_items']['belongs_to_contract']['id']);
                    //订单状态,合同订单只有两种状态，支付完成和未完成
                    if($orderStatus == 'npay'){
                        $arr['order_status'] = '2';
                        //pos机支付的总额
                        $payLogs = ContractPayLog::getPayDetailByType($arr['contract_id'] , ContractPayLog::$_PAY_TYPES );
                        //剩余额度
                        $arr['residue'] = doFormatMoney(floatval($oneOrder['has_one_orders_items']['belongs_to_contract']['amount'] - $payLogs['total']));

                    }
                    else if($oneOrder['has_one_orders_items']['belongs_to_contract']['status'] == 2){
                        $arr['order_status'] = '1';
                    }
                    else if($oneOrder['has_one_orders_items']['belongs_to_contract']['status'] == 4){
                        $arr['order_status'] = '5';
                    }
                    else{
                        $arr['order_status'] = '4';
                    }

                    //获取优惠额度
                    if(in_array($arr['order_status'],['1','4','5'])){
                        $payLogs = ContractPayLog::getPayDetailByType($arr['contract_id'] , ContractPayLog::$_REFUND_TYPES );
                        $arr['discount'] = doFormatMoney(floatval($payLogs['total']));
                        $arr['tail_pay_at'] = trim($oneOrder['has_one_orders_items']['belongs_to_contract']['tail_pay_at']);
                    }

                    //获取品牌的对公账号信息
                    $brandPayInfo = Payinfo::where('brand_id',$brandId)->where('status',0)->select('company','account','bank_name')->first();
                    $arr['company'] = '';
                    $arr['account'] = '';
                    $arr['bank_name'] = '';
                    if(is_object($brandPayInfo)){
                        $arr['company'] = trim($brandPayInfo['company']);
                        $arr['account'] = trim($brandPayInfo['account']);
                        $arr['bank_name'] = trim($brandPayInfo['bank_name']);
                    }
                }
            }
            if($orderType == 'inspect_invite'){
                $brandName = trim($oneOrder['has_one_orders_items']['belongs_to_invitation']['has_one_store']['has_one_brand']['name']);
                $arr['order_name'] = '品牌“'.$brandName.'”门店考察订金支付';
                $arr['actual_pay'] = doFormatMoney(floatval($oneOrder['amount']));
                $arr['pay_at'] = trim($oneOrder['pay_at']);
                $arr['should_pay'] = doFormatMoney(floatval($oneOrder['has_one_orders_items']['belongs_to_invitation']['default_money']));
                $arr['order_type'] = '考察订金';
            }

            if($orderType == 'news'){
                $title = trim($oneOrder['has_one_orders_items']['belongs_to_news']['title']);
                $arr['order_name'] = '《'.$title.'》'.'阅读购买';
                $arr['actual_pay'] = doFormatMoney(intval($oneOrder['score_num']));
                $arr['pay_at'] = trim($oneOrder['pay_at']);
                $arr['should_pay'] = doFormatMoney(intval($oneOrder['score_num']));
                $arr['order_type'] = '资讯';
            }

            if($orderType == 'video'){
                $title = trim($oneOrder['has_one_orders_items']['belongs_to_video']['subject']);
                $arr['order_name'] = '《'.$title.'》'.'观看购买';
                $arr['actual_pay'] = doFormatMoney(intval($oneOrder['score_num']));
                $arr['pay_at'] = trim($oneOrder['pay_at']);
                $arr['should_pay'] = doFormatMoney(intval($oneOrder['score_num']));
                $arr['order_type'] = '录播';
            }

            if($orderType == 'score'){
                $arr['order_name'] = trim($oneOrder['has_one_orders_items']['belongs_to_score']['subject']);
                $arr['actual_pay'] = doFormatMoney(floatval($oneOrder['amount']));
                $arr['pay_at'] = trim($oneOrder['pay_at']);
                $arr['should_pay'] = doFormatMoney(floatval($oneOrder['amount']));
                $arr['order_type'] = '积分充值';
            }

            //这里需要改下
            if($orderType == 'brand_goods' || $orderType == 'brand'){
                if($orderType == 'brand_goods'){
                    $title = trim($oneOrder['has_one_orders_items']['belongs_to_brand_goods']['belongs_to_brand']['name']);
                }else{
                    $title = trim($oneOrder['has_one_orders_items']['live_brand_goods']['brand_info']['name']);
                }
                $arr['order_name'] = '“'.$title.'”'.'加盟定金';
                $arr['actual_pay'] = doFormatMoney(floatval($oneOrder['amount']));
                $arr['pay_at'] = trim($oneOrder['pay_at']);
                $arr['should_pay'] = doFormatMoney(floatval($oneOrder['amount']));
                $arr['order_type'] = '品牌加盟定金';
            }

            $newOrderList['list'][]=$arr;
        }
        return $newOrderList;
    }

    public static function mergeOrder($arr1,$arr2){
        $arr=[];
        $arr['total']=$arr1['total']+$arr2['total'];
        $arr['list'] = array_merge($arr1['list'],$arr2['list']);
        return $arr;
    }

    public static function getOldOrderArr($orderList,$dieTime){
        $newOrderList=[];
        $newOrderList['total']=count($orderList);
        $newOrderList['list']=[];
        foreach ($orderList as $oneOrder){
            $arr = [];
            $orderType = trim($oneOrder['belongs_to_activity_ticket']['type']);
            $orderStatus = trim($oneOrder['status']);
            $createTime = trim($oneOrder['created_at']);
            $arr['order_no'] = trim($oneOrder['order_no']);
            $arr['create_at'] = $createTime;
            if($orderStatus == '1'){
                $arr['order_status'] = '1';
            }
            else if($orderStatus == '0' && $dieTime < $createTime){
                $arr['order_status'] = '2';
            }
            else{
                $arr['order_status'] = '3';
            }

            //            支付方式
            $payWay=trim($oneOrder['pay_way']);
            if($payWay=='ali'){
                $arr['pay_way']='支付宝';
            }
            else if($payWay=='weixin'){
                $arr['pay_way']='微信';
            }
            else if($payWay=='unionpay'){
                $arr['pay_way']='银联';
            }
            else{
                $arr['pay_way']='积分';
            }
            if($orderType == '1'){
                $arr['type'] = 'activity';
                $title = trim($oneOrder['belongs_to_activity_ticket']['belongs_to_activity']['subject']);
                $arr['order_name'] = '“'.$title.'”'.'现场门票购买';
                $arr['order_type'] = '活动门票';
            }
            if($orderType == '2'){
                $arr['type'] = 'live';
                $title = trim($oneOrder['belongs_to_activity_ticket']['belongs_to_activity']['has_one_live']['subject']);
                $arr['order_name'] = '“'.$title.'”'.'直播票购买';
                $arr['order_type'] = '直播门票';
            }
            $arr['actual_pay'] = doFormatMoney(intval($oneOrder['score_num']));
            $arr['pay_at'] = trim($oneOrder['updated_at']);
            $arr['should_pay'] = doFormatMoney(intval($oneOrder['score_num']));
            $newOrderList['list'][] = $arr;
        }
        return $newOrderList;
    }


    /**
     * 我的邀请人    --数据中心版  todo 暂时只处理邀请号码去查询的  其他展示字段未处理
     * if  这个投资人的邀请人字段不为空
     *      then
     *          if  经纪人投资人表中有没有关于这个投资人保护时间的记录
     *              then
     *                  取出该邀请人的信息返回
     *              else
     *                  查user表该邀请人的信息返回
     *          endif
     *      else
     *          没有邀请人
     * endif
     * shiqy
     * */
    public static function getMyInviterInfo($uid){
        $userInfo = self::where('uid',$uid)->where('status','<>',-1)->first();
        if(!is_object($userInfo)){
            return array(
                'error'=>1,
                'message'=>'请输入有效的用户id'
            );
        }
        $data = [];
        $registerInvite = trim($userInfo['register_invite']);
        if(empty($registerInvite)){
            return [];
        }
        //以前c端要的邀请码 是8位随机数字串，所以邀请码可能是8位或者是11位的
        if(strlen($registerInvite) == 8){
            $inviterInfo = self::where('my_invite',$registerInvite)->first();
            $data['avatar'] = getImage($inviterInfo['avatar']);
            $data['gender'] = trim($inviterInfo['gender']);
            //这里添加nickname，realname，is_public_realname只是为了和经纪人统一
            $data['realname'] = empty($inviterInfo['realname'])? trim($inviterInfo['nickname']) : trim($inviterInfo['realname']);
            $data['nickname'] = empty($inviterInfo['realname'])? trim($inviterInfo['nickname']) : trim($inviterInfo['realname']);
            $data['is_public_realname'] = '1';
            $data['city'] = trim($inviterInfo['zone']['name']);
            $data['phone'] = getRealTel($inviterInfo->non_reversible, 'wjsq');
            $data['id'] = trim($inviterInfo['id']);
            $data['tags'] = self::getUserTags($inviterInfo);
            $data['role'] = '1';
        }
        else{
            $log = AgentCustomerLog::with('agent.hasOneZone')->where(function($query)use($uid){
                $query->where('uid',$uid);
                $query->where('action',14);
            })->first();
            if(is_object($log)){
                $logInfo = $log->toArray();
                $tags = [];
                $keywords = Agent::getAgentKeywords($logInfo['agent_id']);
                trim($keywords['years']) && $tags[] = trim($keywords['years']);
                trim($keywords['constellation']) && $tags[] = trim($keywords['constellation']);
                trim($keywords['native']) && $tags[] = trim($keywords['native']);
                foreach ($keywords['industrys'] as $oneIndustry){
                    $tags[] = trim($oneIndustry['name']);
                }
                $agentInfo = $logInfo['agent'];
                $data['avatar'] = getImage($agentInfo['avatar']);
                $data['gender'] = trim($agentInfo['gender']);
                $data['realname'] = trim($agentInfo['realname']);
                $data['nickname'] = trim($agentInfo['nickname']);
                $data['is_public_realname'] = trim($agentInfo['is_public_realname']);
                $data['city'] = trim($agentInfo['has_one_zone']['name']);
                $data['city_name'] = Zone::getCityAndProvince($agentInfo['has_one_zone']['id']);
                $data['phone'] = getRealTel($agentInfo['non_reversible'], 'agent');
                $data['tags'] = $tags;
                $data['role'] = '2';
                $data['id'] = trim($agentInfo['id']);;
            }
            else{
                $inviterInfo = self::with('zone')->where('non_reversible',$userInfo['register_invite'])->first();
                if(!is_object($inviterInfo)){
                    return array(
                        'error'=>1,
                        'message'=>'邀请人信息错误'
                    );
                }
                $data['avatar'] = getImage($inviterInfo['avatar']);
                $data['gender'] = trim($inviterInfo['gender']);
                //这里添加nickname，realname，is_public_realname只是为了和经纪人统一
                $data['realname'] = empty($inviterInfo['realname'])? trim($inviterInfo['nickname']) : trim($inviterInfo['realname']);
                $data['nickname'] = empty($inviterInfo['realname'])? trim($inviterInfo['nickname']) : trim($inviterInfo['realname']);
                $data['is_public_realname'] = '1';
                $data['city'] = trim($inviterInfo['zone']['name']);
                $data['phone'] = getRealTel($inviterInfo->non_reversible, 'wjsq');
                $data['id'] = trim($inviterInfo['id']);
                $data['tags'] = self::getUserTags($inviterInfo);
                $data['role'] = '1';
            }
        }

        return $data;
    }

    //获得用户的标签
    public static function getUserTags($userModel){
        $tags = [];
        $inviterBirth = trim($userModel['birth']);
//        $inviterBirth = '0000-01-23';
        if($inviterBirth != '0000-00-00'){
            $tags[] = getTime($inviterBirth,'birth_time');
            $tags[] = getStarsignByMonth(substr($inviterBirth, 5, 2), substr($inviterBirth, 8, 2));
        }
        $zone = trim($userModel['zone']['name']);
        $zone && $tags[] = $zone;
        //获取关注的行业
        $industryInfo = UserIndustry::with('industry')->where('uid',$userModel['id'])
            ->skip(0)->take(3)->get()->toArray();
        if(count($industryInfo)){
            foreach ($industryInfo as $oneIndustry){
                $tags[] = trim($oneIndustry['industry']['name']);
            }
        }
        return $tags;
    }




    /**
     * 获取用户标签
     *
     * @param $uid
     * @author tangjb
     */
    public function getTags($user)
    {
//        年龄，星座，归属地， 关注行业， 投资意向，投资额度
        $age = date('Y') - substr($user->birth, 0, 4);
        $data = [];
        if ($age > 0 && $age <= 60) {
            $data[] = $this->getAgeTag(substr($user->birth, 0, 4));
        }
        $industrys = UserIndustry::with('industry')->where('uid', $user->uid)->get();
        $industrys_name = array_pluck($industrys, 'industry.name');
        $invest_intention = $user->invest_intention;
        if($user->invest_intention && isset(self::$IFIntention[$invest_intention])){
                $data[] = self::$IFIntention[$invest_intention];
        }
        $invest_range = abandonZero($user->investment_min) . '~' . abandonZero($user->investment_max).'万';
        if($user->investment_max>0){
            $data[] = $invest_range;
        }

        $star_sign = getStarsignByMonth(substr($user->birth, 5, 2), substr($user->birth, 8, 2));
        //星座
        if($star_sign){
            $data[] = $star_sign;
        }

        if(isset($user->zone->name)){
            //归属地
            $city = abandonProvince($user->zone->name);
        }else{
            $city = '';
        }

        if($city){
            $data[] = $city;
        }
        if(count($industrys_name)){
            foreach($industrys_name as $k=>$v){
                $data[] = $v;
            }
        }
        return $data;
    }


    /**
     * 获取年纪标签如 80后，90后
     *
     * @author tangjb
     */
    public function getAgeTag($birth)
    {
        if($birth <= '0'){
            $time = "";
        } elseif ($birth < "1960") {
            $time = "50后";
        } elseif ($birth < "1970") {
            $time = "60后";
        } elseif ($birth < "1980") {
            $time = "70后";
        } elseif ($birth < "1990") {
            $time = "80后";
        } elseif ($birth < "1995") {
            $time = "90后";
        } elseif ($birth < "2000") {
            $time = "95后";
        } elseif ($birth < "2010") {
            $time = "00后";
        } elseif ($birth < "2020") {
            $time = "10后";
        }else{
            $time = '';
        }

        return $time;
    }

    public static function getProtected($agentId){
        $agentInfo=Agent::where('id',$agentId)->where('status','<>','-1')->first();
        if(!is_object($agentInfo)){
            return array(
                "message"=>"请输入有效的经纪人id",
                'error'=>1
            );
        }
        $nowTime=time();
        $protectCustomers=AgentCustomer::where('agent_id',$agentId)->where('protect_time','>',$nowTime)
            ->leftJoin("user",'user.uid','=','agent_customer.uid')
            ->leftJoin('zone','zone.id','=','user.zone_id')
            ->select('zone.name as zname','user.gender','user.avatar','user.nickname','user.uid','agent_customer.protect_time')
            ->get();
        $data=array();
        foreach ($protectCustomers as $protectCustomer){
            $data[]=array(
                'avatar'=> getImage($protectCustomer['avatar'],'avatar',''),
                'nickname'=>trim($protectCustomer['nickname']),
                'city'=>trim($protectCustomer['zname']),
                'gender'=>trim($protectCustomer['gender']),
                'left_days'=>trim(ceil(($protectCustomer['protect_time']-$nowTime)/86400)),
                'customer_id'=> trim($protectCustomer['uid']),
            );
        }
        return $data;
    }



}