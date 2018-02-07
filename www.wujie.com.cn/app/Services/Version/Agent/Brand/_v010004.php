<?php
/**
 * Created by PhpStorm.
 * Title：
 * User: yaokai
 * Date: 2017/11/6 0006
 * Time: 17:17
 */
namespace App\Services\Version\Agent\Brand;


use App\Services\Version\VersionSelect;
use DB, Input;
use App\Models\Agent\BrandChapter;
use App\Models\Brand\Entity as Brand;
use App\Models\Agent\BrandCourse;

class _v010004 extends _v010003
{
    /*
     * 品牌章节列表
     * shiqy
     * */

    public function postChapterList($input){
        $validator = \Validator::make($input,[
            'agent_id' => 'required|exists:agent,id',
            'brand_id' => 'required|exists:brand,id',
        ]);
        if($validator->fails()){
            $warnings = $validator->messages();
            $show_warning = $warnings->first();
            return ['message'=>$show_warning ,'status'=>false];
        }
        $videoId = empty($input['video_id'])  ? 0 : intval($input['video_id']) ;
        $data = Brand::getBrandChapterList($input['brand_id'],$input['agent_id'] , $videoId);
        if(isset($data['error'])){
            return ['message'=>$data['message'] ,'status'=>false];
        }
        return ['message'=>$data ,'status'=>true];
    }

}