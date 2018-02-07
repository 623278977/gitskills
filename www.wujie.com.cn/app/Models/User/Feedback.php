<?php
/**用户关注行业模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Feedback extends Model{

    protected function getDateFormat(){
        return date(time());
    }

    protected $table = 'user_feedback';

    protected $guarded = [];

}