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

class Industry extends Model{

    public $timestamps = false;

    protected $table = 'user_industry';

    protected $fillable = array('uid', 'industry_id');

    public function industry(){
        return $this->belongsTo('App\Models\Industry', 'industry_id', 'id');
    }

    /**
     * @param $user
     * @param int $cache
     * @return bool
     * industry_user_111  某个人的行业缓存
     */
    static function cache($user,$cache =1) {
        if(!isset($user->uid)) return array();
        $data = Cache::has('industry_user_'.$user->uid) ? Cache::get('industry_user_'.$user->uid) : false;
        if ($data === false || $cache) {
            $data=array();
//            $industrys=$user->industrys;
//            if(count($industrys)){
            foreach ($user->industrys as $v) {
                if ($v->industry) {
                    $data[] = [
                        'industry_id' => $v->industry_id,
                        'name' => $v->industry->name
                    ];
                }
            }
            Cache::put('industry_user_'.$user->uid, $data,1440);
//            }

        }
        return $data;
    }

    /**
     * @param $user
     * 获取用户的关注行业
     * id 数组
     * name 数组
     * all  id，name 都有
     */
    static function getUserIndustry($user,$type='id'){
        $industrys=self::cache($user);
        if(!count($industrys))
            return array();
        $data=array();
        if($type=='id'){
            $data=array_column($industrys, 'industry_id');
        }elseif($type=='name'){
            $data=array_column($industrys, 'name');
        }else{
            foreach($industrys as $k=>$v){
                $data[$k]['name']=$v['name'];
                $data[$k]['id']=$v['industry_id'];
            }
        }
        return $data;
    }

    /***
     * @param $user
     * @param $array
     * 插入或者更新
     * 关注行业数据库
     * @return bool
     */
    static function dealUserIndustry($user,$array){
        if(!is_array($array))
            return false;
        $oldIndustrys=self::getUserIndustry($user);
        $newIndustrys=$array;
        $deleteArray=array_diff($oldIndustrys,$newIndustrys);
        $addArray=array_diff($newIndustrys,$oldIndustrys);

        foreach($deleteArray as $k=>$v){
            self::where('uid',$user->uid)->where('industry_id',$v)->delete();
        }
        foreach($addArray as $k=>$v){
            self::create(array(
                'uid'=>$user->uid,
                'industry_id'=>$v
            ));
        }
        return true;
    }

    /**
     * 添加用户喜欢的分类
     *
     * @param $uid
     * @param $cate_id
     *
     * @return bool
     */
    public static function userFondCates($uid, $cate_id)
    {
        if (!is_array($cate_id)) return false;

        UserFondCate::where('uid', $uid)->delete();
        foreach ($cate_id as $k => $v) {
            UserFondCate::insert([
                'uid'     => $uid,
                'cate_id' => $v ]);
        }

        return true;
    }
}