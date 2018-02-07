<?php 
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \DB; 
use \Cache;
use Auth;
use URL;
class UploadController extends CommonController {

    /**
     * 根据code获得常用类型
     *
     * @return Response
     */
    public function postUp(Request $request)    { 	
    	
    	$ext_arr = array(
    			'image' => array(
    					'gif',
    					'jpg',
    					'jpeg',
    					'png',
    					'bmp'
    			),
    			'flash' => array(
    					'swf',
    					'flv'
    			),
    			'media' => array(
    					'mp3',
    					'wav',
    					'wma',
    					'wmv',
    					'mid',
    					'avi',
    					'mpg',
    					'asf',
    					'rm',
    					'rmvb'
    			),
    			'file' => array(
    					'doc',
    					'docx',
    					'xls',
    					'xlsx',
    					'ppt',
    					'htm',
    					'html',
    					'txt',
    					'zip',
    					'rar',
    					'gz',
    					'bz2'
    			)
    	);
    	$resource =$request->input('resource');

    	$php_path = dirname(__FILE__) . '/';
    	$php_url ="/";
    	//文件保存目录路径
    	$save_path =config('app.uploadDir');
    	
    	//文件保存目录URL
    	$save_url = $php_url . 'attached' . '/';
    	if($resource=='ios'){
    		//filecontent 方式上传
    		if ($request->hasFile('filecontent'))
    		{
    			//PHP上传失败
    			if (!empty($_FILES['filecontent']['error'])) {
    				switch ($_FILES['filecontent']['error']) {
    					case '1':
    						$error = '超过php.ini允许的大小。';
    						break;
    					case '2':
    						$error = '超过表单允许的大小。';
    						break;
    					case '3':
    						$error = '图片只有部分被上传。';
    						break;
    					case '4':
    						$error = '请选择图片。';
    						break;
    					case '6':
    						$error = '找不到临时目录。';
    						break;
    					case '7':
    						$error = '写文件到硬盘出错。';
    						break;
    					case '8':
    						$error = 'File upload stopped by extension。';
    						break;
    					case '999':
    					default:
    						$error = '未知错误。';
    				}
    				$this->alert($error);
    			}
    			//文件大小
    			$file_size = $_FILES['filecontent']['size'];
    			//检查文件名
    			if (!$_FILES['filecontent']['name']) {
    				$this->alert("请选择文件。");
    			}
    			$file = $request->file('filecontent');

    			//最大文件大小
    			$max_size = 500000000;
                //文件扩展名
    			$file_ext=$file->getClientOriginalExtension();
    			$isAllow = false;
    			foreach ($ext_arr as $key => $val) {
    				if (in_array($file_ext, $val)) {
    					$isAllow = true;
    					$dir_name = $key;
    					break;
    				}
    			}
    			if (!$isAllow) {
    				$this->alert("上传文件扩展名是不允许的扩展名。");
    			}
    			//创建文件夹
    			if ($dir_name !== '') {
    				$save_path .= $dir_name . "/";
    				$save_url .= $dir_name . "/";
    				if (!file_exists($save_path)) {
    					mkdir($save_path);
    				}
    			}
    			$ymd = date("Ymd");
    			$save_path .= $ymd . "/";
    			$save_url .= $ymd . "/";
    			if (!file_exists($save_path)) {
    				mkdir($save_path);
    				mkdir($save_path."/_large");
    				mkdir($save_path."/_thumb");
    			}
    			//新文件名
    			$new_file = date("YmdHis") . '_' . rand(10000, 99999);
    			$new_file_name = $new_file.".".$file_ext;
    			//移动文件
    			$file_path = $save_path . $new_file_name;
    			//检查目录
    			if (@is_dir($save_path) === false) {
    				$this->alert("上传目录不存在。");
    			}
    			//检查目录写权限
    			if (@is_writable($save_path) === false) {
    				$this->alert("上传目录没有写权限。");
    			}
    			$tmp_name=$file->getPathname();
    			//检查是否已上传
    			if (@is_uploaded_file($tmp_name) === false) {
    				$this->alert("临时文件可能不是上传文件。");
    			}
    			//检查文件大小
    			if ($file_size > $max_size) {
    				$this->alert("上传文件大小超过限制。");
    			}
    			if (move_uploaded_file($tmp_name, $file_path) === false) {
    				$this->alert("上传文件失败。");
    			}
    		
    			//生成大图小图
    			$this->createBigSmallPhoto($file_path,$save_path,$new_file_name);
    			$file_url = $save_url . $new_file_name;
    			$data=array(
    					'url' =>URL::asset('').ltrim($file_url,'/'),
    					'path'=>$file_path,
    					'saveUrl'=> ltrim($file_url,'/')
    			);
    			return	AjaxCallbackMessage($data,true,'');
    		}else{
    			return	AjaxCallbackMessage('no filecontent',false,'');
    		}
    	}elseif($resource=='android'){   
    		$filetype=$request->input('filetype');
    		if(empty($filetype))
    			return AjaxCallbackMessage('no filetype',false,'');
    		//用stream流上传
    		$opts = array(
    				'http' => array(
    						'method' => "GET",
    						'timeout' => 60
    				)
    		);
    		$context = stream_context_create($opts);
    		$binary_string = file_get_contents('php://input', false, $context);
    		foreach ($ext_arr as $key => $val) {
				if (in_array($filetype, $val) && $filetype) {
				    if ($filetype == 'txt'){
                        $dir_name = 'android';//如果是安卓上传的txt文件放在安卓目录下
                    }else{
                        $dir_name = $key;//其他类型
                    }
					break;
				}
			}
    		// 创建文件夹
    		if ($dir_name !== '') {
    			$save_path .= $dir_name . "/";
    			$save_url .= $dir_name . "/";
    				
    			if (! file_exists($save_path)) {
    				mkdir($save_path);
    			}
    		}
    		$ymd = date("Ymd");
    		$save_path .= $ymd . "/";
    		$save_url .= $ymd . "/";
    		if (! file_exists($save_path)) {
    			mkdir($save_path);
    			mkdir($save_path."/_large");
    			mkdir($save_path."/_thumb");
    		}
    	  	$new_file = date("YmdHis") . '_' . rand(10000, 99999);
    		$new_file_name = $new_file.".".$filetype;
    		$file_path = $save_path . $new_file_name ;
    		@chmod($file_path, 0777);
    		$file = fopen($file_path, "wr"); // 打开文件准备写入
    		fwrite($file, $binary_string); // 写入
    		fclose($file); // 关闭

    		$file_url = $save_url . $new_file_name;
    		if(in_array($filetype,$ext_arr['image'])){
    			$this->createBigSmallPhoto($file_path,$save_path,$new_file_name);
    		}
    		$data=array(
    				'url' =>URL::asset('').ltrim($file_url,'/'),
   					'path'=>$file_path,
    				'saveUrl'=> ltrim($file_url,'/')
   			);
   			return	AjaxCallbackMessage($data,true,'');
    	}else{
    		return	AjaxCallbackMessage('no resource',false,'');
    	}

    }

    public function postUp1(Request $request)    {

        $ext_arr = array(
            'image' => array(
                'gif',
                'jpg',
                'jpeg',
                'png',
                'bmp'
            ),
            'flash' => array(
                'swf',
                'flv'
            ),
            'media' => array(
                'mp3',
                'wav',
                'wma',
                'wmv',
                'mid',
                'avi',
                'mpg',
                'asf',
                'rm',
                'rmvb'
            ),
            'file' => array(
                'doc',
                'docx',
                'xls',
                'xlsx',
                'ppt',
                'htm',
                'html',
                'txt',
                'zip',
                'rar',
                'gz',
                'bz2'
            )
        );
        $resource =$request->input('resource');

        $php_path = dirname(__FILE__) . '/';
        $php_url ="/";
        //文件保存目录路径
        $save_path =config('app.uploadDir');

        //文件保存目录URL
        $save_url = $php_url . 'attached' . '/';
        if($resource=='ios'){
            //filecontent 方式上传
            if ($request->hasFile('filecontent'))
            {
                //PHP上传失败
                if (!empty($_FILES['filecontent']['error'])) {
                    switch ($_FILES['filecontent']['error']) {
                        case '1':
                            $error = '超过php.ini允许的大小。';
                            break;
                        case '2':
                            $error = '超过表单允许的大小。';
                            break;
                        case '3':
                            $error = '图片只有部分被上传。';
                            break;
                        case '4':
                            $error = '请选择图片。';
                            break;
                        case '6':
                            $error = '找不到临时目录。';
                            break;
                        case '7':
                            $error = '写文件到硬盘出错。';
                            break;
                        case '8':
                            $error = 'File upload stopped by extension。';
                            break;
                        case '999':
                        default:
                            $error = '未知错误。';
                    }
                    $this->alert($error);
                }
                //文件大小
                $file_size = $_FILES['filecontent']['size'];
                //检查文件名
                if (!$_FILES['filecontent']['name']) {
                    $this->alert("请选择文件。");
                }
                $file = $request->file('filecontent');

                //最大文件大小
                $max_size = 500000000;
                //文件扩展名
                $file_ext=$file->getClientOriginalExtension();
                $isAllow = false;
                foreach ($ext_arr as $key => $val) {
                    if (in_array($file_ext, $val)) {
                        $isAllow = true;
                        $dir_name = $key;
                        break;
                    }
                }
                if (!$isAllow) {
                    $this->alert("上传文件扩展名是不允许的扩展名。");
                }
                //创建文件夹
                if ($dir_name !== '') {
                    $save_path .= $dir_name . "/";
                    $save_url .= $dir_name . "/";
                    if (!file_exists($save_path)) {
                        mkdir($save_path);
                    }
                }
                $ymd = date("Ymd");
                $save_path .= $ymd . "/";
                $save_url .= $ymd . "/";
                if (!file_exists($save_path)) {
                    mkdir($save_path);
                    mkdir($save_path."/_large");
                    mkdir($save_path."/_thumb");
                }
                //新文件名
                $new_file = date("YmdHis") . '_' . rand(10000, 99999);
                $new_file_name = $new_file.".".$file_ext;
                //移动文件
                $file_path = $save_path . $new_file_name;
                //检查目录
                if (@is_dir($save_path) === false) {
                    $this->alert("上传目录不存在。");
                }
                //检查目录写权限
                if (@is_writable($save_path) === false) {
                    $this->alert("上传目录没有写权限。");
                }
                $tmp_name=$file->getPathname();
                //检查是否已上传
                if (@is_uploaded_file($tmp_name) === false) {
                    $this->alert("临时文件可能不是上传文件。");
                }
                //检查文件大小
                if ($file_size > $max_size) {
                    $this->alert("上传文件大小超过限制。");
                }
                if (move_uploaded_file($tmp_name, $file_path) === false) {
                    $this->alert("上传文件失败。");
                }

                //生成大图小图
                $this->createBigSmallPhoto($file_path,$save_path,$new_file_name);
                $file_url = $save_url . $new_file_name;
                $data=array(
                    'url' =>URL::asset('').ltrim($file_url,'/'),
                    'path'=>$file_path,
                    'saveUrl'=> ltrim($file_url,'/')
                );
                return	$data;
            }else{
                return	AjaxCallbackMessage('no filecontent',false,'');
            }
        }elseif($resource=='android'){
            $filetype=$request->input('filetype');
            if(empty($filetype))
                return AjaxCallbackMessage('no filetype',false,'');
            //用stream流上传
            $opts = array(
                'http' => array(
                    'method' => "GET",
                    'timeout' => 60
                )
            );
            $context = stream_context_create($opts);
            $binary_string = file_get_contents('php://input', false, $context);
            foreach ($ext_arr as $key => $val) {
                if (in_array($filetype, $val) && $filetype) {
                    $dir_name = $key;
                    break;
                }
            }
            // 创建文件夹
            if ($dir_name !== '') {
                $save_path .= $dir_name . "/";
                $save_url .= $dir_name . "/";

                if (! file_exists($save_path)) {
                    mkdir($save_path);
                }
            }
            $ymd = date("Ymd");
            $save_path .= $ymd . "/";
            $save_url .= $ymd . "/";
            if (! file_exists($save_path)) {
                mkdir($save_path);
                mkdir($save_path."/_large");
                mkdir($save_path."/_thumb");
            }
            $new_file = date("YmdHis") . '_' . rand(10000, 99999);
            $new_file_name = $new_file.".".$filetype;
            $file_path = $save_path . $new_file_name ;
            @chmod($file_path, 0777);
            $file = fopen($file_path, "wr"); // 打开文件准备写入
            fwrite($file, $binary_string); // 写入
            fclose($file); // 关闭

            $file_url = $save_url . $new_file_name;
            if(in_array($filetype,$ext_arr['image'])){
                $this->createBigSmallPhoto($file_path,$save_path,$new_file_name);
            }
            $data=array(
                'url' =>URL::asset('').ltrim($file_url,'/'),
                'path'=>$file_path,
                'saveUrl'=> ltrim($file_url,'/')
            );
            return	$data;
        }else{
            return	AjaxCallbackMessage('no resource',false,'');
        }

    }

    /**
     * 图片截取
     *
     * @return Response
     */
    public function postCut(Request $request)    {
    	$cutInfo_type = $request->input('cut_type');
    	//原图路径
    	$cutInfo_image_upload = $request->input('cut_image');
    	//截图的坐标信息
    	$cutInfo_x = intval($request->input('cut_x'));
    	$cutInfo_y = intval($request->input('cut_y'));
    	$cutInfo_width = intval($request->input('cut_width'));
    	$cutInfo_height = intval($request->input('cut_height'));
    	//原图大小
    	$size = getimagesize($cutInfo_image_upload);
    	$width = $size[0];
    	$height = $size[1];
    	$j_x1 = $width / 170 * $cutInfo_x;
    	$j_y1 = $height / 170 * $cutInfo_y;
    	$j_w = $width / 170 * $cutInfo_width;
    	$j_h = $height / 170 * $cutInfo_height;
    	//裁剪生成的图片路径
    	$cutInfo_image_after = getImage($request->input('cut_image'),'user','thumb');
    	$cutInfo_image_after = str_replace(URL::asset('/'), '', $cutInfo_image_after);
    	//调用方法裁剪头像
    	self::cut($cutInfo_image_upload, $cutInfo_image_after, $j_x1, $j_y1, $j_w, $j_h);
    	$image_url = '';
    	if($cutInfo_type == 'user'){
    		$image_url = getImage($cutInfo_image_upload,'user','thumb');
    	}else if($cutInfo_type == 'project'){
    		$image_url = getImage($cutInfo_image_upload,'project','thumb');
    	}
    	return	AjaxCallbackMessage($image_url,true,'');
    }
    private function createBigSmallPhoto($file_path,$save_path,$new_file_name){
    	//大
    	$imageInfo=getimagesize($file_path);
    	$lh = $imageInfo[1] /( $imageInfo[0] / 358);
    	$large_url = $save_path ."/_large/". $new_file_name;
    	$this->img_create_small($file_path, 358, $lh, $large_url);
    	//小
    	$th = $imageInfo[1] /( $imageInfo[0] / 158);
    	$thumb_url = $save_path ."/_thumb/". $new_file_name;
    	$this->img_create_small($file_path, 158, $th, $thumb_url);
    }
    private function alert($msg) {
    	return	AjaxCallbackMessage($msg,false,'');
    }
    static function img_create_small($big_img, $width, $height, $small_img) { // 原始大图地址，缩略图宽度，高度，缩略图地址
    	$imgage = getimagesize($big_img); // 得到原始大图片
    	switch ($imgage[2]) { // 图像类型判断
    		case 1:
    			$im = imagecreatefromgif($big_img);
    			break;
    		case 2:
    			$im = imagecreatefromjpeg($big_img);
    			break;
    		case 3:
    			$im = imagecreatefrompng($big_img);
    			break;
    		case 6:
    			$im = self::_imageCreateFromBMP($big_img);
    			break;
    	}
    	$src_W = $imgage[0]; // 获取大图片宽度
    	$src_H = $imgage[1]; // 获取大图片高度
    	$tn = imagecreatetruecolor($width, $height); // 创建缩略图
    	imagecopyresampled($tn, $im, 0, 0, 0, 0, $width, $height, $src_W, $src_H); // 复制图像并改变大小
    	imagejpeg($tn, $small_img); // 输出图像
    }
    
    public static function _imageCreateFromBMP($filePath)
    {
    	$fileHandle = fopen($filePath, 'rb');
    	if (empty($fileHandle)) {
    		return false;
    	}
    
    	$file = unpack(
    		'vfile_type/Vfile_size/Vreserved/Vbitmap_offset',
    		fread($fileHandle, 14)
    	);
    
    	if ($file['file_type'] != 19778) {
    		return false;
    	}
    
    	$bmp = unpack(
    		'Vheader_size/Vwidth/Vheight/vplanes/'.
    		'vbits_per_pixel/Vcompression/Vsize_bitmap/'.
    		'Vhoriz_resolution/Vvert_resolution/Vcolors_used/Vcolors_important',
    		fread($fileHandle, 40)
    	);
    	$bmp['colors'] = pow(2, $bmp['bits_per_pixel']);
    	if ($bmp['size_bitmap'] == 0) {
    		$bmp['size_bitmap'] = $file['file_size'] - $file['bitmap_offset'];
    	}
    	$bmp['bytes_per_pixel'] = $bmp['bits_per_pixel'] / 8;
    	$bmp['bytes_per_pixel2'] = ceil($bmp['bytes_per_pixel']);
    	$bmp['decal'] =  $bmp['width'] * $bmp['bytes_per_pixel'] / 4;
    	$bmp['decal'] -= floor($bmp['width'] * $bmp['bytes_per_pixel'] / 4);
    	$bmp['decal'] = 4 - (4 * $bmp['decal']);
    	if ($bmp['decal'] == 4) {
    		$bmp['decal'] = 0;
    	}
    
    	$palette = array();
    	if ($bmp['colors'] < 16777216) {
    		$palette = unpack(
    			'V' . $bmp['colors'],
    			fread($fileHandle, $bmp['colors'] * 4)
    		);
    	}
    	$image = fread($fileHandle, $bmp['size_bitmap']);
    	$vide = chr(0);
    	$res = imagecreatetruecolor($bmp['width'], $bmp['height']);
    	$p = 0;
    
    	$y = $bmp['height'] - 1;
    	while ($y >= 0) {
    		$x = 0;
    		while ($x < $bmp['width']) {
    			if ($bmp['bits_per_pixel'] == 24) {
    				$color = unpack('V', substr($image, $p, 3) . $vide);
    			} else if ($bmp['bits_per_pixel'] == 16) {
    				$color = unpack('n', substr($image, $p, 2));
    				$color[1] = $palette[$color[1]+1];
    			} else if ($bmp['bits_per_pixel'] == 8) {
    				$color = unpack('n', $vide . substr ($image, $p, 1));
    				$color[1] = $palette[$color[1]+1];
    			} else if ($bmp['bits_per_pixel'] ==4) {
    				$color = unpack('n', $vide . substr($image, floor($p), 1));
    				if (($p * 2) % 2 == 0) {
    					$color[1] = ($color[1] >> 4);
    				} else {
    					$color[1] = ($color[1] & 0x0F);
    				}
    				$color[1] = $palette[$color[1] + 1];
    			} else if ($bmp['bits_per_pixel'] == 1) {
    				$color = unpack('n', $vide . substr($image, floor($p), 1));
    				switch (($p * 8) % 8) {
    					case  0:
    						$color[1] = ($color[1] >> 7);
    						break;
    					case  1:
    						$color[1] = ($color[1] & 0x40) >> 6;
    						break;
    					case  2:
    						$color[1] = ($color[1] & 0x20) >> 5;
    						break;
    					case  3:
    						$color[1] = ($color[1] & 0x10) >> 4;
    						break;
    					case  4:
    						$color[1] = ($color[1] & 0x8) >> 3;
    						break;
    					case  5:
    						$color[1] = ($color[1] & 0x4) >> 2;
    						break;
    					case  6:
    						$color[1] = ($color[1] & 0x2) >> 1;
    						break;
    					case  7:
    						$color[1] = ($color[1] & 0x1);
    						break;
    				}
    				$color[1] = $palette[$color[1] + 1];
    			} else {
    				return false;
    			}
    			imagesetpixel($res, $x, $y, $color[1]);
    			$x++;
    			$p += $bmp['bytes_per_pixel'];
    		}
    		$y--;
    		$p += $bmp['decal'];
    	}
    	fclose($fileHandle);
    	return $res;
    }
    
    /**
     *
     * @param unknown_type $img
     * @return multitype:unknown string number Ambigous <> |boolean
     */
    static function get_image_info($img) {
    
    	$image_info = getimagesize($img);
    	if ($image_info !== false) {
    		$image_type = strtolower(substr(image_type_to_extension($image_info[2]), 1));
//            dd($img);
    		$image_size = filesize($img);
    		$info = array(
    				'width' => $image_info[0],
    				'height' => $image_info[1],
    				'type' => $image_type,
    				'size' => $image_size,
    				'mime' => $image_info['mime']
    		);
    		return $info;
    	}
    	else {
    		return false;
    	}
    }
    
    static function cut($image, $cut_image, $src_x, $src_y, $width, $height) {
    
    	$info = self::get_image_info($image);
    	if ($info !== false) {
    		$src_type = strtolower($info['type']);
    		$create_fun = 'imagecreatefrom' . ($src_type == 'jpg' ? 'jpeg' : $src_type);
    		$im_src = $create_fun($image);
    		if (function_exists('imagecreatetruecolor')) {
    			$im_cut = imagecreatetruecolor($width, $height);
    		}
    		else {
    			$im_cut = imagecreate($width, $height);
    		}
    		if (function_exists('imagecopyresampled')) {
    			imagecopyresampled($im_cut, $im_src, 0, 0, $src_x, $src_y, $width, $height, $width, $height);
    		}
    		else {
    			imagecopyresized($im_cut, $im_src, 0, 0, $src_x, $src_y, $width, $height, $width, $height);
    		}
    		// TP_filesys::make_dir(dirname($cut_image));
    		imageinterlace($im_cut, 1);
    		imagejpeg($im_cut, $cut_image, 100);
    		imagedestroy($im_cut);
    		imagedestroy($im_src);
    		return $cut_image;
    	}
    	return false;
    }

}