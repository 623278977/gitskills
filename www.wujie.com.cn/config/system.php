<?php

return [
    'huan_xin' => [//环信配置
        'name' => env('HUAN_XIN_APP_NAME'),
        'client_id' => env('HUAN_XIN_CLIENT_ID'),
        'client_secret' => env('HUAN_XIN_CLIENT_SECRET'),
    ],
//    'cshhrUrl'=>"http://".$_SERVER['HTTP_HOST'].'/',
    'score_rate'=>100,
    'currency_rate'=>1,
    'igexin' => [//个推配置
        'app_key' => env('IGEXIN_APP_KEY'),
        'app_id' => env('IGEXIN_APP_ID'),
        'app_secret' => env('IGEXIN_APP_SECRET'),
        'master_secret' => env('IGEXIN_MASTER_SECRET'),
        'host' => env('IGEXIN_HOST'),
    ],


    'agent_igexin' => [//经纪人端个推配置
        'app_key' => env('AGENT_IGEXIN_APP_KEY'),
        'app_id' => env('AGENT_IGEXIN_APP_ID'),
        'app_secret' => env('AGENT_IGEXIN_APP_SECRET'),
        'master_secret' => env('AGENT_IGEXIN_MASTER_SECRET'),
        'host' => env('AGENT_IGEXIN_HOST'),
    ],

    'virtual_ovo' => [
        'id'=>0,
        'subject'=>'全国ovo运营中心',
        'image'=>'images/default/virtual_ovo_image.jpg',
        'logo'=>'images/default/virtual_ovo_logo.jpg',
        'address'=>'杭州市拱墅区祥园路28号乐富智汇园8号楼1楼',
        'tel'=>'400-0110-061',
        'description'=>'无界商圈对目前尚未开设运营点城市提供的虚拟运营中心。通过对城市合伙人资源整合，借助天涯网真高清视频会议设备进行跨域无缝连接,实现优质资源的匹配与共享',
        'groupid'=>  env('VIRTUAL_OVO_GROUP_ID'),
        'zone_id'=>175,
        'nickname'=>'黄蓉',
        'uid'=>0
        //'alpha'=>'H',
    ],
    'version' => 'V0214',
    'back_path' => env('back_path'),


    'duiba' => [//兑吧配置
        'app_key' => env('DUIBA_APPKEY'),
        'app_secret' => env('DUIBA_APPSECRET'),
    ],

    //数据中心相关配置
    'data_center' => [
        'hosts' => env('DATA_CENTERS'),//请求地址
        'encrypt' => '/api/user/encrypt',//加密
        'decrypt' => '/api/user/decrypt',//解密
        'send-sms' => '/api/user/send-sms',//发送短信(通过手机号md5加盐值发短信)
        'direct-sms' => '/api/user/direct-sms',//发送短信(通过手机号直接发短信)
    ],


];
