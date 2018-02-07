<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>城市合伙人</title>
    <link rel="stylesheet" href="{{URL::asset('/')}}css/citypartner/reset.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}css/citypartner/animation.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}css/citypartner/common.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}css/citypartner/jquery.jcrop.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}css/citypartner/basic.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}css/citypartner/w-pages.css">
    <script>var uploadUrl = "{{url('citypartner/upload/index')}}";</script>
</head>
<body>
<div class="g-doc">
    <div class="m-banner-bg" id="m-banner-bg">
        <!--头部-->
        <div class="g-hd ">
            <div class="container">
                <div class="m-logo ">
                    <img src="{{URL::asset('/')}}images/citypartner/logo.png" alt="">

                    <div class="m-about fr f14 ">
                        @if(empty($userinfo))
                            <a href="#001">关于我们</a>
                            <a href="#" class="register">立即注册</a>
                        @else
                            <a href="{{url('citypartner/account/list')}}">{{$userinfo->realname?:$userinfo->username}}</a>
                            <a href="{{url('citypartner/message/list')}}">消息中心@if(\App\Models\Partner\Message::getCount($userinfo->uid)) <img src="/images/citypartner/img/xiaoxi.png" alt="" style="position: relative;left:-6px;top:-6px;"/> @endif</a>
                            <a href="/citypartner/public/loginout">退出账号</a>
                        @endif
                    </div>
                </div>
                <div class="m-banner-c ">
                    <img src="{{URL::asset('/')}}images/citypartner/logo-c.png" alt="" class=" ">
                </div>
                <!--<div class="clearfix"></div>-->
                @if(empty($userinfo))
                    <div class="m-login-btn" id="m-login-btn">
                        <button class="btn btn-login tc f24 a-fadein" id="btn-login"> 立即登入</button>
                        <a href="#" class="fr" id="m-forget" style="font-size:16px">忘记密码</a>
                    </div>
                @else
                    <div class="m-login-btns" id="m-login-btns">
                        <button class="btn btn-loged tc f20" name="mybusiness">我的业务</button>
                        <button class="btn btn-loged tc f20" name="myprofit">我的收益</button>
                        <button class="btn btn-loged tc f20" name="myteam">我的团队</button>
                        <button class="btn btn-loged tc f20" name="mymaker">我的OVO中心</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!--主体-->
    <div class="g-bd">
        <div class="g-mn">
            <div class="g-m-bg">
                <div class="container">
                    @foreach($articles as $k => $article)
                    <div class="m-box">
                        <img src="{{getImage($article->image)}}" alt=""  height="320px" style="max-width:550px;@if($k==1) float:right @endif">

                        <div class="m-text @if($k==0) fr @else fl @endif">
                            <h3 class="f26 mb20">{{$article->title}}</h3>

                            <p class="f18" style="max-height:155px;overflow:hidden;">
                                {{$article->synopsis}}
                            </p>

                                <div class="clearfix"></div>
                                <a href="/citypartner/public/suggestedread?id={{$article->id}}">
                                <div class="btn btn-know mt10 tc f18">了解详情</div>
                                </a>
                            </div>
                        </div>

                    @endforeach

                </div>
            </div>
            <a name="001" id="001" ></a>
            <div class="g-m-about">
                <div class="container">
                    <div class="m-about-t mb50">
                        <h1 class="f26 tc mb20" style="font-size:40px;">关于我们 • 城市合伙人</h1>

                        <p class="tc c6" style="font-size:20px;margin-bottom:15px;">城市合伙人计划是天涯若比邻公司</p>

                        <p class="tc c6 mb50" style="font-size:20px;">针对区域发展不平衡问题推出的一套综合解决方案</p>
                    </div>


                    <h4 class="c9 mb20" style="padding-left:30px;font-size:18px;">从以下维度了解我们</h4>

                    <div class="m-about-box f14">
                        <ul>
                            <li><a href="/citypartner/public/aboutus?type=1"><span class="tc">技术</span></a></li>
                            <li><a href="/citypartner/public/aboutus?type=2"><span class="tc">布局</span></a></li>
                            <li><a href="/citypartner/public/aboutus?type=3"><span class="tc">招商</span></a></li>
                            <li><a href="/citypartner/public/aboutus?type=4"><span class="tc">创投</span></a></li>
                            <li><a href="/citypartner/public/aboutus?type=5"><span class="tc">PPP</span></a></li>
                            <li><a href="/citypartner/public/aboutus?type=6"><span class="tc">海外</span></a></li>
                            <li><a href="/citypartner/public/aboutus?type=7"><span class="tc">教育</span></a></li>
                            <li><a href="/citypartner/public/aboutus?type=8"><span class="tc">金融</span></a></li>
                            <li><a href="/citypartner/public/aboutus?type=9"><span class="tc">收益</span></a></li>
                            <li><a href="/citypartner/public/aboutus?type=10"><span class="tc">加入我们</span></a></li>
                        </ul>
                    </div>

                </div>
            </div>
            <div class="m-join">
                <div class="container ">
                    <h1 class="tc  " style="font-size:40px;margin:67px 0 60px 0;">加入我们/您的建议</h1>

                    <p class="tc f20 mb50">填写信息，我们将有工作人员与您取得联系，您的意见我们会认真阅读</p>

                    <form action="" id="join">
                        <label for="">姓名Name</label><input name='name' type="text" style="padding-left:105px;">
                        <label for="">联系电话Phone</label><input name='phone' type="text" style="padding-left:140px;">
                        <label for="">电子邮箱Email</label><input name='email' type="text" class="mr0" style="padding-left:130px;">
                        <textarea name="message" id="" class="mt20 mb50" cols="164" rows="13" placeholder="备注Message"></textarea>

                        <div class="mc btns">
                            <button type="button" class="btn btn-know btn-form-s f20" name="join">提交</button>
                            <button type="reset" class="btn btn-know btn-form-r f20">重置</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--底部-->
    <div class="g-ft">
        <div class="container">
            <div class="m-foot tc">
                <p>服务热线：400-033-0161</p>

                <p>版权所有：©2012 - 2016 tyrbl.com, all rights reserved 杭州天涯若比邻网络信息服务有限公司浙ICP备案号：12021152号-2 </p>
            </div>
        </div>
    </div>
    <!--遮罩层-->
    <div class="bg hide a-fadein"></div>
    <!--加入我们弹出层-->
    <div class="m-login-join hide" name="join">
        <p class="tc mt50">提交成功</p>
        <p class="tc">感谢您的支持！</p>
    </div>
    <!--注册表单-->
    <div class="m-form m-forms form-reg a-fadeinT hide " name="reg1">
        <div class="relative">
            <form action="" id="formRegister" class="mc"  >
                <h2 class="tc">注册城市合伙人</h2>

                <p>
                    <label for="phone">账&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp号*</label><input type="text" name="phone" id="regphone"
                                                                             placeholder="请输入手机号码" autocomplete="off">
                    <span class="error" name="phone_error"></span>
                </p>
                <input type="button" class="m-check m-check3" value="获取验证码" id="getCode">

                <p>
                    <label for="check">验&nbsp证&nbsp码*</label><input class="inputCheck" type="text" name="code"
                                                          placeholder="请输入验证码">
                    <span class="fl" id="inputCheck"></span>
                    <span class="error" name="code_error"></span>
                </p>

                <p>
                    <label for="">领&nbsp导&nbsp人*</label><input type="text" name="leadername" placeholder="请输入领导人姓名">
                    <span class="error" name="leadername_error"></span>
                </p>

                <p>
                    <label for="">邀&nbsp请&nbsp码*</label><input type="text" name="invite" placeholder="请输入邀请码">
                    <a class="fr a-tip-invite mt5">什么是邀请码？</a>
                    <span class="error" name="invite_error"></span>

                <div id="tip-invite" class="hide a-fadein"><img
                            src="{{URL::asset('/')}}images/citypartner/tip-invite.png" alt=""></div>
                </p>
                <p>
                    <label for="">密&nbsp&nbsp&nbsp&nbsp&nbsp码*</label><input type="password" id="password" name="password"
                                                                        placeholder="请设置密码，6-16位数字或字母（区分大小写）">
                    <span class="error" name="password_error"></span>
                </p>

                <p>
                    <label for="">确认密码*</label><input type="password" name="confirmpassword" placeholder="请确认密码">
                    <span class="error" name="confirmpassword_error"></span>
                </p>
                <button type="button" class="btn btn-login btn-reg " id="firstBtn">下一步</button>
                <a class="close tc close-reset reg-close-resset login-close" >  <button type="reset" style="background-color:#1e2832;width: 15px;height: 15px;line-height: 15px;opacity:0;filter: alpha(opacity = 0);"></button></a>

                <p class="tc mt50">点击“下一步”即表示您同意遵守 <a  class="afuwu">《无界商圈服务条例》</a></p>

                <div class="login-now">我有账号 <a href="#" id="m-reg-log">立即登入</a></div>

                <input type="hidden" name="act" value="register"/>

            </form>
        </div>
    </div>
    <!--个人资料表单-->
    <div class="m-form m-forms form-info a-fadeinB hide" name="reg2">
        <div class="relative">
            <form action="" id="formInfo" class="mc">
                <h2 class="tc ">个人资料</h2>

                <div class="m-head mc mb20 tc" id="user_avatar">
                    <div class="img-head  mb20">
                        <img src="{{isset($userinfo->avatar) ? getImage($userinfo->avatar) : '/images/citypartner/m-head.png' }}" id="user-avatar" class="jcrop-preview jcrop_preview_s" alt="">
                    </div>
                    <button class=" btn uphead btn-head" type="button">上传头像</button>
                    <p class="mt20 f12">支持JPG、GIF、PNG格式图片，建议上传正方形图片，不超过5M</p>

                </div>
                <div class=" m-head-pic mc mb20 tc hide" id="upload_avatar">
                    <div class="wrap" id="jcropdiv">
                        <div class="wl">
                            <div class="jc-demo-box" data="0">
                                <div id="target" class="jcrop_w">
                                        <img src="{{isset($userinfo->avatar) ? getImage($userinfo->avatar) : '/images/citypartner/m-head.png' }}" width="170" name="avatar" height="170"/>
                                </div>
                            </div>
                            <div class="jy-up-ch">
                                <a id="idLeft" href="" class="bch bch1"> </a>
                                <a id="idSmall" href="" class="bch bch2"> </a>
                                <a id="idBig" href="" class="bch bch3"> </a>
                                <a id="idRight" href="" class="bch bch4"> </a>
                            </div>
                        </div>
                        <div class="wr" id="preview-pane">
                            <span>头像预览：</span>
                            <div class="preview-container">
                                <div class="pre-1">
                                        <img src="{{isset($userinfo->avatar) ? getImage($userinfo->avatar) : '/images/citypartner/m-head.png' }}" name="avatar" class="jcrop-preview jcrop_preview_s" alt=""/>
                                </div>
                                <div class="pre-2">
                                        <img src="{{isset($userinfo->avatar) ? getImage($userinfo->avatar) : '/images/citypartner/m-head.png' }}" name="avatar" class="jcrop-preview jcrop_preview_s" alt=""/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-block" style="position: relative;">
                        <p class="mb0 f12">拖拽或者缩放图中的虚线方格可调整头像，注意右侧小头像浏览效果。</p>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" class="span11" name="avatar" value="{{!empty($userinfo) ? $userinfo->avatar :''}}" id="user-avatar-input"/>
                        <button class="btn btn-head " name="changeAvatar" type="button">
                            <a href="javascript:void(0);" id="uploadBtn" style="color:#fff;">换张照片</a>
                        </button>
                        <input type="file" style="width: 120px;height: 45px;position: absolute;bottom: 0px;left: 118px;opacity: 0; filter: alpha(opacity=0);cursor: pointer;" id="uploadAvatar" name="myfile" size="10" onchange="fileUpload('uploadAvatar','avatar','170:170')">                        
                        <button class="btn btn-head" id="AvatarOk" type="button">确定</button>
                    </div>
                </div>
                <p>
                    <label for="">姓&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp名*</label><input type="text" name="name" placeholder="请输入真实姓名">
                    <span class="error" name="name_error"></span>
                </p>
                <p>
                    <label for="">性&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp别*</label>
                            <span class="selbg">
                                <select name="sex">
                                    <option selected="selected" value="" class="select-no">请选择 <span id="sel"></span>
                                    </option>
                                    <option value="1">男</option>
                                    <option value="0">女</option>
                                </select>
                            </span>
                    <span class="error" name="sex_error"></span>
                </p>
                <p>
                    <label for="">所在地区*</label>
                        <span class=" selbg2">
                           <select name='province' id="province">
                               <option value="">省份</option>
                               @foreach($zoneTree as $zone)
                                   <option value="{{$zone['id']}}">{{$zone['name']}}</option>
                               @endforeach
                           </select>
                        </span>
                        <span class=" selbg2">
                            <select name="city" id="city"></select>
                        </span>
                    <span class="error" name="province_error"></span>
                </p>
                <input type="hidden" name="action" value="register2">
                <button type="button" class="btn btn-login btn-reg " id="secondBtn">完成</button>
                <a class="close tc"></a>
                <a class="close tc"></a>
            </form>
            <form id="cutAvatar" class="coords hide" style="display:none">
                <div style="display: none;">
                    <label> <input type="text" size="4" id="x1" name="x1" /></label>
                    <label >Y1 <input type="text" size="4" id="y1" name="y1" /></label>
                    <label>X2 <input type="text" size="4" id="x2" name="x2" /></label>
                    <label>Y2 <input type="text" size="4" id="y2" name="y2" /></label>
                    <label>W <input type="text" size="4" id="w" name="w" /></label>
                    <label>H <input type="text" size="4" id="h" name="h" /></label>
                    <input type="hidden" value="" name="path" id="cut-path">
                </div>
            </form>
        </div>

    </div>
    <!--注册完成-->
    <div class="m-form m-forms form-success tc  a-fadein hide" name="reg3">
        <div class="relative ">
            <p class="f26">注册完成</p>

            <p class="f26">恭喜成为天涯若比邻城市合伙人！</p>
            <a class="close tc"></a>
            <button class="btn btn-login btn-reg close " id="registerOk">OK</button>
        </div>
    </div>
    <!--设置新密码-->
    <div class="m-form m-forms form-modify tc a-fadein hide " name="reset" id="resetdiv">
        <div class="relative">
            <form action="" id="formModify" class="mc" autocomplete="off" >
                <h2 class="tc">设置新密码</h2>

                <p class="mt50">
                    <label for="">新密码*</label><input type="password" name="password"
                                                     placeholder="请设置新密码，6-16位数字或字母（区分大小写）">
                </p>

                <p>
                    <label for="">确认密码*</label><input type="password" name="confirmpassword" placeholder="请确认新密码">
                    <span class="error" name="password_error"></span>
                </p>
                <button class="btn btn-login btn-reg  " type="button" id="reset">完成</button>
                <input type="hidden" name="act" value="reset">
                <a class="close tc"></a>
            </form>
            <p class=" fr mt50 mr50">知道密码了 <a href="#" id="login-now" class="flipLink">立即登入</a></p>
        </div>
    </div>
    <!--新密码设置成功-->
    <div class="m-form m-forms form-modify-success tc hide a-fadein " name="resetOk" id="resetOkdiv">
        <div class="relative ">
            <p class="f26">密码设置成功请重新登入!</p>
            <a class="close tc"></a>
            <button class="btn btn-login btn-reg close mt100">OK</button>
        </div>
    </div>

    <!--3d翻转表单  登入和忘记密码-->
    <div id="formContainer" class="hide a-fadeinB">
        <div class="relative">
            <!--登入表单-->
            <div class="m-form form-login  " id="form-login">
                <div class="relative">
                    <form action="" id="formLogin" class="mc" autocomplete="off" >
                        <h2 class="tc">城市合伙人</h2>

                        <p class="mt50">
                            <label for="">账号</label><input type="text" name="phone" placeholder="请输入账号" value="@if(isset($username)){{$username}}@endif">
                            <span class="error" name="phone_error"></span>
                        </p>

                        <p>
                            <label for="">密码</label><input type="password" name="password" placeholder="请输入密码">
                            <span class="error" name="password_error"></span>
                        </p>

                        <p class="pl50">
                                <span class="uncheckbox " id="remNameSpan">
                                    <input type="checkbox" class="login-checkbox" id="remName" value="0" name="remName">
                                </span>
                            <label for="remName" class="checkbox">记住账号</label>

                                <span class="uncheckbox " id="remPassSpan">
                                    <input type="checkbox" class="login-checkbox" id="remPass" value="0" name="remPass">
                                </span>
                            <label for="remPass" class="checkbox">记住密码</label>
                        </p>

                        <a class="close2 tc login-close"></a>
                        <button class="btn btn-login tc f16  mt50" id="dologin" type="button"> 登入</button>
                        <p class="mt50 forget">没有账号？<a href="#" id="m-reg">立即注册</a> <a class="fr flipLink">忘记密码</a></p>
                        <input type="hidden" name="act" value="login">
                    </form>
                </div>
            </div>
            <!--忘记密码表单-->
            <div class="m-form form-forget" id="form-forget" name="forgetPwd">
                <div class="relative">
                    <form action="" id="formForget" class="mc" autocomplete="off" >
                        <h2 class="tc">找回密码</h2>

                        <p class="mt50">
                            <label for="">账&nbsp&nbsp&nbsp&nbsp号</label><input type="text" name="phone" id="forgetphone"
                                                                               placeholder="请输入账号">
                            <span class="error" name="phone_error"></span>
                        </p>
                        <input type="button" class="m-check m-check2" value="获取验证码" id="getcode" style="top:131px;right:91px">

                        <p>
                            <label for="check">验证码*</label><input class="inputCheck" type="text" name="code"
                                                                  placeholder="请输入验证码">
                            <span class="fl" id="inputCheck"></span>
                            <span class="error" name="code_error"></span>
                        </p>
                        <input type="hidden" name="act" value="forget_partner_pwd"/>
                        <button type="button" class="btn btn-login btn-reg " id="forgetPwd">下一步</button>
                        <p class="mt50 fr mr-50 ">知道密码了 <a href="#" class="" id='login-suden'>立即登入</a></p>
                        <a class="close2 tc"></a>
                    </form>
                </div>
            </div>
        </div>

    </div>
     <!-- 服务条款 -->
            <div class="fuwu hide" >
                <h1>无界商圈服务条款</h1>
                <div style="width: 100%;height: 570px;background-color: #fff;padding-top: 10px;">
                     <div class="content">
                         <h3>一． 服务条款的确认和接纳</h3>
                         <p>
                             在您决定成为无界商圈会员前，请仔细阅读本会员服务条款。 您必须在完全同意如下条款的前提下，才能进行会员注册程序，您只有在成为无界商圈会员后，才能使用我们所提供的服务。用户在享受无界商圈服务时必须完全、严格遵守本服务条款。
                         </p>
                         <h3>二． 服务条款的完善和修改</h3>
                         <p>
                             无界商圈根据互联网的发展和中华人民共和国有关法律、法规的变化，不断地完善服务质量并依此修改无界商圈会员服务条款。用户的权利以及义务的表述，均以最新的服务条款为准。
                         </p>
                         <h3>三．无界商圈会员资格的有关规定</h3>
                         <p>
                             无界商圈根据互联网的发展和中华人民共和国有关法律、法规的变化，不断地完善服务质量并依此修改无界商圈会员服务条款。用户的权利以及义务的表述，均以最新的服务条款为准。
                         </p>
                         <h3>二． 服务条款的完善和修改</h3>
                         <p>
                             1. 无界商圈会员在注册会员资格时，所提交的资料必须真实有效，否则无界商圈有权拒绝其申请或者撤销其会员资格，并不予任何赔偿或者退还任何已缴纳的收费服务费用。会员的个人资料发生变化时，应及时修改注册的个人资料，否则由此造成的会员权利不能全面有效地行使的责任由会员自己承担，无界商圈有权因此取消其会员资格，并不予任何赔偿或者退还任何已缴纳的收费服务费用。
                         </p>
                         <p>
                             2. 本认证资格、会员资格只限本用户名使用，不得转让到其他用户名上。
                         </p>
                         <p>
                             3. 若会员违反本服务条款的规定，无界商圈将有权取消该会员的会员资格而无须给与任何补偿，也不予退还任何已缴纳的收费服务费用。
                         </p>
                         <p>4. 会员对本服务条款的修改有任何异议，可自动放弃会员资格，但不退还任何已缴纳的收费服务费用。
                         </p>
                         <h3>四．会员的账号、密码</h3>
                         <p>
                             1. 会员必须妥善保管其用户名及密码，非我方原因引发的密码被盗或泄露造成的全部责任和损失均由您本人承担，无界商圈概不负责。
                         </p>
                         <p>
                             2. 用户因忘记密码或密码被盗向无界商圈查询密码时，必须提供完全正确的注册信息，否则无界商圈有权本着为会员账号安全保密的原则不予告知。
                         </p>
                         <p>
                             3. 会员的用户名和密码只能供会员本人使用，不得以任何形式转让或授权他人使用，如果发现同一账号和密码在同一时间内被多人同时登陆使用，出于账户安全考虑，无界商圈有权冻结此账号。
                         </p>
                         <p>
                             4. 会员用户名中不得含有任何威胁、恐吓、漫骂、庸俗、亵渎、色情、淫秽、非法、反动、攻击性、伤害性、骚扰性、诽谤性、辱骂性的或侵害他人知识产权的文字。
                         </p>
                         <h3>五． 会员隐私制度</h3>
                         <p>尊重会员的个人隐私是无界投融的一项基本政策，无界商圈保证不公开或披露会员的个人信息，也不会私自更改会员的注册信息。 </p>
                         <h3>六． 拒绝提供担保</h3>
                         <p>
                             1. 您须明确同意无界商圈所有网络平台的使用由您个人承担风险。无界商圈明确表示不提供任何类型的担保，不论是明确的或隐含的。
                         </p>
                         <p>
                             2. 无界商圈不担保所提供的服务一定能满足您的要求，也不担保服务不会中断，对服务的及时性、安全性、错误发生都不作担保。无界商圈拒绝提供任何担保，包括信息能否准确、及时、顺利的传送。
                         </p>
                         <p>
                             3. 您理解并接受任何信息资料(下载或通过服务取得)，取决于您自己并由您承担系统受损或资料丢失的所有风险和责任。无界商圈对您在会员服务中得到的任何免费及收费服务或交易进程，都不作担保。您不会从无界商圈收到口头或书写的意见或信息，也不会在这里作明确担保。
                         </p>
                         <h3>七． 免责条款</h3>
                         <p>
                             1. 无界商圈对任何直接、间接、偶然、特殊及继起的损害不负责任，不予赔偿。这些损害来自：网络故障；不正当下载、使用无界商圈服务内容；在网上购买商品或类似服务；在网上进行交易，非法使用服务等。
                         </p>
                         <p>
                             2. 会员在使用无界商圈所提供的服务时，如遭受任何人身或财务的损失、损害或伤害，不论原因如何，无界商圈均不负责任。由于用户将个人密码告知他人或与他人共享注册帐户，由此导致的任何个人资料泄露，本网站不负任何责任。
                         </p>
                         <p>
                             3. 会员须对其自身在使用无界商圈所提供的服务时的一切行为、行动（不论是否故意）负全部责任。
                         </p>
                         <p>
                             4. 用户应对自己在无界商圈上发表的内容负全部责任，并承诺提交的姓名、身份证号码、常住地址、联系方式等个人资料真实可靠。
                         </p>
                         <p>
                             5. 对于因服务器的死机、网络的故障、数据库故障、软件升级等问题造成的服务中断和对会员个人数据及资料造成的损失，无界商圈不负责任，亦不予补偿。

                         </p>
                         <p>
                             6. 当政府司法机关依照法定程序要求本网站披露个人资料时，我们将根据执法单位之要求或为公共安全之目的提供个人资料。在此情况下之任何披露，本网站均得免责。
                         </p>
                         <p>
                             7. 用户定制包月服务后,包月期中含服务器维修、调整、升级时间，无界商圈对服务器维修、调整、升级所占用的时间，不予补偿并保留解释权。
                         </p>
                         <h3>
                             八． 关于中断或终止服务：
                         </h3>
                         <p>
                             1. 因发生不可抗拒的事由，如政府行为、不可抗力，导致会员服务无法继续提供，无界商圈将尽快通知您，但不承担由此对您造成的任何损失或退还任何已缴纳的收费服务费用。
                         </p>
                         <p>
                             2. 如用户违反被服务条款中的内容，无界商圈有权取消用户的会员资格，并中止向其继续提供服务。
                         </p>
                         <h3>
                             九． 服务内容的版权：
                         </h3>
                         <p>
                             无界商圈提供的会员服务内容（包括但不限于：文字、照片、图形、图像、图表、声音、FLASH 动画、视讯、音频等），均受版权保护：由无界商圈版 权所有，或由第三方授权使用。用户不能擅自复制、再造这些内容、或创造与内容有关的派生产品。 凡以任何方式登陆本网站或直接、间接使用本网站资料者，视为自愿接受本网站声明的约束。本声明未涉及的问题参见国家有关法律法规，当本声明与国家法律法规 冲突时，以国家法律法规为准。<br>

                         本网站之声明以及其修改权、更新权及最终解释权均属杭州天涯若比邻网络信息服务有限公司所有。

                         </p>
                     </div>
                </div>
                   <a href="#" class="queren">确认</a>
            </div>



</div>

<script src="{{URL::asset('/')}}js/citypartner/jquery-1.8.3.min.js"></script>
<script src="http://static.runoob.com/assets/jquery-validation-1.14.0/dist/jquery.validate.min.js"></script>
<script src="{{URL::asset('/')}}js/citypartner/messages_zh.js"></script>
<script src="{{URL::asset('/')}}js/citypartner/additional-methods.js"></script>

<!--头像裁剪-->
<script src="{{URL::asset('/')}}js/citypartner/jquery.jcrop.js"></script>
<script src="{{URL::asset('/')}}js/citypartner/basic.js"></script>
<script src="{{URL::asset('/')}}js/citypartner/script.js"></script>
<script src="{{URL::asset('/')}}js/common.js"></script>
<script src="{{URL::asset('/')}}js/citypartner/index.js"></script>
<script type="text/javascript" src="/js/citypartner/common.js"></script>
<script type="text/javascript" src="/js/citypartner/ajaxfileupload.js"></script>
<script>
    $("#province").change(function () {
        $("#city").empty();
        $.post("{{url('citypartner/zone/children')}}", {
            id: $(this).val(),
            //_token: $('input[name="_token"]').val()
        }, function (data) {
            if (data.status) {
                var html = '<option value="">城市</option>';
                $.each(data.message, function (i, n) {
                    html += "<option value='" + n.id + "'>" + n.name + "</option>";
                });
                $("#city").html(html);
            }
        }, 'json');
    });
</script>

</body>
</html>
