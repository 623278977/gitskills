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

class BrandContractFee extends Model
{

    public $timestamps = true;

    protected $table = 'brand_contract_fee';

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

}