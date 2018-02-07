<?php

namespace App\Http\Controllers\Citypartner;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
class ZoneController extends Controller
{
    public function postChildren()
    {
        if(!Request::has('id'))
        {
            return AjaxCallbackMessage('参数异常',false);
        }
        $id = Request::input('id');
        $zone = DB::table('zone')->where('upid',$id)->where('status',1)->get();
        return AjaxCallbackMessage($zone,true);
    }
}
