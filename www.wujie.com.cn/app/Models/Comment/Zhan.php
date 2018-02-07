<?php
/**评论模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Comment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Agent\Agent;
class Zhan extends Model
{
    public $timestamps = false;
    protected $dateFormat = 'U';

    protected $table = 'comment_zhan';

    //黑名单
    protected $guarded = [];

    public function comment()
    {
        return $this->belongsTo(Entity::class, 'comment_id', 'id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'uid', 'id');
    }
    /**
     * 为一个评论点赞
     *
     * @return array|bool
     */
    static function zhan(Array $data)
    {
        $exist = self::where('uid', $data['uid'])->where('comment_id', $data['comment_id'])->where('status', 1)->first();
        if (is_object($exist)) {
            return false;
        }

        $result = self::create(
            [
                'uid'        => $data['uid'],
                'comment_id' => $data['comment_id'],
                'status'     => 1,
                'zhan_created_at' => time(),
            ]
        );

        return $result;
    }


    /**
     * 为一个评论取消点赞
     *
     * @return array|bool
     */
    static function unZhan(Array $data)
    {
        $exist = self::where('uid', $data['uid'])->where('comment_id', $data['comment_id'])->first();
        if (!is_object($exist) || $exist->status==0) {
            return false;
        }

        $result = self::create(
            [
                'uid'        => $data['uid'],
                'comment_id' => $data['comment_id'],
                'status'     => 0
            ]
        );

        return $result;
    }
}