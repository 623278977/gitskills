<?php

namespace App\Models\Video;

use App\Models\Brand\Entity as Brand;
use Illuminate\Database\Eloquent\Model;
use App\Models\Brand\BrandVideo;
use Illuminate\Support\Str;

class Entity extends Model
{
    protected $table = 'video';

    public function belongsToBrand()
    {
        return $this->belongsTo(Brand::class,'brand_id','id');
    }

    /*
    * 作用:获取专版视频搜索
    * 参数:
    * 
    * 返回值:
    */
    public static function getVipVideo($vip_id, $keywords,$pageSize = 15)
    {
        $videos = self::where('vip_id', $vip_id)->where('status', 1)
            ->where('subject', 'like', '%'.$keywords.'%')->paginate($pageSize);

        //['id','subject', 'video_url', 'image', 'description']
        $data = [] ;
        foreach($videos as $key => $video){
            $data[$key]['image']            = getImage($video->image, 'video');
            $data[$key]['id']               = $video->id;
            $data[$key]['subject']          = $video->subject;
            $data[$key]['video_url']        = $video->video_url;
            $data[$key]['description']      = $video->description;
            $data[$key]['view']             = $video->view;
            $data[$key]['favorite_count']   = $video->favor_count;
            $data[$key]['type']             = self::getHumanVideoType($video->type);
        }
        return $data;
    }


    /**
     * 根据某个键自增
     */
    public static function incre(Array $incre, Array $field)
    {
        $result = self::where($field)->increment(array_keys($incre)[0], array_values($incre)[0]);

        return $result;
    }

    /*
    * 作用:获取视频类型
    * 参数:
    *
    * 返回值:
    */
    public static function getHumanVideoType($type)
    {
        return \DB::table('video_type')
            ->where('id',$type)
            ->first()->subject;
    }

    /**获取品牌的创业基金*/
    public static function brand_fund($brand_id)
    {
        $fund = Brand::where('id',$brand_id)->value('fund');
        return $fund;
    }

    /*
     *经纪人端根据 视频id获取视频及品牌有关详情
     *
     * */
    public static function getDetailById($id){
        $videoInfo=self::where('id',$id)->where('agent_status',1)->first();
        if(!is_object($videoInfo)){
            return array(
                'message' => '请输入有效的视频id',
                'error' => 1,
            );
        }

//        \DB::connection()->enableQueryLog();
        $videoDetail=self::leftJoin('brand', 'brand.id', '=', 'video.brand_id')
            ->leftJoin('video_analysis','video_analysis.video_id','=','video.id')
            ->leftJoin('categorys','categorys.id','=','brand.categorys1_id')
            ->select('video.subject','video.image','video.content','brand.name',
                'video.created_at','video.video_url',
                'categorys.name as cate_name','investment_min','investment_max',
                'brand_summary','brand.keywords','video_analysis.comment','brand.id',
                'brand.logo','video.description','brand.details'
            )
            ->where('video.id',$id)
            ->where('video.agent_status','1')
            ->first();
//        $str=\DB::getQueryLog();
        $data=[];
        if(is_object($videoDetail)){
            $result=$videoDetail;
            $keywordStr=trim($result['keywords']);
            $keywordArr=explode(" ",$keywordStr);
            $detail = trim($result['description']);
            if(empty($detail)){
                $detail = extractText(trim($result['content']),50);
            }
            $brandSummary = trim($result['brand_summary']);
            if(empty($brandSummary)){
                $brandSummary = extractText(trim($result['details']),50);
            }
            $data=array(
                'title'=>$result['subject'],
                'created_at'=> strtotime($result['created_at']),
                'list_img'=> getImage($result['image'],'video'),
                'video_url'=>$result['video_url'],
                'bg_image'=> getImage('','video'),
                'detail' => $detail,
                'brand_id'=>trim($result['id']),
                'brand_title'=>$result['name'],
                'brand_logo'=> getImage($result['logo']),
                'brand_category_name'=>$result['cate_name'],
                'brand_investment_min'=> floatval($result['investment_min']),
                'brand_investment_max'=> floatval($result['investment_max']),
                'brand_summary'=> $brandSummary,
                'brand_keywords'=>$keywordArr,
                'comments'=>$result['comment'],
                'share_img'=> getImage($result['image'],'video'),
            );
        }
        return $data;
    }
    /*
     *根据id获取品牌学习视频详情
     *
     * */
    public static function getStudyVideoDetailById($id){
        $brandVideo = BrandVideo::where('id',$id)->where('is_delete',0)->first();
        if(!is_object($brandVideo)){
            return ['message'=>'该视频不存在','error'=>1];
        }
        $data = [];
        $videoInfo = BrandVideo::with('brand.categorys1')->where('id',$id)->first();
        //视频描述
        $detail = trim($videoInfo['summary']);
        if(empty($detail)){
            $detail = extractText(trim($videoInfo['description']),50);
        }
        $brandSummary = extractText($videoInfo['brand']['summary'],50);
        if(empty($brandSummary)){
            $brandSummary = extractText(trim($videoInfo['brand']['details']),50);
        }
        $keywordArr = [];
        if(!empty($videoInfo['brand']['keywords'])){
            $keywordArr = explode(' ',$videoInfo['brand']['keywords']);
        }

        $data['title'] = trim($videoInfo['subject']);
        $data['list_img'] = getImage($videoInfo['image'],'video');
        $data['bg_image'] = getImage($videoInfo['image'],'video');
        $data['video_url'] = trim($videoInfo['video_url']);
        $data['video_description'] = extractText(empty($videoInfo['description']) ? $videoInfo['summary'] : $videoInfo['description'] , -1);
        $data['created_at'] = trim(strtotime($videoInfo['created_at']));
        $data['detail'] = $detail;
        $data['brand_title'] = trim($videoInfo['brand']['name']);
        $data['brand_logo'] = trim($videoInfo['brand']['logo']);
        $data['brand_id'] = trim($videoInfo['brand']['id']);
        $data['brand_slogan'] = trim($videoInfo['brand']['slogan']);
        $data['brand_category_name'] = trim($videoInfo['brand']['categorys1']['name']);
        $data['brand_investment_min'] = trim(floatval($videoInfo['brand']['investment_min']));
        $data['brand_investment_max'] = trim(floatval($videoInfo['brand']['investment_max']));
        $data['brand_summary'] = $brandSummary;
        $data['brand_keywords'] = $keywordArr;
        $data['lecturers_id'] = trim($videoInfo['lecturers_id']);
        return $data;
    }

    /**
     * 获取首页需要展示的热门视频数据信息
     */
    public static function gainIndexShowVideos()
    {
        $confirm_data = array();

        //根据指定的显示视频信息
        $video_result = self::where([
            'is_index_show' => 1,
            'agent_status'  => 1,
        ])
        ->orderBy('created_at', 'desc')
        ->orderBy('top','desc')
        ->limit(2)
        ->get();

        //默认显示两条
        $default_result = self::where('status', 1)
            ->orderBy('created_at', 'desc')
            ->orderBy('top','desc')
            ->limit(2)
            ->get();

        //对结果进行判断
        if ($video_result) {
            $data = $video_result;
        } else {
            $data = $default_result;
        }

        //对结果进行处理，两种情况：
        //1、如果后台设置了指定视频的显示，就根据设置的显示视频信息
        //2、如果后台没有设置指定的视频显示，就默认获取两条显示，按最新时间倒序排序

        //指定后台设置显示的视频
        if ($data) {
            foreach ($data as $key => $vls) {
                $confirm_data[$key] = [
                    'id'           => $vls->id,
                    'activity_id'  => $vls->activity_id,
                    'duration'     => $vls->duration,
                    'video_url'    => $vls->video_url,
                    'list_img'     => getImage($vls->image),
                    'bg_image'     => getImage($vls->bg_image),
                    'summary'      => $vls->description ?  Str::limit(str_replace(["\t", "\r", "\n",  "&nbsp"], '', strip_tags($vls->description)), 28) : Str::limit(str_replace(["\t", "\r", "\n", "&nbsp"], '', strip_tags($vls->content)), 28),
                    'is_hot'       => $vls->is_hot,
                    'is_recommend' => $vls->is_recommend,
                ];
            }
        }

        //返回结果
        return $confirm_data;
    }

}
