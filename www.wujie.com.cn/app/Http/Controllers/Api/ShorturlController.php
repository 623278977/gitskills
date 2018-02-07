<?php
	/**
	 * 短链接控制器
	 */
	namespace App\Http\Controllers\Api;
	use App\Http\Controllers\Api\CommonController;
	use Illuminate\Http\Request;
	use DB;
	class ShorturlController extends CommonController
	{
		private static $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
	    /**
	     * 生成短链接唯一ID
	     * @param  string $url 长链接
	     * @return 短链接
	     */
	    private static function encode($url){
	        $key = 'tyrbl'; //加盐
	        $urlhash = md5($key . $url);
	        $len = strlen($urlhash);
	        //将加密后的串分成4段，每段4字节，对每段进行计算，一共可以生成四组短连接
	        for ($i = 0; $i < 4; $i++) {
	            $urlhash_piece = substr($urlhash, $i * $len / 4, $len / 4);    
	            //将分段的位与0x3fffffff做位与，0x3fffffff表示二进制数的30个1，即30位以后的加密串都归零
	            //此处需要用到hexdec()将16进制字符串转为10进制数值型，否则运算会不正常
	            $hex = hexdec($urlhash_piece) & 0x3fffffff;
	            //域名根据需求填写
                // http://w.wujie.org/
	            $short_url = "http://test.wujie.com.cn/";
	            //生成6位短网址
	            for ($j = 0; $j < 5; $j++) {	                
	                //将得到的值与0x0000003d,3d为61，即charset的坐标最大值
	                $short_url .= self::$charset[$hex & 0x0000003d];	                
	                //循环完以后将hex右移5位
	                $hex = $hex >> 5;
	            }	 
	            $short_url_list[] = $short_url;
	        }	 
	        return $short_url_list;
	    }
	    public function postAjaxshort(Request $request){
	    	$url = trim($request->input('url'));
	    	// 接受请求短链接的地址
			// 检查需要压缩的URL格式是否正确
			$prt = substr($url,0,strpos($url, ':')+3);
			$prt_array = array('http://','https://');
			$res = in_array($prt, $prt_array);
			if(!in_array($prt, $prt_array)){
				return AjaxCallbackMessage('请检查地址格式是否正确！',FALSE);
			}else{
				$short = self::encode($url);
				$urlid = substr($short[0], strrpos($short[0], '/')+1);
				$res = DB::table('mappedurl')->where('shortCode',$urlid)->get();
				$createtime = time();
				if(!$res){
					DB::table('mappedurl')->insert([
						['shortCode'=>$urlid,'longURL'=>$url,'createtime'=>$createtime]
					]);
				}
				return AjaxCallbackMessage($short[0],TRUE,$url);
			}
	    }
	    public static function shortredirecte($shorturlID){
            $data = DB::table('mappedurl')->where('shortCode',$shorturlID)->select('longURL')->first();
            if(!$data){
                return json_encode(array('message'=>'请求地址有误，请检查请求参数是否正确','status'=>'false'));
            }else{
                $url = $data->longURL;
                return redirect("$url");
            }
        }
	}