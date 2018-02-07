<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e) {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e) {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }
        if ($request->is('api/*') || $request->is('agent/*')) {
            if ($e instanceof NotFoundHttpException) {
                return response(AjaxCallbackMessage('请求接口不存在！', false), 404);
            }
            $key = md5($request->fullUrl());
            if (\App::environment() === 'production' && !(\Cache::has($key) ? \Cache::get($key) : false)) {
                //发送短信提醒
                //SendSMS(18058190409, '接口：'.$request->fullUrl().'异常！', 'interface_exception');
                \Cache::put($key, true, 30);
                if (!$e instanceof \Symfony\Component\Debug\Exception\FlattenException) {
                    $e = \Symfony\Component\Debug\Exception\FlattenException::create($e);
                }
                $exception = new \Symfony\Component\Debug\ExceptionHandler();
                $content = $exception->getContent($e);
                $css = $exception->getStylesheet($e);
                \Mail::queue('errors.api.mail', [
                    'url' => $request->fullUrl(),
                    'request' => $request->all(),
                    'method' => $request->getMethod(),
                    'header' => $request->header(),
                    'content' => $content,
                    'css' => $css
                        ], function ($message) {
                    $message->to('yaokai@tyrbl.com')
                            ->cc('zhangyl@tyrbl.com')
                            ->cc('tangjb@tyrbl.com')
                            ->cc('chenmj@tyrbl.com')
                            ->cc('shiqy@tyrbl.com')
                            ->subject('商圈接口异常');
                });
            }
            if (\App::environment() === 'production'){
                return response(AjaxCallbackMessage('服务器累了，稍后再试！', false));
            }
        }
        return parent::render($request, $e);
    }

}
