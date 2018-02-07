<?php

namespace App\Models\Agent\Academy;

use Illuminate\Database\Eloquent\Model;

class AgentLecturerColumns extends Model
{
    protected  $table =  'agent_lecturer_columns';
    protected $dateFormat = 'U';
    protected $guarded = [];

    //关联讲师
    public function agent_lecturers(){
        return $this->belongsTo(AgentLecturers::class, 'lecturer_id' , 'id');
    }

    //关联专栏内容表
    public function agent_columns_content(){
        return $this->hasMany(AgentColumnsContent::class,'columns_id','id');
    }


//    //获取首页的专栏内容
    public static function getIndexColumns(){
        $data = [];
        $columns = self::with('agent_columns_content','agent_lecturers')
            ->with(['agent_columns_content'=>function($query){
                $query->where('status',1);
                $query->orderBy('created_at','desc');
            }])
            ->where('status',1)
            ->orderBy('sort','desc')
            ->orderBy('created_at','desc')
            ->skip(0)->take(3)
            ->get()->toArray();
        foreach ($columns as $oneColumn){
            $arr = [];
            $arr['column_id'] = trim($oneColumn['id']);
            $arr['photo'] = getImage($oneColumn['photo'], 'news', '');
            $arr['title'] = trim($oneColumn['title']);
            $arr['lecturer_name'] = trim($oneColumn['agent_lecturers']['name']);
            $arr['lecturer_appellation'] = trim($oneColumn['agent_lecturers']['appellation']);
            $arr['newest_at'] = '';
            $arr['newest_title'] = '';
            if(!empty($oneColumn['agent_columns_content'])){
                $newestContent = $oneColumn['agent_columns_content'][0];
                $arr['newest_id'] = trim($newestContent['post_id']);
                $arr['newest_at'] = $newestContent['updated_at'];
                if($newestContent['type'] == 1){
                    $newestContent = AgentLessons::find($newestContent['post_id']);
                    $arr['newest_title'] = trim($newestContent['subject']);
                    $arr['newest_type'] = 'video';
                }
                else{
                    $newestContent = AgentArticlesKnowledge::with('news')
                        ->where('id',$newestContent['post_id'])->first();
                    $arr['newest_title'] = trim($newestContent['news']['title']);
                    $arr['newest_type'] = 'article';
                }
            }
            $data[]= $arr;
        }
        return $data;
    }
}
