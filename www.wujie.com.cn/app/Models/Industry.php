<?php
/**关注行业模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Industry extends Model{

    public $timestamps = false;

    protected $table = 'industry';

    protected $fillable = array('uid', 'industry_id');

    /**
     * 返回缓存或者更新缓存
     *industry 整体缓存
     * @return array
     */
    static function cache($cache = 0) {

        $data = Cache::has('industry') ? Cache::get('industry') : false;
        if ($data === false || $cache) {
            $data = array();
            $industrys = self::whereRaw('status = 1')->orderBy('sort','desc')->get();
            $i=0;
            foreach ($industrys as $v) {
                $data[$i]['name'] = $v->name;
                $data[$i]['id'] = $v->id;
                $data[$i]['hot'] = $v->hot;
                $i++;
            }
            Cache::put('industry', $data, 1440);
        }
        return $data;
    }
}