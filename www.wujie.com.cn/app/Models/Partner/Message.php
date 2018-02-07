<?php

namespace App\Models\Partner;

use Illuminate\Database\Eloquent\Model;
use App\Models\CityPartner\Entity as User;
use DB;

class Message extends Model
{
    protected $table = 'partner_message';
    protected $fillable = ['uid', 'title', 'content', 'type', 'post_id', 'created_at', 'updated_at'];
    public $timestamps = true;

    public function cityPartner()
    {
        return $this->hasOne('App\Models\City\Partner', 'uid', 'uid');
    }

    public static function getCount($uid)
    {
        return self::where('uid', $uid)->where('is_read', 0)->count();
    }

    /**
     * 新团队成员加入
     * @param $leader
     * @param $invite_token
     * @param $user
     */
    static function youHaveNewbie($leader, $invite_token, $user)
    {
        $leaderInfo = DB::table('city_partner')
            ->where('realname', $leader)
            ->where('invite_token', $invite_token)
            ->first();
        $newbie = User::where('username', $user)->first();
        $user = $newbie->realname ?: $user;
        $messageid = DB::table('partner_message')->insertGetId(
            [
                'uid' => $leaderInfo->uid,
                'title' => '您有新的团队成员',
                'content' => "用户'<span style='color: #ff6633;'>$user</span>'加入您的团队,点击<a href='/citypartner/myteam/index' style='color: #23a4f8;'>查看详情</a>",
                'type' => 'newTeamer',
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );
        return $messageid;
    }

    /**
     * 新加入,欢迎信息
     * @param $uid
     * @return mixed
     */
    static function welcome($uid)
    {
        $welcomeid = DB::table('partner_message')->insertGetId(
            [
                'uid' => $uid,
                'title' => '欢迎加入无界商圈',
                'content' => "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;感谢您的加入，成为城市合伙人与我们联合共赢！<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;无界商圈的OVO运营中心联合分布在全国各地，在这里我们为您创建一个基于本地，面向全国，充满商机活动的线上商圈；开通OVO中心将获得更多的权益；您可以与我们一起合办活动，也可以自己举办本地活动；<br/>  如有疑问，请联系服务热线：400-011-0061",
                'type' => 'newMember',
                'created_at' => time(),
                'updated_at' => time(),
            ]
        );
        return $welcomeid;
    }

    /**
     * 新会员加入
     * @param $user
     * @param $maker_id
     * @throws Exception
     */
    static function newMemberJoinYou($user, $maker_id)
    {
        try {
            $netWorkInfo = DB::table('maker as m')
                ->join('city_partner as cp', 'm.partner_uid', '=', 'cp.uid')
                ->where('m.id', $maker_id)
                ->first();
            if (!$netWorkInfo) {
                return;
                //throw new Exception('网点不存在');
            }
            DB::table('partner_message')->insert(
                [
                    'uid' => $netWorkInfo->uid,
                    'title' => '您有新的会员',
                    'content' => "用户'<span style='color: #ff6633;'>{$user->nickname}</span>'成为您的会员,点击<a href='/citypartner/maker/memberdetail?uid=$user->uid' style='color: #23a4f8;'>查看详情</a>",
                    'type' => 'newMember',
                    'created_at' => time(),
                    'updated_at' => time(),
                ]
            );
        } catch (\exception $e) {
            return;
        }
    }
}
