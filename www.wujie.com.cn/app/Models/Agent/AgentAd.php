<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class AgentAd extends Model
{
    protected $table='agent_ad';
    public $timestamps = false;

    public static $_TYPE=[
        1 => 'agent_index_banner',
        2 => 'agent_talking_skill',
        3 => 'agent_article',
        4 => 'agent_lesson',
        5 => 'agent_knowledge',
        6 => 'agent_we_chat',
        7 => 'agent_poster',
        8 => 'agent_after_welcome',
    ];
    static function getRows($where, $limit = null)
    {
        return self::where($where)
            ->where('start_time', '<', time())
            ->where('expired_time', '>', time())
            ->where('status', 1)
            ->limit($limit)
            ->get();
    }

    /**
     * @param $where
     * @return mixed
     * 返回的是数组
     */
    static function getAds($type)
    {
        /***对单个的type的广告缓存起来***/
        $ads = self::getRows(array('type' => $type), 5);
        if (count($ads)) {
            foreach ($ads as $k => $v) {
                $data[$k] = self::getBase($v);
            }
            $ads = $data;
        }

        return $ads;
    }

    static function getBase($ad ,$version = null)
    {
        $is_gif = 0;
        if (!isset($ad->id)) {
            return array();
        }
        $data = array();
        $data['title'] = $ad->title;
        $data['type'] = $ad->type;
        $data['link_url'] = $ad->link_url;
        $data['app_url'] = $ad->app_url;
        $data['stay_at'] = $ad->stay_at;
        $data['image_x'] = $ad->img_other?getImage($ad->img_other,'',''):'';
        //对gif图片进行处理,不做压缩处理
        if(get_extension($ad->image) == 'gif' && $version){
            $is_gif = 1;
        }

        if ($ad->type == 'after_welcome') {
            //先用点分割得到两部分
            $dot_cut = explode('.', $ad->image);
            //再用斜杠打散成数组
            $slash_cut= explode('/', $dot_cut[0]);
            $big_cut = $slash_cut;
            $slash_cut[4].='_480X800';
            $big_cut[4].='_720X1280';
            $small = implode('/', $slash_cut).'.'.$dot_cut[1];
            $big = implode('/', $big_cut).'.'.$dot_cut[1];
            $data['image']['small'] = $is_gif ? getImage($ad->image, 'ad', '') : getImage($small, 'ad', '');
            $data['image']['big'] = $is_gif ? getImage($ad->image, 'ad', '') : getImage($big, 'ad', '');
        } else {
            $data['image'] = $is_gif ? getImage($ad->image, 'ad', '') : getImage($ad->image, '', '');

        }

        return $data;
    }

    static function getPublicData($ad){
        if(!isset($ad->id)){
            return array();
        }
        $data = self::find($ad->id);
        $return = [];
        $return['id'] = $data->id;
        $return['title'] = $data->title;
        $return['type'] = $data->type;
        $return['link_url'] = $data->link_url;
        $return['app_url'] = $data->app_url;
        $return['image'] = url($data->image);
        $return['image'] = getImage($data->image, 'ad', 'large', 0);
        $return['intro'] = $data->intro;
        $return['click_count'] = $data->click_count;
        return $return;
    }
}
