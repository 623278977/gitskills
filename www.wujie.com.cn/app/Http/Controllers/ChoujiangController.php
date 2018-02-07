<?php

namespace App\Http\Controllers;

use App\Models\ActivityApply;
use App\Models\GamePrize;
use App\Models\Game;
use Illuminate\Support\Facades\Cookie;
use App\Models\Category;
use App\Models\UserInfo;
use App\models\RoleUser;
use App\Models\LogOperation;
use App\models\User;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \DB;
use \Cache;
use Auth;

class ChoujiangController extends Controller {
    /**
     * 年会抽奖
     * @return Response
     */
    public function getIndex(Request $request) {
        /*         * *奖项** */
        $activity_id = intval($request->input('activity_id'))? : 0;
        $id = intval($request->input('id'))? : 0;
//        $code = $request->input('code')? : "chou";
        $game = Game::where('activity_id', $activity_id)
//                ->leftJoin('game_type', 'game_type.id', '=', 'game.type_id')
//                ->where('game_type.id', $code)
                ->where('game.id', $id)
                ->select('game.id','game.title','game.image')
                ->first();
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
        return View('game.choujiang')
                ->with('data', $data)
                ->with('activity_id', $activity_id)
                ->with('title', $game->title)
                ->with('currentType', $currentType)
                ->with('background' , $game->image);
    }

}
