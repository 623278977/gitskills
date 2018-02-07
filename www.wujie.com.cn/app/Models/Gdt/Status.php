<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Gdt;

use Illuminate\Database\Eloquent\Model;
use \DB, Closure, Input;
use Validator;


class Status extends Model
{

    protected $dateFormat = 'U';
    //黑名单
    protected $guarded = [];

    public $timestamps = false;

    protected $table = 'gdt_status';



}