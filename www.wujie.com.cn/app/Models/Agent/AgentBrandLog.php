<?php

namespace App\Models\Agent;

use App\Models\Zone\Entity as Zone;
use Illuminate\Database\Eloquent\Model;

class AgentBrandLog extends Model
{
    protected $table = 'agent_brand_log';
    protected $fillable = ['agent_brand_id', 'action', 'created_at', 'post_id','type'];
    public $timestamps = false;
    protected $dateFormat = 'U';

    public function agent_brand(){
        return $this->belongsTo(AgentBrand::class ,'agent_brand_id','id');
    }


    /**
     * 找出某个视频、资讯相关经纪人阅读信息
     * @param $action  video视频 news资讯
     * @param $post_id  视频/资讯id
     * @param $page_size  是否分页
     * return
     */
    public static function clockList($action, $post_id, $page_size = false)
    {
        $builder = self::with(['agent_brand', 'agent_brand.agent' => function ($query) {
            $query->select('id', 'realname', 'avatar', 'zone_id');
        }, 'agent_brand.agent.zone' => function ($query) {
            $query->select('id', 'name', 'upid');
        }])
            ->where('type', $action)
            ->where('action', '2')
            ->where('post_id', $post_id)
            ->orderBy('created_at', 'desc');//时间倒序排
        if ($page_size) {
            $ret = $builder->paginate($page_size);
        } else {
            $ret = $builder->get();
        }
        if ($ret) {
            foreach ($ret as $k => $v) {
                $item[$k]['agent_id'] = $v->agent_brand->agent->id;//经纪人id
                $item[$k]['realname'] = starReplace($v->agent_brand->agent->realname);//隐藏用户真实姓名
                $zone1 = str_replace('省', '', Zone::pidName($v->agent_brand->agent->zone->id));//一级地区
                $zone2 = str_replace('市', '', $v->agent_brand->agent->zone->name);//二级地区名
                if ($zone1) {
                    $item[$k]['zone_name'] = $zone1 . ' ' . $zone2;
                } else {
                    $zone2 = str_replace('省', '', $zone2);//直接定位省的地区名
                    $item[$k]['zone_name'] = $zone2;
                }
                $item[$k]['avatar'] = getImage($v->agent_brand->agent->avatar, 'avatar');//用户头像
                $item[$k]['created_at'] = $v->created_at;//打卡时间
            }
            $data['total'] = $ret->total();//条数
            $data['data'] = $item;
        }
        return $data;
    }








}