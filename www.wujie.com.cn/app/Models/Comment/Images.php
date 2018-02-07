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

class Images extends Model
{
    public $timestamps = true;
    protected $dateFormat = 'U';

    protected $table = 'comment_images';

    //黑名单
    protected $guarded = [];

}