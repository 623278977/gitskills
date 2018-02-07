<?php

/*
 * 专题
 */

namespace App\Http\Controllers\Api;

use App\Models\Special;
use Illuminate\Http\Request;
use App\Models\News\Entity as News;
use App\Models\Guest\Entity as Guest;
use App\Models\Video\Entity as Video;
use App\Models\Brand\Entity as Brand;

class SpecialController extends CommonController {
    //列表
    public function postList(Request $request) {
        $lists = Special::whereIn('type', ['brand', 'guest'])
                ->where('status', 'enable')
                ->paginate(max($request->get('pageSize', 10), 10), ['title', 'synopsis', 'type', 'id'])
                ->items();
        return AjaxCallbackMessage($lists, true);
    }

    //详情
    public function postDetail(Request $request,Special $special=null,$version) {
        $id = $request->input('id', '');
        if (!($special = Special::whereIn('type', ['brand', 'guest'])->where('status', 'enable')->find($id, ['id', 'title', 'synopsis', 'type', 'image']))) {
            return AjaxCallbackMessage('非法的id', false);
        }
        $bindIds = [];
        foreach (\DB::table('special_bind')
                ->where('special_id', $special->id)
                ->get(['type', 'bind_id']) as $item) {
            $bindIds[$item->type][] = $item->bind_id;
        }
        //取视频
        $videos = empty($bindIds['video']) ? [] : Video::where('status', 1)
                        ->whereIn('id', $bindIds['video'])
                        ->orderBy('sort', 'desc')
                        ->get()
                        ->all();
        //取资讯
        $news = empty($bindIds['news']) ? [] : News::orderBy('sort', 'desc')
                        ->whereIn('id', $bindIds['news'])
                        ->where('status', 'show')
                        ->get()
                        ->all();
        $data = $special->toArray();
        $data['image'] = getImage($data['image'], 'activity', '', 0);
        if ($special->type === 'brand') {//品牌相关
            $data['brands'] = empty($bindIds['brand']) ? [] : array_map(function($item) {
                        $item['logo'] = getImage($item['logo'], 'activity', '', 0);
                        $item['keywords'] = array_filter(explode(' ', $item['keywords']));
                        $item['detail'] = cut_str(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $item['details'])), 100);
                        $item['investment_arrange'] = $item['investment_min'] . '万-' . $item['investment_max'] . '万';
                        if (isset($item['categorys1']['name'])) {
                            $item['category_name'] = $item['categorys1']['name'];
                        }
                        return array_only($item, ['id', 'logo', 'name', 'slogan', 'detail', 'keywords', 'investment_arrange', 'category_name', 'is_recommend','brand_summary']);
                    }, Brand::with('categorys1')->find($bindIds['brand'])->toArray());
        } else {//嘉宾相关
            $data['guests'] = empty($bindIds['guest']) ? [] : array_map(function($item) {
                        $item['image'] = getImage($item['image'], 'activity', '', 0);
                        $item['details'] = cut_str(trim(preg_replace('#<\w+(\s+.*?)?>|</\w+>|&nbsp;#', '', $item['detail'])), 100);
                        return array_only($item, ['id', 'image', 'name', 'brief']);
                    }, Guest::find($bindIds['guest'])->toArray());
        }
        $data['videos'] = array_map(function($item) {
            $item = $item->toArray();
            $item['image'] = getImage($item['image'], 'video', '', 0);
            $item['record_at'] = explode(' ', $item['created_at'])[0];
            $item['keywords'] = array_filter(explode(' ', $item['keywords']));
            $item['length'] = $item['duration'] ? changeTimeType($item['duration']) : 0;
            return array_only($item, ['id', 'image', 'subject', 'duration', 'record_at', 'price', 'keywords', 'length']);
        }, $videos);
        $data['news'] = array_map(function($item) {
            $item = $item->toArray();
            $item['logo'] = getImage($item['logo'], 'news', '', 0);
            $item['created_at_format'] = date('Y-m-d', $item['created_at']);
            $item['keywords'] = array_filter(explode(' ', $item['keywords']));
            return array_only($item, ['id', 'logo', 'title', 'author', 'created_at_format', 'type', 'keywords','view','summary','detail']);
        }, $news);

        //v020700版本对接口做相关处理
        $versionService = $this->init(__METHOD__, $version);
        if($versionService){
            $response = $versionService->bootstrap(['result' => $data]);
            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage($data, true);
    }

}
