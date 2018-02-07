<?php

namespace App\Services\Version\Video;

use App\Models\Special;
use Illuminate\Support\Facades\DB;
use App\Models\Guest\Entity as Guest;
use App\Models\Video\Entity as Video;

class _v020600 extends _v020500 {
    /**
     * 课程列表
     */
    public function postCurriculum($data) {
        $lists = Special::whereIn('type', ['course'])
                ->where('status', 'enable')
                ->select('id', DB::raw('"course" as type'), 'created_at');
        $video = Video::where('type', 2)
                ->where('status', 1)
                ->select('id', DB::raw('"video" as type') , 'created_at');
        $result = DB::table(DB::raw('(' . $lists->union($video)->toSql() . ') as t'))
                ->mergeBindings($lists->getQuery())
                ->orderBy('created_at','desc')
                ->paginate(max(array_get($data, 'request.pageSize', 10), 10))
                ->items();
        $list = [];
        foreach ($result as $key => $item) {
            $list[$key] = (array) $item;
            if ($item->type == 'video') {//视频
                $list[$key] +=$this->video(Video::find($item->id));
            } else {//课程数据
                $bindIds = [];
                foreach (\DB::table('special_bind')
                        ->where('special_id', $item->id)
                        ->get(['type', 'bind_id']) as $bind) {
                    $bindIds[$bind->type][] = $bind->bind_id;
                }
                //取视频
                $videos = empty($bindIds['video']) ? [] : Video::where('status', 1)
                                ->whereIn('id', $bindIds['video'])
                                ->orderBy('sort', 'desc')
                                ->get()
                                ->all();
                //取嘉宾
                $guests = empty($bindIds['guest']) ? [] : Guest::orderBy('id', 'desc')
                                ->whereIn('id', $bindIds['guest'])
                                ->get()
                                ->all();
                $special = Special::find($item->id);
                $list[$key]['title'] = $special->title;
                $list[$key]['guests'] = array_map([$this, 'guest'], $guests);
                $list[$key]['videos'] = array_map([$this, 'video'], $videos);
            }
        }
        return ['message' => $list, 'status' => true];
    }

    //获取视频数据
    protected function video(Video $video) {
        $data = [];
        $data['image'] = getImage($video['image'], 'video', '', 0);
        $data['record_at'] = explode(' ', $video['created_at'])[0];
        $data['keywords'] = array_filter(explode(' ', $video['keywords']));
        $data['subject'] = $video['subject'];
        $data['length'] = $video->duration?changeTimeType($video->duration):0;
        $data['id'] = $video['id'];
        return $data;
    }

    //获取视频数据
    protected function guest(Guest $guest) {
        $guest->image = getImage($guest['image'], 'activity', '', 0);
        return array_only($guest->toArray(), ['id', 'image', 'name', 'brief']);
    }

}
