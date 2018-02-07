<?php

namespace App\Http\Controllers\Citypartner;

use Illuminate\Http\Request;
use URL;

define('DS', DIRECTORY_SEPARATOR);

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UploadController extends Controller {

    protected $uploadDir;
    protected $cshhrUrl;

    function __construct() {
//        $this->cshhrUrl = config('system.cshhrUrl');
//        $this->uploadDir = config('upload.uploadDir');
        $this->uploadDir = public_path('attached/');
    }

    public function postIndex(Request $request, $response = true) {
        //定义好哪些文件允许上传,同时为文件夹名做准备
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
//            'flash' => array('swf','flv'),
//            'media' => array('swf','flv','mp3','wav','wma','wmv', 'mid','avi','mpg','asf','rm','rmvb'),
//            'file' => array('doc','docx','xls','xlsx','ppt','htm','html','txt','zip','rar','gz','pdf','bz2')
        );
        if ($request->hasFile('myfile')) {
            //PHP上传失败
            if (!empty($_FILES['myfile']['error'])) {
                switch ($_FILES['myfile']['error']) {
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
                return $this->alert($error);
            }
            //文件大小
            $file_size = human_filesize($_FILES['myfile']['size']);
            //检查文件名
            if (!$_FILES['myfile']['name']) {
                return $this->alert("请选择文件。");
            }
            $file = $request->file('myfile');

//            $php_path = dirname(__FILE__) . DS;
//            $php_url = "";
            //文件保存目录路径
//            $save_path = config('upload.uploadDir');
            $save_path = $this->uploadDir;
            //文件保存目录URL
//            $save_url = $php_url . '/public' . '/attached/';
            //最大文件大小
            $max_size = 500000000;

            //获取绝对路径
            $save_path = realpath($save_path) . DS;

            //获取扩展名
            $file_ext = $file->getClientOriginalExtension();
            //判断是否允许上传
            $isAllow = false;
            foreach ($ext_arr as $key => $val) {
                if (in_array($file_ext, $val)) {
                    $isAllow = true;
                    $dir_name = $key;
                    break;
                }
            }
            if (!$isAllow) {
                return $this->alert("上传文件扩展名是不允许的扩展名。");
            }

            //创建类型文件夹
            if ($dir_name !== '') {
                $save_path .= $dir_name . "/";
//                $save_url .= $dir_name . "/";
                if (!file_exists($save_path)) {
                    mkdir($save_path);
                    mkdir($save_path . "_large");
                    mkdir($save_path . "_thumb");
                }
            }
            //创建日期文件夹
            $ymd = date("Ymd");
            $save_path .= $ymd . "/";
//            $save_url .= $ymd . "/";
            if (!file_exists($save_path)) {
                mkdir($save_path);
                mkdir($save_path . "_large");
                mkdir($save_path . "_thumb");
            }
            //新文件名
            $new_file = date("YmdHis") . '_' . rand(10000, 99999);
            $new_file_name = $new_file . "." . $file_ext;
            //移动文件
            $file_path = $save_path . $new_file_name;
            //检查目录
            if (@is_dir($save_path) === false) {
                return $this->alert("上传目录不存在。");
            }
            //检查目录写权限
            if (@is_writable($save_path) === false) {
                return $this->alert("上传目录没有写权限。");
            }
            $tmp_name = $file->getPathname();
            //检查是否已上传 检查文件是不是通过post方法上传的文件 隔离攻击
            if (@is_uploaded_file($tmp_name) === false) {
                return $this->alert("临时文件可能不是上传文件。");
            }
            //检查文件大小
            if ($file_size > $max_size) {
                return $this->alert("上传文件大小超过限制。");
            }
            if (move_uploaded_file($tmp_name, $file_path) === false) {
                return $this->alert("上传文件失败。");
            }
//            $file_url = $save_url . $new_file_name;
            //如果是图片则生成大图和小图
            if ('image' == $dir_name) {
                //判断原图是否要压缩
                $width = $request->input('width');
                $height = $request->input('height');
                if ($width > 0 && $height > 0) {//原图需要缩略处理
                    $this->img_create_small($file_path, $width, $height, $file_path);
                }
                //生成大图小图
                //大
                $imageInfo = getimagesize($file_path);
                $lh = $imageInfo[1] / ($imageInfo[0] / 358);
                $large_url = $save_path . "_large/" . $new_file_name;
                $this->img_create_small($file_path, 358, $lh, $large_url);
                //小
                $th = $imageInfo[1] / ($imageInfo[0] / 158);
                $thumb_url = $save_path . "_thumb/" . $new_file_name;
                $this->img_create_small($file_path, 158, $th, $thumb_url);
            }
//            print_r($this->cshhrUrl . str_replace('/public/', '', $file_url));die();
//            header('Content-type: text/html; charset=UTF-8');
            $basePath = str_replace('\\', '/', str_replace(public_path(), '', $file_path));
            $data = array(
                'error' => 0,
                'url' => $request->getUriForPath($basePath),
                'path' => ltrim($basePath, '/'),
                'filename' => $_FILES['myfile']['name'],
                'filetype' => $file_ext,
                'filesize' => $file_size,
            );
            if ($response) {
                return AjaxCallbackMessage($data);
            } else {
                return $data;
            }
        } else {
            return $this->alert("上传失败");
        }
    }

    /**
     * 图片截取
     *
     * @return Response
     */
    public function postCut(Request $request) {
        //原图路径
        if ($path = $request->input('path')) {
            if (strpos($path, 'http://') === 0) {
                return AjaxCallbackMessage('未上传图片', false);
//                $path=ltrim(parse_url($path,PHP_URL_PATH),'/');
            }
            $result = ['path' => $path];
        } else {
            $result = $this->postIndex($request, false);
            if (!isset($result['path'])) {
                return $result;
            }
        }
        $image_upload = public_path($result['path']);
        //截图的坐标信息
        $cutInfo_x = intval($request->input('cut_x'));
        $cutInfo_y = intval($request->input('cut_y'));
        $cutInfo_width = intval($request->input('cut_width'));
        $cutInfo_height = intval($request->input('cut_height'));
        $pathInfo = pathinfo($image_upload);
        $cutInfo_image_upload = $pathInfo ['dirname'] . '/cut_' . $pathInfo ['basename'];
        //调用方法裁剪头像
        self::cut($image_upload, $cutInfo_image_upload, $cutInfo_x, $cutInfo_y, $cutInfo_width, $cutInfo_height);
        //生成大图小图
        $save_path = dirname($cutInfo_image_upload);
        file_exists($save_path . "/_large") || mkdir($save_path . "/_large");
        file_exists($save_path . "/_thumb") || mkdir($save_path . "/_thumb");

        $new_file_name = basename($cutInfo_image_upload);
        //大
        $imageInfo = getimagesize($cutInfo_image_upload);
        $lh = $imageInfo[1] / ($imageInfo[0] / 358);
        $large_url = $save_path . "/_large/" . $new_file_name;
        $this->img_create_small($cutInfo_image_upload, 358, $lh, $large_url);
        //小
        $th = $imageInfo[1] / ($imageInfo[0] / 158);
        $thumb_url = $save_path . "/_thumb/" . $new_file_name;
        $this->img_create_small($cutInfo_image_upload, 158, $th, $thumb_url);
        $basePath = str_replace('\\', '/', str_replace(public_path(), '', $cutInfo_image_upload));
        return AjaxCallbackMessage(ltrim($basePath, '/'), true, '');
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
        } else {
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
            } else {
                $im_cut = imagecreate($width, $height);
            }
            if (function_exists('imagecopyresampled')) {
                imagecopyresampled($im_cut, $im_src, 0, 0, $src_x, $src_y, $width, $height, $width, $height);
            } else {
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

    private function alert($msg) {
        return AjaxCallbackMessage($msg, false);
//        header('Content-type: text/html; charset=UTF-8');
//        echo json_encode(
//            array(
//                'error'   => 1,
//                'message' => $msg
//            )
//        );
//        exit();
    }

    function img_create_small($big_img, $width, $height, $small_img) { // 原始大图地址，缩略图宽度，高度，缩略图地址
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
            default :
                return;
        }
        $src_W = $imgage[0]; // 获取大图片宽度
        $src_H = $imgage[1]; // 获取大图片高度
        $tn = imagecreatetruecolor($width, $height); // 创建缩略图
        $color = imagecolorallocatealpha($tn, 255, 255, 255, 127);
        imagefill($tn, 0, 0, $color);
        $dst_x = 0;
        $dst_y = 0;
        $dst_ratio = round($width / $height, 2);
        $src_ratio = round($src_W / $src_H, 2);
        if ($dst_ratio > $src_ratio) {//高度过大
            if ($width > $src_W && $height > $src_H) {//源图片尺寸都小于目标图片
                $dst_y = round(($height - $src_H) / 2);
                $height = $src_H;
            }
            $_width = $height * $src_ratio;
            $dst_x = round(($width - $_width) / 2);
            $width = $_width;
        } elseif ($dst_ratio < $src_ratio) {//宽度过大
            if ($width > $src_W && $height > $src_H) {//源图片尺寸都小于目标图片
                $dst_x = round(($width - $src_W) / 2);
                $width = $src_W;
            }
            $_height = $width / $src_ratio;
            $dst_y = round(($height - $_height) / 2);
            $height = $_height;
        } elseif ($width > $src_W || $height > $src_H) {//图片过小
            $dst_x = round(($width - $src_W) / 2);
            $dst_y = round(($height - $src_H) / 2);
            $width = $src_W;
            $height = $src_H;
        }
        imagecopyresampled($tn, $im, $dst_x, $dst_y, 0, 0, $width, $height, $src_W, $src_H); // 复制图像并改变大小
        imagejpeg($tn, $small_img, 100); // 输出图像
    }

}
