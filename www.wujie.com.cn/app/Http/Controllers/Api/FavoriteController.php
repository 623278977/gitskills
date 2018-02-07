<?php
/****收藏控制器********/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\CommonController;
use App\Models\User\Entity;
use App\Models\Activity\Entity as Activity;
use App\Models\User\Favorite;
use App\Models\Video;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends CommonController {
	/**
	 * 收藏
 	* @param Request $request
	 * @return string
 	*/
	public function postDeal(Request $request)
	{
		$uid=$request->input('uid');
		if (!Entity::checkAuth($uid))
			return  AjaxCallbackMessage('账号异常',false);
		$post_id = $request->input('post_id');
		$model = $request->input('model');
		$type=$request->input('type');
		if($type==''||empty($model)||empty($post_id)||empty($uid))
			return  AjaxCallbackMessage('参数有误',false);
		$first=$data=array(
			'uid'=>$uid,
			'model'=>$model,
			'post_id'=>$post_id,
		);
		if($type==0){
			//取消
			$favorite=Favorite::getRow($data);
			if(!isset($favorite->id))
				return  AjaxCallbackMessage('操作错误',false);
			$favorite->status=0;
			$favorite->save();
			if($model=="video"){
				$video=Video::where('id',$post_id)->first();
				if($video->favor_count>0) {
					$video->favor_count = $video->favor_count - 1;
					$video->save();
				}
			}

			if($model=='activity'){
				$activty=Activity::where('id',$post_id)->first();
				if($activty->likes>0){
					$activty->likes = $activty->likes-1;
					$activty->save();
				}
			}

			if($model=='opportunity'){
				$opportunity=Opportunity::where('id',$post_id)->first();
				if($opportunity->likes>0){
					$opportunity->likes = $opportunity->likes-1;
					$opportunity->save();
				}
			}

			return  AjaxCallbackMessage('取消成功',true);
		}elseif($type==1){
			//收藏
			$data['status']=1;
			if(Favorite::getCount($data))
				return  AjaxCallbackMessage('收藏成功',true);
			$favorite=Favorite::getRow($first);
			if(isset($favorite->id)){
				$favorite->status=1;
				$favorite->created_at=time();
				$favorite->save();
			}else{
				Favorite::create($data);
			}
			if($model=="video"){
				$video=Video::where('id',$post_id)->first();
				$video->favor_count=$video->favor_count+1;
				$video->save();
			}

			if($model=='activity'){
				$activty=Activity::where('id',$post_id)->first();
					$activty->likes = $activty->likes+1;
					$activty->save();
			}

			if($model=='opportunity'){
				$opportunity=Opportunity::where('id',$post_id)->first();
					$opportunity->likes = $opportunity->likes+1;
					$opportunity->save();
			}
			return  AjaxCallbackMessage('收藏成功',true);
		}
		return  AjaxCallbackMessage('参数有误',false);
	}

}