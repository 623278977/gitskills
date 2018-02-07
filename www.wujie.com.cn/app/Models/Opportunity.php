<?php
/**
 * 发布商机模型
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 11:10
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model{

    protected function getDateFormat()
    {
        return date(time());
    }

    protected $table = 'opportunity';

    protected $fillable = array('uid', 'content','phone','name','created_at','updated_at','is_submit');

    public function maker()
    {
        return $this->hasOne('App\Models\Maker\Entity', 'id', 'maker_id');
    }
    public function user()
    {
        return $this->hasOne('App\Models\User\Entity', 'uid', 'uid');
    }
    public function industrys()
    {
        return $this->belongsToMany('App\Models\Industry','opportunity_industry','opportunity_id','industry_id');
    }
    public function activitys()
    {
        return $this->belongsToMany('App\Models\Activity\Entity','opportunity_activity','opportunity_id','activity_id');
    }
    public function favorite($uid=0){
        if($uid){
            return $this->hasMany('App\Models\User\Favorite','post_id')->where(array(
                'model'=>'opportunity',
                'uid'=>$uid
            ));
        }else{
            return $this->hasMany('App\Models\User\Favorite','post_id')->where(array(
                'model'=>'opportunity'
            ));
        }
    }
    static function getRow($where){
        return self::where($where)->where('status',1)->first();
    }
    static function getRows($where,$page=0,$pageSize=10,$params){
        $query=self::where($where)->where('opportunity.status','=','1');
        if(array_key_exists('keyword',$params))
            $query->where('opportunity.subject','like','%'.$params['keyword'].'%');
        if(array_key_exists('zone_id',$params)){
            if(array_key_exists('quyu',$params)){
                $zone_id=Zone::cityAliasZoneid($params['quyu']);
                $zone_id=array_merge($zone_id,$params['zone_id']);
            }else{
                $zone_id=$params['zone_id'];
            }
//            $query->leftJoin('maker as m','m.id','=','opportunity.maker_id')
//                ->whereIn('m.zone_id',$zone_id);
            $query->whereIn('opportunity.zone_id',$zone_id);
        }else{
            if(array_key_exists('quyu',$params)){
                $zone_id=Zone::cityAliasZoneid($params['quyu']);
                $query->whereIn('opportunity.zone_id',$zone_id);
//                $query->leftJoin('maker as m','m.id','=','opportunity.maker_id')
//                    ->whereIn('m.zone_id',$zone_id);
            }
        }
        if(array_key_exists('industry_id',$params))
            $query->leftJoin('opportunity_industry as oi','oi.opportunity_id','=','opportunity.id')
                ->whereIn('oi.industry_id',$params['industry_id'])->groupBy('opportunity.id');
        return $query->Orderby('opportunity.recommend','desc')->skip($page*$pageSize)->take($pageSize)->get();
    }
    static function getBase($opportunity){
        if(!isset($opportunity->id)) return array();
        $types=Type::cache();
        $data=array();
        $data['subject']=$opportunity->subject;
        $data['recommend']=$opportunity->recommend;
        $data['type']=$opportunity->type;
        if($opportunity->type=='park'){//园区
            $data['park_level']=isset($types['park_level'][$opportunity->park_level])?$types['park_level'][$opportunity->park_level]:'';//全区级别
            $data['park_type']=isset($types['park_type'][$opportunity->park_type])?$types['park_type'][$opportunity->park_type]:'';//全区类型
            $data['park_attr']=isset($types['park_attr'][$opportunity->park_attr])?$types['park_attr'][$opportunity->park_attr]:'';//全区属性
        }else{//政府
            $data['attract_invest_way']=isset($types['attract_invest_way'][$opportunity->attract_invest_way])?$types['attract_invest_way'][$opportunity->attract_invest_way]:'';//融资方式
        }
        $data['view']=$opportunity->view;
        $data['id']=$opportunity->id;
        $data['url']=createUrl('business/goverment',array('id'=>$opportunity->id,
            'pagetag'=>config(
            'app.opportunity_detail'
        )));
        $data['maker_subject']=$opportunity->maker?$opportunity->maker->subject:'';
//        $data['address']=isset($opportunity->maker->id)?$opportunity->maker->address:'';
        $data['address']=$opportunity->address;
        $data['dapartment']=$opportunity->dapartment;
//        $data['intro'] = strip_tags($opportunity->park_info);
        $data['industry']=implode(" | ",$opportunity->industrys->lists('name')->toArray());
        return $data;
    }

    /**
     * 是否首次发布商机
     * @param $uid
     * @return bool
     */
    static function first($uid){
        $opp = self::where('uid',$uid)
            ->where('is_submit',1)
            ->get();
        if(count($opp)==1) return true;
        return false;
    }
}