<?php
/**
 * Created by PhpStorm.
 * User: jizx
 * Date: 2016/5/17
 * Time: 14:42
 * des:OVO控制器
 */
namespace App\Http\Controllers\Webapp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
class OvoController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getDescription(Request $request)
    {
        $data = $request->input();
        return view('ovo.description')->with('id',$data['id']);  //ovo中心详细介绍
    }

}