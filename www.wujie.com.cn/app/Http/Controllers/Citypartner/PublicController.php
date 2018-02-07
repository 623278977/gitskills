<?php
/**
 * 城市合伙人ovo中心   --数据中心 整个弃用  不处理   yaokai
 */
namespace App\Http\Controllers\Citypartner;

use App\Http\Controllers\Api\IdentifyController;
use App\Models\CityPartner\Entity as CityPartner;
use App\Models\Partner\Message;
use App\Models\Zone;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB, Auth, Session, Cookie, Crypt, Hash, Cache;

class PublicController extends Controller
{
    protected $userinfo = null;
    protected $mid = null;

    public function __construct()
    {
        config()->set('auth.model', \App\Models\CityPartner\Entity::class);
        config()->set('auth.table', 'city_partner');
        if (Auth::check()) {
            $this->userinfo = Auth::user();
            $this->mid = $this->userinfo->uid;
        }
    }

    /**
     * 注册页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function anyIndex(Request $request)
    {
        $param = $request->all();
        $articles = DB::table('city_partner_article')
            ->where('status', 'show')
            ->orderby('sort', 'desc')
            ->limit(2)
            ->get();
        if (isset($_COOKIE['username'])) {
            $username = Crypt::decrypt($_COOKIE['username']);
        }
        $zones = Zone\Entity::cache(0);
        $zoneTree = toTree($zones, 'id', 'upid', 'children');
        return view('citypartner.public.index', compact('param', 'articles', 'username', 'zoneTree'))->with('userinfo', $this->userinfo);
    }

    /**
     * 登陆
     * @param Requests\PublicRequest $request
     * @return string
     */
    public function postLogin(Requests\PublicRequest $request)
    {
        $param = $request->all();
        $remName = $request->input('remName', 0);
        $remPass = $request->input('remPass', 0);
        $ip = $request->ip();
        if (!empty($param['phone'])) {
            if (!checkMobile($param['phone'])) return AjaxCallbackMessage(['phone_error','手机号格式不对!'], false);
            $userInfo = CityPartner::checkUser($param);
            if ($userInfo['status'] == 0) {
                return AjaxCallbackMessage($userInfo['message'], false);
            }
            if (Auth::attempt(['username' => trim($param['phone']), 'password' => trim($param['password'])], $remPass)) {
                //更新登陆信息
                $this->upDateLoginInfo($userInfo['data'], $ip);
            }
            if ($remName) {
                setcookie('username', Crypt::encrypt($param['phone']), time() + 7 * 24 * 60 * 60);
            }
            return AjaxCallbackMessage('登陆成功', true);
        }
    }

    /**
     * 更新登陆信息
     * @param $param
     * @param $ip
     */
    private function upDateLoginInfo($param, $ip)
    {
        $phone = $param->username;
        $data = [
            'last_ip' => $ip,
            'last_time' => time(),
            'login_count' => $param->login_count + 1,
        ];
        CityPartner::upDateLoginInfo($phone, $data);
    }

    /**
     * 注册(第一步)
     * @param Requests\PublicRequest $request
     * @return string
     */
    public function postRegister(Requests\PublicRequest $request)
    {
        $param = $request->all();
        if (!empty($param['phone'])) {
            if (!checkMobile($param['phone'])) return AjaxCallbackMessage(['phone_error','手机号格式不对'], false, '');
        }
        //验证码验证
        $param['act'] = 'citypartner_register';
        $res = $this->checkVerifyCode($param);
        if ($res == '验证码过期!' || $res == '验证码错误!' || $res == false)
            return AjaxCallbackMessage(['code_error', $res], false);
        //邀请码验证
        $res = $this->checkLeader($param['leadername'], $param['invite']);
        if (!$res) {
            return AjaxCallbackMessage(['invite_error', '领导人姓名与邀请码不一致!'], false);
        } else {
            $param['p_uid'] = $res;
            //邀请码
            $param['invite_token'] = uniqid($res);
        }
        $id = CityPartner::register($param);
        if ($id) {
            //注册成功,给领导发消息
            $messageid = Message::youHaveNewbie($param['leadername'], $param['invite'], $param['phone']);
            Cache::put('youHaveNewbie', $messageid, 60 * 5);
            //注册成功,欢迎信息
            Message::welcome($id);
            return AjaxCallbackMessage($id, true);
        }
        return AjaxCallbackMessage('', false);
    }

    /**
     * 注册(第二步)
     * @param Requests\PublicRequest $request
     * @return string
     */
    public function postRegister2(Requests\PublicRequest $request)
    {
        $param = $request->all();
        $id = $param['partner_id'];
        $data = [
            'zone_id' => $param['city'],
            'realname' => $param['name'],
            'gender' => $param['sex'],
            'avatar' => $param['avatar'],
            'reg_time' => time(),
            'reg_ip' => $request->ip(),
            'login_count' => 1,
            //'status' => 1,
        ];
        $res = CityPartner::where('uid', $id)->update($data);
        if ($res !== false) {
            $messageid = Cache::get('youHaveNewbie');
            $user = $data['realname'];
            $content = "用户'<span style='color: #ff6633;'>$user</span>'加入您的团队,点击<a href='/citypartner/myteam/index' style='color: #23a4f8;'>查看详情</a>";
            Message::where('id', $messageid)->update(['content' => $content]);
            Auth::loginUsingId($id);
            return AjaxCallbackMessage('保存成功', true);
        }
        return AjaxCallbackMessage('保存失败', false);
    }


    /**
     * 发送验证码
     * @param Request $request
     * @return string
     */
    public function postSendcode(Request $request)
    {
        $obj = new IdentifyController();
        $type = $request->input('type', '');
        $phone = $request->input('username', '');
        if ($type == 'forget_partner_pwd') {
            if (empty($phone))
                return AjaxCallbackMessage(['phone_error', '账号不能为空!'], false);
            if (!CityPartner::getCount(['username' => $phone]))
                return AjaxCallbackMessage(['phone_error', '该账号未注册!'], false);
        }
        if ($type == 'citypartner_register') {
            if (empty($phone))
                return AjaxCallbackMessage(['phone_error', '账号不能为空!'], false);
            if (CityPartner::getCount(['username' => $phone]))
                return AjaxCallbackMessage(['phone_error', '该账号已注册!'], false);
        }
        return $obj->postSendcode($request);
    }

    /**
     * 验证码验证
     * @param $param
     * @return string
     */
    public function checkVerifyCode($param)
    {
        $obj = new IdentifyController();
        return $obj->checkVerifyCode($param);
    }

    /**
     * 邀请码验证
     * @param $username
     * @param $invite
     * @return bool
     */
    private function checkLeader($username, $invite)
    {
        $obj = new CityPartner();
        return $obj->checkLeader($username, $invite);
    }

    /**
     * 找回密码
     * @param Requests\PublicRequest $request
     * @return string
     */
    public function postForgetpwd(Requests\PublicRequest $request)
    {
        $param = $request->all();
        if (!empty($param['phone'])) {
            if (!checkMobile($param['phone'])) return AjaxCallbackMessage(['phone_error','手机号格式不对'], false, '');
        }
        if (!CityPartner::getCount(['username' => $param['phone']]))
            return AjaxCallbackMessage(['phone_error', '该账号未注册!'], false);
        //验证码验证
        $res = $this->checkVerifyCode($param);
        if ($res == '验证码过期!' || $res == '验证码错误!' || $res == false)
            return AjaxCallbackMessage(['code_error', $res], false);
        return AjaxCallbackMessage('', true);
    }

    /**
     * 重置密码
     * @param Requests\PublicRequest $request
     * @return string
     */
    public function postReset(Requests\PublicRequest $request)
    {
        $param = $request->all();
        $data = [
            'password' => Hash::make($param['password']),
        ];
        $res = CityPartner::where('username', $param['phone'])
            ->update($data);
        if ($res !== false) return AjaxCallbackMessage('更改成功', true);
        return AjaxCallbackMessage('更改失败', false);
    }

    /**
     * 登出账号
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function anyLoginout(Request $request)
    {
        Auth::logout();
        return redirect('/citypartner/public/index');
    }

    /**
     * 关于我们
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAboutus(Request $request)
    {
        $count = null;
        $type = $request->input('type', '');
        if ($this->mid) {
            $count = Message::getCount($this->mid);
        }
        return view('citypartner.public.about', compact('type', 'count'))
            ->with('partner', $this->userinfo)
            ->with('partner_uid', $this->mid)
            ->with('count', $count);
    }

    /**
     * 推荐阅读
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSuggestedread(Request $request)
    {
        $count = null;
        if ($this->mid) {
            $count = Message::getCount($this->mid);
        }
        $id = $request->input('id');
        $article = DB::table('city_partner_article')->where('id', $id)->first();
        if(isset($article)){
            $article->content = htmlspecialchars_decode($article->content);
        }
        return view('citypartner.public.suggestedRead')
            ->with('partner', $this->userinfo)
            ->with('partner_uid', $this->mid)
            ->with('count', $count)
            ->with('article', $article?:'');
    }
}
