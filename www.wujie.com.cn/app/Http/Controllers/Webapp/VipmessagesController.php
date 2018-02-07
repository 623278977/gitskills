<?php
/**
 * Vip消息控制器
 * @author Administrator
 *
 */
namespace App\Http\Controllers\Webapp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use App\Models\Activity\Entity as Activity;
class VipmessagesController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getVipmessage(Request $request)
    {
        $data = $request->input();
        return view('vipmessage.vipmessages')->with('uid',$request->input('uid',0))->with('position_id',$request->input('position_id',0));
    }
}