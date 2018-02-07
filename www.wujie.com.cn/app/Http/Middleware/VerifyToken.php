<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;
use Illuminate\Contracts\Auth\Guard;
class VerifyToken
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }


    protected $except = [
        'api/test/test',
    ];



    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $imei = $request->header('imei');
        $time = $request->header('time');
        $salt = $request->header('salt');
        $username = '';
        if($request->has('username')){
            $username = $request->input('username');
        }

        if($request->has('uid')){
            $username = $request->input('uid');
        }

        if($request->has('agent_id')){
            $username = $request->input('agent_id');
        }

        $flag = false;
        foreach($this->except as $k=>$v){
            if($request->is($v)){
                $flag = true;
            }
        }

        //线上才做这个token验证
        if(md5($imei.$username.$time)!=$salt && !$flag && app()->environment() === 'production' ){
            exit;
        }

        return $next($request);
    }
}
