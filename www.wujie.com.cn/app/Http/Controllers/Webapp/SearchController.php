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
class SearchController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getDetail(Request $request)
    {
        $data = $request->input();
    
        return view('search.detail')->with('vip_id',$data['vip_id']);
    }
}
