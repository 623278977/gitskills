<?php
/**
 * 邀请注册控制器
 * @author Administrator
 *
 */
namespace App\Http\Controllers\Webapp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use App\Models\Activity\Entity as Activity;
class InviteController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getDetail(Request $request)
    {
        $data = $request->input();
        return view('invite.detail')->with('uid',$request->input('uid',0))->with('code',$request->input('code',1));
    }


        /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getDetailpc(Request $request)
    {
        $data = $request->input();
        return view('invite.detailpc')->with('uid',$request->input('uid',0))->with('code',$request->input('code',1));
    }
   
}
