<?php

namespace App\Http\Controllers\Citypartner;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, DB, Cache, Cookie, Crypt, Auth;
use App\Models\Partner\Message as Message;
class CommonController extends Controller
{
    protected $userinfo;
    protected $mid;

    public function __construct(Request $request)
    {
        config()->set('auth.model', \App\Models\CityPartner\Entity::class);
        config()->set('auth.table', 'city_partner');
        $this->beforeFilter(function(){
            return $this->isLoing();
        });
    }

    /**
     * 判断用户是否登陆
     * @return \Illuminate\Http\RedirectResponse
     */
    public function isLoing()
    {
        if (Auth::check()) {
            $this->userinfo = Auth::user() ?: null;
            $this->mid = Auth::id() ?: null;
        } else {
            return redirect('citypartner/public/index');
        }
        $count = Message::getCount($this->mid);
        //
        //视图Composer
        view()->composer('citypartner.layouts.layout', function ($view) use($count) {
            $view->with('partner', $this->userinfo)->with('partner_uid', $this->mid)->with('count',$count);
        });
    }
}
