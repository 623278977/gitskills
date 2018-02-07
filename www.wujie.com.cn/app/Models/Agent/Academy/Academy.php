<?php
namespace App\Models\Agent\Academy;

//use Illuminate\Database\Eloquent\Model;


class Academy
{
    /*
     * 获取新手学院
     * 每日推荐的内容
     * $return array
     * */
    public static function getDailyRecommendation(){

        //获取最多6条文章
        $articles = AgentArticlesKnowledge::with('news')->where('status',1)
            ->orderBy('index_sort','desc')->skip(0)->take(6)->get()->toArray();
        //获取最多6条视频
        $lessons = AgentLessons::where('status',1)
            ->orderBy('index_sort','desc')->skip(0)->take(6)->get()->toArray();

        //获取最多6条的话术随身听
        $talkSkill = AgentTalkingSkills::where('is_recommend',1)
            ->where('status',1)->orderBy('recommend_sort','desc')->select('id','audio_url','audio_len','subject','recommend_sort as index_sort','created_at')->skip(0)->take(6)->get()->toArray();

        //排序
        $contentArr = collect($articles)->merge($lessons)->merge($talkSkill)->toArray();
        $rule = [
            'index_sort'=>'desc',
            'created_at'=>'desc',
        ];
        $contents = multiFieldSort($contentArr , $rule);
        $contents = collect($contents)->slice(0, 6)->toArray();
        $data = [];
        foreach ($contents as $one){
            $arr = [];
            $arr['content_id'] = trim($one['id']);
            if(isset($one['news_id'])){
                $arr['type'] =  'article';
                $arr['title'] = trim($one['news']['title']);
            }
            else if(isset($one['audio_url'])){
                $arr['type'] =  'audio';
                $arr['title'] = trim($one['subject']);
                $arr['audio_len'] =  formatAudioLen($one['audio_len']);
            }
            else{
                $arr['type'] =  'video';
                $arr['title'] = trim($one['subject']);
            }
            $data[] = $arr;
        }
        return $data;
    }



}