<?php
/**
 * 商机控制器
 * @author Administrator
 *
 */
namespace App\Http\Controllers\Webapp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
class BusinessController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getGoverment(Request $request)
    {
    	/*id 直播id  uid  用户id**/
        return view('business.goverment',array("id"=>$request->input('id'),"uid"=>$request->input('uid',0)));
    }
}
