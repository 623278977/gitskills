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
class BrandController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getDetail(Request $request)
    {
        $data = $request->input();
    
        return view('brand.detail')->with('id',$data['id'])->with('uid',$request->input('uid',0));
    }
    public function getNote(Request $request)
    {
        $data = $request->input();
    
        return view('brand.note')->with('id',$data['id'])->with('uid',$request->input('uid',0));
    }
    public function getJoin(Request $request)
    {
        $data = $request->input();
    
        return view('brand.join')->with('id',$data['id'])->with('uid',$request->input('uid',0));
    }
}
