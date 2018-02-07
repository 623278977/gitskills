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
class HeadlineController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getDetail(Request $request)
    {
        $data = $request->input();
        return view('headline.detail')->with('id',$request->input('id',0));
    }
 
}