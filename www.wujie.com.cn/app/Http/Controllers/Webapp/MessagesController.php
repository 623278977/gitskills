<?php
/**
 * 官方消息控制器
 * @author Administrator
 *
 */
namespace App\Http\Controllers\Webapp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use App\Models\Activity\Entity as Activity;
class MessagesController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getMessages(Request $request)
    {
        $data = $request->input();
        return view('message.messages')->with('uid',$request->input('uid',0));
    }
}
