<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
/******************************api***************************/

// 短链接解析路由
//Route::get('/{shorturlID}', function ($shorturlID) {
//    return App\Http\Controllers\Api\ShorturlController::shortredirecte($shorturlID);
//})->where('shorturlID', '[A-Za-z0-9]{5}');

Route::get('/{shorturlID}', 'Api\\ShorturlController@shortredirecte')
    ->where('shorturlID', '[A-Za-z0-9]{5}');


Route::group(['namespace' => 'Script'],function(){
    //处理数据
    Route::controller('script/index','IndexController');
});


Route::group(['namespace' => 'Api'], function () {

    //短链接请求地址 
    Route::controller('api/shorturl', 'ShorturlController');
    //登陆注册
    Route::controller('api/login', 'LoginController');
    //验证码
    Route::controller('api/identify', 'IdentifyController');
    Route::controller('identify', 'PicIdentifyController');
    //订单
    Route::controller('api/order', 'OrderController');
    //活动
    Route::controller('api/activity', 'ActivityController');
    //广告banner
    Route::controller('api/ad', 'AdController');
    //点播视频
    Route::controller('api/video', 'VideoController');
    //收藏
    Route::controller('api/favorite', 'FavoriteController');
    //ovo中心
    Route::controller('api/maker', 'MakerController');
    //行业
    Route::controller('api/industry', 'IndustryController');
    //消息中心
    Route::controller('api/message', 'MessageController');
    //评论
    Route::controller('api/comment', 'CommentController');
    //对接申请池
    Route::controller('api/userapply', 'UserApplyController');
    //发布商机
    Route::controller('api/opportunity', 'OpportunityController');
    //活动主办方
    Route::controller('api/organizer', 'OrganizerController');
    //活动主办方
    Route::controller('api/user', 'UserController');
    //直播
    Route::controller('api/live', 'LiveController');
    //直播
    Route::controller('api/userticket', 'UserTicketController');
    //地区
    Route::controller('api/zone', 'ZoneController');
    //脚本
    Route::controller('api/script', 'ScriptController');
    //热门群聊
    Route::controller('api/groupchat', 'GroupChatController');
    //上传图片
    Route::controller('api/upload', 'UploadController');
    //上传图片
    Route::controller('api/userguard', 'UserGuardController');
    //好友备注
    Route::controller('api/userfriend', 'UserFriendController');
    //版本检查
    Route::controller('api/version', 'VersionController');
    //搜索
    Route::controller('api/search', 'SearchController');

    //搜索
    Route::controller('api/share', 'ShareController');

    //专版
    Route::controller('api/vip', 'VipController');
    Route::controller('api/test', 'TestController');
    //专版
    Route::controller('api/vip', 'VipController');
    //微信
    Route::controller('api/weixin', 'WeixinController');
    //个推
    Route::controller('api/push', 'PushController');
    //品牌
    Route::controller('api/brand', 'BrandController');
    //分类
    Route::controller('api/categorys', 'CategorysController');
    //资讯
    Route::controller('api/news', 'NewsController');
    //抽奖api
    Route::controller('api/game', 'GameController');
    //首页
    Route::controller('api/index', 'IndexController');
    //兑吧
    Route::controller('api/duiba', 'DuiBaController');
    //专题
    Route::controller('api/special', 'SpecialController');

    //配置
    Route::controller('api/config', 'ConfigController');

    //积分
    Route::controller('api/score', 'ScoreController');

    //点赞
    Route::controller('api/userpraise', 'UserPraiseController');

    #点赞
    Route::controller('api/contract', 'ContractController');

    //pos机支付
    Route::controller('api/pos', 'PosController');

    //红包
    Route::controller('api/redpacket', 'RedPacketController');
    //限时活动
    Route::controller('api/timelimited', 'TimeLimitedActivityController');
});

/******************************api***************************/
Route::group(['namespace' => 'Webapp'], function () {
    ////用户协议
    //Route::controller('webapp/agreement', 'AgreementController');
    ////直播
    //Route::controller('webapp/live', 'LiveController');
    ////点播
    //Route::controller('webapp/vod', 'VodController');
    ////活动
    //Route::controller('webapp/activity', 'ActivityController');
    ////票券
    //Route::controller('webapp/ticket', 'TicketController');
    ////OVO中心介绍
    //Route::controller('webapp/ovo', 'OvoController');
    ////商机
    //Route::controller('webapp/business', 'BusinessController');
    ////官方消息
    //Route::controller('webapp/message','MessagesController');
    ////Vip消息
    //Route::controller('webapp/vipmessage','VipmessagesController');
    ////专版
    //Route::controller('webapp/special','SpecialController');
    ////品牌
    //Route::controller('webapp/brand','BrandController');
    ////品牌留言
    //
    ////搜索
    //Route::controller('webapp/search','SearchController');
    //
    ////权益
    //Route::controller('webapp/rights','RightsController');
    //
    ////邀请注册
    //Route::controller('webapp/invite','InviteController');
    ////成功加入
    // Route::controller('webapp/join','JoinController');
    // //成功加入
    // Route::controller('webapp/wjload','WjloadController');
    // //商圈头条详情
    // Route::controller('webapp/headline','HeadlineController');

    Route::get('webapp/activity/freecheck', 'ActivityController@getFreecheck');
    Route::get('webapp/activity/bmap', 'ActivityController@getBmap');

//    Route::get('webapp/{class}/{method}/{version?}', function ($class, $method, $version = '') {
//        return App\Http\Controllers\Webapp\BaseController::init(Request::all(), $class, $method, $version);
//    });

    Route::get('webapp/{class}/{method}/{version?}', 'BaseController@init');




    //经纪人端web页面
//    Route::get('webapp/agent/{class}/{method}/{version?}',function($class,$method,$version = ''){
//        return App\Http\Controllers\Webapp\BaseController::agentInit(Request::all(),$class,$method,$version);
//    });
    Route::get('webapp/agent/{class}/{method}/{version?}','BaseController@agentInit');

});
/******************************Citypartner***************************/
Route::group(['namespace' => 'Citypartner'], function () {
    Route::controller('citypartner/profit', 'ProfitController');
    Route::controller('citypartner/zone', 'ZoneController');
    Route::controller('citypartner/account', 'AccountController');
    Route::controller('citypartner/business', 'BusinessController');
    Route::controller('citypartner/message', 'MessageController');
    Route::controller('citypartner/upload', 'UploadController');
    Route::controller('citypartner/maker', 'MakerController');
    Route::controller('citypartner/myteam', 'MyteamController');
    Route::controller('citypartner/public', 'PublicController');
});

/******************************Script***************************/
//Route::group(['namespace'=>'Script'], function(){
//    Route::controller('script/income', 'IncomeController');
//    Route::controller('script/achieve', 'AchieveController');

//});


/******************************天涯云***************************/
Route::group(['namespace' => 'Cloud'], function () {
    //登录
    Route::controller('cloud/auth', 'AuthController');
    //活动信息
    Route::controller('cloud/activity', 'ActivityController');
});


////Route::get('weixin/signature', function () {
//    return \App\Http\Libs\OpenWeixin\Signature::valid(Request::all());
////});

Route::get('weixin/signature', 'OpenWeiXinController@valid');


//抽奖
Route::controller('cj', 'ChoujiangController');


//微信处理
Route::get('open/wei-xin', 'OpenWeiXinController@signature');
Route::post('open/wei-xin', 'OpenWeiXinController@index');

/******************************腾讯社交广告(广点通)***************************/
Route::group(['namespace' => 'Tencentgdt'], function () {
    Route::controller('tencent/gdt', 'GdtController');
    Route::controller('headlines/headlines', 'HeadlinesController');
});


/******************************B端***************************/
Route::group(['namespace' => 'Biz', 'domain' => json_decode(env('env_hosts'), true)[env('APP_ENV')][1]], function () {
    Route::controller('acc', 'AccountController');
    Route::get('login/login', 'LoginController@login');
    Route::post('login/login', 'LoginController@doLogin');
    Route::get('login/login', 'LoginController@login');
});


/******************************经纪人端***************************/

Route::group(['namespace' => 'Agent'], function () {
    //版本检查
    Route::controller('agent/version', 'VersionController');


    //经纪人首页
    Route::controller('agent/index', 'AgentIndexController');
    //资讯详情页
    Route::controller('agent/news', 'NewsController');
    Route::controller('agent/comment', 'CommentController');
    Route::controller('agent/inspector', 'InspectorController');
    Route::controller('agent/video', 'VideoController');
    //消息
    Route::controller('agent/message', 'MessageController');

    //品牌
    Route::controller('agent/brand', 'BrandController');
    //活动
    Route::controller('agent/activity', 'ActivityController');
    //客户
    Route::controller('agent/customer', 'CustomerController');
    //合同
    Route::controller('agent/contract', 'ContractController');

    Route::controller('agent/live', 'LiveController');
    //我的
    Route::controller('agent/user', 'UserController');

    //我的
    Route::controller('agent/push', 'PushController');
    //新手教程
    Route::controller('agent/tiro', 'AgentTiroController');
    //经纪人端广告
    Route::controller('agent/ad', 'AgentAdController');

    //派单
    Route::controller('agent/consult','ConsultController');

    //经纪人推荐品牌
    Route::controller('agent/recommend_brand','RecommendBrandController');
    /*
     * 新手学院
     * */
    Route::controller('agent/academy','AcademyController');

    //新手学院话术随身听
    Route::controller('agent/talking_skill', 'TalkingSkillController');
    //新手学院视频课堂
    Route::controller('agent/lessons', 'LessonController');
    //新手学院热文
    Route::controller('agent/article', 'ArticleController');
    //专栏
    Route::controller('agent/column', 'LecturerColumnController');
    //知识树
    Route::controller('agent/knowledge', 'KnowledgeController');
    //新手专区详情页
    Route::controller('agent/new-agent-details', 'NewAgentDetailsController');

    //展业夹话术天天练
    Route::controller('agent/talking_exercise', 'TalkingExerciseController');
    Route::controller('agent/workspace', 'WorkSpaceController');
    Route::controller('agent/agent-redpacket' , 'AgentRedPacketController');

    //经纪人的福袋红包
    Route::controller('agent/lucky-bag', 'LuckyBagController');
    Route::controller('api/lucky-bag', 'LuckyBagController'); //c端

    //临时活动举办
    Route::group(['namespace' => 'TemporaryHold'], function() {
        Route::controller('agent/temporary', 'AgentBrandActivityController');
    });
});


/******************************G端CRM  KA***************************/
Route::group(['namespace' => 'Crm'], function () {
    //品牌入驻
    Route::controller('crm/enter', 'BrandEnterController');
});



/****************************** 数据中心 ***************************/
Route::group(['namespace' => 'Center'], function () {
    //品牌入驻
    Route::controller('data-center', 'DataCenterController');

    //解析手机号
    Route::controller('analyses-phone', 'DataCenterController');
});














