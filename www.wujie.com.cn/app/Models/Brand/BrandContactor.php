<?php
/**活动模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models\Brand;

use App\Models\Agent\Agent;
use \DB;
use Monolog\Handler\CouchDBHandlerTest;
use Illuminate\Database\Eloquent\Model;

class BrandContactor extends Model
{

    public $timestamps = true;

    protected $table = 'brand_contactor';

    protected $dateFormat = 'U';

    //黑名单
    protected $guarded = [];
    //关联经纪人
    public function agent(){
        return $this->belongsTo(Agent::class , 'agent_id' , 'id');
    }

    /**
     * 功能描述：按照一定规则匹配一个商务
     *
     * 参数说明：
     *
     * 返回值：
     * @return  如果匹配到：返回商务model，如果没有返回带['non_reversible' => '']
     *
     * 实例：
     * 结果：
     *
     * 作者： shiqy
     * 创作时间：@date 2018/1/31 0031 上午 10:41
     */
    public static function selectContactor(){
        //目前先做成随机选择商务，随后再改
        $contactors = self::with('agent')->whereHas('agent' , function ($query){})
            ->select('agent_id')->get();
        $selectContactor['non_reversible'] = '';
        if(count($contactors)){
            $selectContactor = collect($contactors)->random();
        }
        return is_object($selectContactor) ? $selectContactor->agent : $selectContactor ;
    }
}