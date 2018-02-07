<?php
namespace App\Http\Controllers\Api;

use DB, Input;
use Illuminate\Http\Request;

class ScoreController extends CommonController
{

    /**
     * 积分商品
     */
    public function postGoods(Request $request, $version = null)
    {
        $versionService = $this->init(__METHOD__, $version);

        if ($versionService) {
            $response = $versionService->bootstrap($request->all());

            return AjaxCallbackMessage($response['message'], $response['status']);
        }

        return AjaxCallbackMessage('api接口不再维护', false);
    }



    /**
     * 积分商品
     */
    public function postDiff(Request $request, $version = null)
    {
        $user_scores = \DB::table('user')->select('uid', 'score')->get();
        $user_scores = array_pluck($user_scores, 'score', 'uid');

        //收入
        $incomes = \DB::table('score_log')->where('operation', 1)->select(\DB::raw('uid, sum(num) as score'))->groupBy('uid')->get();
        $incomes = array_pluck($incomes, 'score', 'uid');


        $pays = \DB::table('score_log')->where('operation', -1)->select(\DB::raw('uid, sum(num) as score'))->groupBy('uid')->get();
        $pays = array_pluck($pays, 'score', 'uid');


        $less = $equl = $more = [];
        foreach($user_scores as $k=>$v){
            if(isset($incomes[$k])){
                $income = $incomes[$k];
            }else{
                $income = 0;
            }

            if(isset($pays[$k])){
                $pay = $pays[$k];
            }else{
                $pay = 0;
            }

            $diff = $v -($income-$pay);

            if($diff >0){ //账上积分大于日志积分， 需要在日志表插入一条积分
                $more[$k] = $diff;
            }elseif($diff==0){//两者相等
                $equl[$k] = $diff;
            }else{//账上积分小于日志积分， 需要在账上添加积分
                $less[$k] = $diff;
            }

        }


        dd($less, $equl, $more);
    }





}