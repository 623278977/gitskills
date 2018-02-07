<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */

namespace App\Models\Contract;


use App\Models\Brand\Entity as Brand;
use App\Models\User\Entity as User;
use \DB;
use Illuminate\Database\Eloquent\Model;

class ContractSuccessCertify extends Model
{

    public $timestamps = true;

    protected $table = 'contract_success_certify';

    protected $dateFormat = 'U';

    public static $instance = null;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }


    //黑名单
    protected $guarded = [];


    //关联品牌
    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    //关联用户
    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

}