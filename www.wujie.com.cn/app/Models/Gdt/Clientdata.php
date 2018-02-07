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


class Clientdata extends Model
{

    protected $dateFormat = 'U';
    protected $connection = 'razor';

    protected $table = 'clientdata';




}