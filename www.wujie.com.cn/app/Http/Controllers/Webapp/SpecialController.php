<?php
/**
 * 活动控制器
 * @author Administrator
 *
 */
namespace App\Http\Controllers\Webapp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use App\Models\Activity\Entity as Activity;
class SpecialController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getDetail(Request $request)
    {
        $data = $request->input();

        return view('special.detail')->with('vip_id',$data['vip_id'])->with('uid',$request->input('uid',0))->with('position_id',$request->input('position_id',0));
         dd($request->input('position_id'));
    }
    
}
