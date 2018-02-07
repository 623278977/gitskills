<?php

/*
 * HTTP请求日志
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Foundation\Application;

class HttpLogs {

    private $log = [];
    private $table;
    private $connection;

    /**
     * 初始化处理
     */
    public function __construct(Request $request, Application $app) {
        $url = $request->url();
        \Log::info($url);
        $data = $request->input();
        \Log::info(json_encode($data));
        return;
        if ($request->is('citypartner/*', 'api/script/*')) {//城市合伙人页面不记录
            $this->log = false;
            return;
        }
        $name = DB::getDefaultConnection();
        $config = config('database.connections.' . $name);
        $config['database'] = 'wjsq_logs';
        config()->set('database.connections.wjsq_logs', $config);
        $this->connection = 'wjsq_logs';

        //Changed by zyl for get logs begin
//        $this->table = 'http_log_' . date('Y_m');
        if (strtolower($request->getMethod()) == "get") {
            $this->table = 'http_get_log_' . date('Y_m');
        } else {
            $this->table = 'http_log_' . date('Y_m');
        }
        //Changed by zyl for get logs end

        $this->createTable();
        $this->request = $request;
        DB::connection($name)->enableQueryLog();
        register_shutdown_function(function() use($request) {
            if (count(ob_get_status(true))) {
                $text = ob_get_clean();
                $response = response($text);
                $response->send();
                $this->terminate($request, $response);
            }
        });
        $app->instance(__CLASS__, $this);
    }

    /**
     * 响应开始处理
     */
    public function handle(Request $request, Closure $next) {
        if (is_array($this->log)) {
            ob_start();
            $platform = 'other';
            $agent = $request->header('USER_AGENT');
            if (strpos($agent, 'MicroMessenger') !== false) {//微信内置
                $platform = 'weixin';
            } elseif (strpos($agent, 'android') !== false) {//安卓
                $platform = 'android';
            } elseif (strpos($agent, 'iPhone') !== false) {//iPhone
                $platform = 'iPhone';
            } elseif (strpos($agent, 'iPod') !== false) {//iPod
                $platform = 'iPod';
            } elseif (preg_match('/mozilla|m3gate|winwap|openwave|Windows NT|Windows 3.1|95|Blackcomb|98|ME|XWindow|ubuntu|Longhorn|AIX|Linux|AmigaOS|BEOS|HP-UX|OpenBSD|FreeBSD|NetBSD|OS\/2|OSF1|SUN/i', $agent)) {
                $platform = 'pc';
            }
            $this->log = [
                'url' => $request->fullUrl(),
                'method' => strtolower($request->getMethod()),
                'get_parame' => var_export($_GET, true),
                'post_parame' => file_get_contents('php://input'),
                'file_parame' => var_export($_FILES, true),
                'request_at' => $request->header('REQUEST_TIME', time()),
                'meid' => array_get($_SERVER,'HTTP_UUID', ''),
                'ip' => $request->ip(),
                'platform' => $platform,
                'request_header' => var_export($_SERVER, true),
                'initial_session' => var_export(session()->all(), true),
            ];
        }
        return $next($request);
    }

    /**
     * 响应结束处理
     */
    public function terminate(Request $request, Response $response) {
        if (!$this->log) {
            return;
        }
        $this->log['respond_at'] = time();
        $this->log['respond'] = $response->getContent();
        $this->log['respond_status'] = $response->getStatusCode();
        $this->log['respond_header'] = (string) $response->headers;
        $this->log['finish_session'] = var_export(session()->all(), true);
        $this->log['sql_query'] = var_export(DB::getQueryLog(), true);
        DB::connection($this->connection)
                ->table($this->table)
                ->insert($this->log);
    }

    /*
     * 创建表 -- api调试表
     */
    protected function createTable() {
        if ($r = DB::connection($this->connection)->selectOne('show tables like "%' . $this->table . '"')) {
            return;
        }
        Schema::connection($this->connection)->create($this->table, function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->string('url', 500)->notnull()->comment('请求地址');
            $table->enum('method', ['get', 'post', 'delete', 'options', 'put', 'head', 'patch'])->notnull()->comment('请求方法GET,HEAD,POST,PUT,PATCH,DELETE,OPTIONS');
            $table->text('get_parame')->notnull()->comment('GET参数包');
            $table->longText('post_parame')->notnull()->comment('POST参数包');
            $table->text('file_parame')->notnull()->comment('上传文件参数包');
            $table->longText('respond')->notnull()->comment('响应信息');
            $table->unsignedSmallInteger('respond_status')->notnull()->comment('响应状态值');
            $table->string('meid', 50)->notnull()->comment('终端唯一标识码');
            $table->string('ip', 50)->notnull()->comment('终端IP');
            $table->string('platform', 30)->notnull()->comment('终端请求平台，安卓，IOS，微信等');
            $table->text('request_header')->notnull()->comment('请求头信息');
            $table->text('respond_header')->notnull()->comment('响应头信息');
            $table->text('initial_session')->notnull()->comment('初始SESSION信息');
            $table->text('finish_session')->notnull()->comment('响应后SESSION信息');
            $table->longText('sql_query')->notnull()->comment('SQL语句');
            $table->unsignedInteger('request_at')->notnull()->comment('请求时间');
            $table->unsignedInteger('respond_at')->notnull()->comment('响应时间');
            $table->engine = 'MyISAM';
        });
    }

}
