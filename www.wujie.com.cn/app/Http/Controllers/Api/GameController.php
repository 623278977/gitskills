<?php
/**
 * 抽奖
 */

namespace App\Http\Controllers\Api;

use App\Models\Activity\Intention;
use App\Models\GameWinners;
use App\Http\Libs\Helper_Huanxin;
use App\Models\GameGoods;
use App\Models\Game;
use App\Models\GamePrize;
use App\Models\User\Entity as User;
use App\Models\Activity\Sign;
use App\Models\Activity\Entity as Activity;
use App\Models\ActivityVideoComment;
use App\Models\Praise;
use Illuminate\Http\Request;

class GameController extends \App\Http\Controllers\CommonController
{
    /**
     * 活动/年会抽奖  --数据中心版
     * @User yaokai
     * @param Request $request
     * @return string
     */
    public function postChoujiang(Request $request)
    {
        $activity_id = intval($request->input('activity_id')) ?: 0;
        if (empty($activity_id)) return AjaxCallbackMessage('未选中活动', false);
        $prize_id = intval($request->input('prize_id')) ?: 0;
        if (empty($prize_id)) return AjaxCallbackMessage('奖项数据有误', false);
        $good_id = intval($request->input('good_id')) ?: 0;
        if (empty($good_id)) return AjaxCallbackMessage('奖品数据有误', false);
        $userData = array();
        //$selected=intval($request->input('selected'))?:0;
        $apply_ids = Sign::where('activity_id', $activity_id)->where('status', 1)->where('is_win', 0)->count();
        $apply_id = intval($request->input('apply_id')) ?: 0;
        if (count($apply_ids)) {
            $apply = Sign::where('id', $apply_id)->first();
            $activity_id = $apply->activity_id;
            $tel = $apply->tel;
            $activity = Activity::where('id', $activity_id)->first();
            //找出这个活动相同标签的活动
            $tag_id = $activity->tag_id;
            $activitys = Activity::where('tag_id', $tag_id)->get()->toArray();
            $activity_ids = array_pluck($activitys, 'id');//这个标签所有的关联活动id
            $sign = Sign::whereIn('activity_id', $activity_ids)
                ->where('tel', $tel)
                ->get();
            foreach ($sign as $apply) {
                $apply->is_win = 1;
                $apply->save();
            }

            //奖项剩余-1
            $gamePrize = GamePrize::where('id', $prize_id)->first();
            $gamePrize->left -= 1;
            if ($gamePrize->left < 0) {
                return AjaxCallbackMessage($userData, true, "此奖项名额已抽完");
            }
            $gamePrize->save();
            $user = User::where('uid', $apply->uid)->first();
            $userArray['avatar'] = isset($user->uid) ? getImage($user->avatar, 'choujiang') : getImage('', 'choujiang');
            $userArray['realname'] = cut_str($apply->name, 3) ?: (isset($user->uid) ? (cut_str($user->realname, 3) ?: cut_str($user->nickname, 3)) : cut_str($apply->tel, 3));
            $userArray['tel'] = isset($user->uid) ? dealTel($user->username) : dealTel($apply->tel);
            //这里需要取新的值  手机号md5
            $userArray['realtel'] = isset($user->uid) ? $user->non_reversible : $apply->non_reversible;
            $userArray['uid'] = isset($user->uid) ? $user->uid : 0;//$userArray['realtel']
            $good = GameGoods::where('id', $good_id)->first();
            $content_sms = trans('sms.yearPrize', array(
                'name' => $userArray['realname'],
                'tel' => $userArray['realtel'],
                'prize' => isset($good->id) ? $good->name : ''
            ));
            //关闭短信通知
//            @SendTemplateSMS('yearPrize', $userArray['realtel'], 'choujiang', [
//                'name' => $userArray['realname'],
//                'tel' => $userArray['realtel'],
//                'prize' => isset($good->id) ? $good->name : ''
//            ]);
            //环信通知  弃用
//            if ($userArray['uid']) {
//                @Helper_Huanxin::sendMessage(array($userArray['uid']), $content_sms);
//            }
            $userData[] = $userArray;
            $gameWinners['prize_id'] = $prize_id;
            $gameWinners['address'] = '';
            $gameWinners['tel'] = isset($user->uid) ? $user->username : $apply->tel;
            $gameWinners['name'] = isset($user->uid) ? ($user->realname ?: $user->nickname) : $apply->name;
            $gameWinners['uid'] = isset($user->uid) ? $user->uid : 0;
            $gameWinners['non_reversible'] = isset($user->uid) ? $user->non_reversible : $apply->non_reversible;
            GameWinners::create($gameWinners);
        }
        if (!count($userData)) {
            return AjaxCallbackMessage("此奖项已抽完", false);
        }
        return AjaxCallbackMessage($userData, true);
    }

    /**
     * 没人抽中 没领奖 删除资格
     * @param Request $request
     */
    public function postDelete(Request $request)
    {
        $activity_id = intval($request->input('activity_id')) ?: 0;
        $activity = Activity::where('id', $activity_id)->first();
        //找出这个活动相同标签的活动
        $tag_id = $activity->tag_id;
        $activitys = Activity::where('tag_id',$tag_id)->get()->toArray();
        $activity_ids = array_pluck($activitys,'id');//这个标签所有的关联活动id
        if (empty($activity_id))
            return AjaxCallbackMessage('未选中活动', false);
        $uid = intval($request->input('uid')) ?: 0;
        $tel = $request->input('tel') ?: 0;
        $prize_id = intval($request->input('prize_id')) ?: 0;
        if (empty($prize_id))
            return AjaxCallbackMessage('奖项数据有误', false);
        $good_id = intval($request->input('good_id')) ?: 0;
        if (empty($good_id))
            return AjaxCallbackMessage('奖品数据有误', false);
        //获奖名单里面踢出
        if ($uid) {
            Sign::whereIn('activity_id', $activity_ids)->where('uid', $uid)->update(array('is_win' => -1));
            GameWinners::where('prize_id', $prize_id)->where('uid', $uid)->delete();
        } else {
            Sign::whereIn('activity_id', $activity_ids)->where('tel', $tel)->update(array('is_win' => -1));
            GameWinners::where('prize_id', $prize_id)->where('tel', $tel)->delete();
        }

        //奖品剩余+1
        $gamePrize = GamePrize::where('id', $prize_id)->first();
        $gamePrize->left += 1;
        $gamePrize->save();

        return AjaxCallbackMessage('成功删除用户', true);
    }

    /**
     *
     * @param Request $request
     */
    public function postNextstep(Request $request)
    {
        $prize_id = intval($request->input('prize_id')) ?: 0;
        $next_prize_id = intval($request->input('next_prize_id')) ?: 0;
        if (empty($prize_id)) return AjaxCallbackMessage('奖项数据有误', false);
        $gamePrize = GamePrize::where('id', $prize_id)->first();
        $nextGmaePrize = GamePrize::where('id', $next_prize_id)->first();
        $return = [
            'left' => isset($gamePrize->left) ? $gamePrize->left : 0,
            'image' => $nextGmaePrize ? $nextGmaePrize->image : ''
        ];
        return $return;
    }

    /**
     * 获取未中奖的用户  --数据中心版  TODO 好像手机号只是展示   暂未处理 2017.12.13
     * @User yaokai
     * @param Request $request
     * @return string
     */
    public function postGetusers(Request $request)
    {
        $activity_id = $request->input('activity_id');
        $activity = Activity::where('id', $activity_id)->first();
        //找出这个活动相同标签的活动
        $tag_id = $activity->tag_id;
        $activitys = Activity::where('tag_id',$tag_id)->get()->toArray();
        $activity_ids = array_pluck($activitys,'id');//这个标签所有的关联活动id
        $users = Sign::with('user')
//            ->where('activity_id', $activity_id)
            ->whereIn('activity_id', $activity_ids)
            ->where('is_win', 0)
            ->where('status', 1)
            ->groupBy('tel','uid')
            ->orderBy('id', 'desc')
            ->get();
        $userData = array();
        if (count($users)) {
            foreach ($users as $k => $v) {
                $userData[$k]['apply_id'] = $v->id;
                $userData[$k]['uid'] = $v->uid ? $v->uid : 0;
                $userData[$k]['avatar'] = $v->uid ? getImage($v->user->avatar, 'choujiang', 'large') : getImage('', 'choujiang', 'large');
                $userData[$k]['realname'] = $v->name ?: ($v->uid ? ($v->user->realname ?: $v->user->nickname) : $v->tel);
                $userData[$k]['tel'] = $v->uid ? dealTel($v->user->username) : dealTel($v->tel);
            }
        }
        return AjaxCallbackMessage($userData, true);
    }

    /**
     * 某一奖项未中奖的人数
     * @param Request $request
     */
    public function postNowinnumberbyoneprize(Request $request)
    {
        $prize_id = intval($request->input('prize_id')) ?: 0;
        if (empty($prize_id)) return AjaxCallbackMessage('获取未中奖参数有误', false);
        $gamePrize = GamePrize::where('id', $prize_id)->first();
        return AjaxCallbackMessage($gamePrize->left, true);
    }

    /**
     * 某一奖项中奖的人
     * @param Request $request
     */
    public function postWinning(Request $request)
    {
        $prize_id = intval($request->input('prize_id')) ?: 0;
        $activity_id = $request->input('activity_id');
        $activity = Activity::where('id', $activity_id)->first();
        //找出这个活动相同标签的活动
        $tag_id = $activity->tag_id;
        $activitys = Activity::where('tag_id',$tag_id)->get()->toArray();
        $activity_ids = array_pluck($activitys,'id');//这个标签所有的关联活动id
        if (empty($prize_id))
            return AjaxCallbackMessage('获取未中奖参数有误', false);
        $gamePrize = GamePrize::where('id', $prize_id)->first();
        if ($gamePrize->left <= 0) {
            return AjaxCallbackMessage('奖品已经抽完！', false);
        }
        $sign = Sign::whereIn('activity_id', $activity_ids)
//                        ->whereIn('maker_id',function($query){
//                            $query->from('maker')->where('zone_id','175')->select('id');
//                        })
            ->where('status', '=', '1')
            ->groupBy('is_win', 'maker_id')
            ->get([\DB::raw('count(1) as num,maker_id,is_win')]);
        if (!$sign->count()) {
            return AjaxCallbackMessage('签到人员均已中奖！', false);
        }
        $arr = [];
        foreach ($sign as $item) {
            $arr[$item->maker_id][$item->is_win] = $item->num;
        }
        $winning_maker_id = null;
        foreach ($arr as $maker_id => $item) {
            if (empty($item[0])) {//无未中奖人员
                continue;
            }
            if (empty($winning_maker_id) || empty($item[1]) || isset($arr[$winning_maker_id][1]) && $arr[$winning_maker_id][1] > $item[1]) {
                $winning_maker_id = $maker_id;
            }
            if (empty($item[1])) {//无中奖人员
                break;
            }
        }
        //取出这个奖品的意向客户
        $intentions = Intention::select('tel')
            ->where('goods_id', $gamePrize->goods_id)
            ->whereIn('activity_id', $activity_ids)
            ->get()
            ->toArray();
        $intentions = array_flatten($intentions);
        $winning = Sign::whereIn('activity_id', $activity_ids)
//            ->where('maker_id', '=', $winning_maker_id)
            ->where('status', '=', '1')
            ->where('is_win', '=', '0')
            ->groupBy('tel','uid')
//            ->get()->toArray();
            ->whereIn('tel', $intentions)
            ->orderBy(\DB::Raw('rand()'), 'desc')
            ->pluck('id');
        if (empty($winning)) {//如果没有开奖数据则扩大范围
            $winning = Sign::whereIn('activity_id', $activity_ids)
//                ->where('maker_id', '=', $winning_maker_id)
                ->where('status', '=', '1')
                ->where('is_win', '=', '0')
                ->groupBy('tel','uid')
                ->orderBy(\DB::Raw('rand()'), 'desc')
//                ->get()->toArray();
                ->pluck('id');
        }
        return AjaxCallbackMessage($winning, true);
    }

    /**
     * 奖项列表
     * @param Request $request
     */
    public function postPrizes(Request $request)
    {
        $id = intval($request->input('id')) ?: 0;
        $game = Game::where('game.id', $id)
            ->select('game.id', 'game.title', 'game.image')
            ->first();
        if (!$game) {
            return AjaxCallbackMessage('游戏不存在！', false);
        }
        $game->status = 0;
        $game->save();
        $prizes = GamePrize::where('game_id', $game->id)->orderBy('type', 'desc')->get();
        $data = array();
        $currentType = false;
        if (count($prizes)) {
            foreach ($prizes as $k => $v) {
                $data[$k]['name'] = $v->title;
                $data[$k]['num'] = $v->num;
                $data[$k]['type'] = $v->type;
                $data[$k]['prize_id'] = $v->id;
                $data[$k]['good_id'] = $v->goods_id;
                $data[$k]['image'] = $v->image;
                if ($currentType === false && $v->left > 0) {
                    $currentType = $v->type;
                }
            }
        }
        return AjaxCallbackMessage(compact('prizes', 'currentType'), true);
    }
}