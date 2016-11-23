<?php
namespace Lib;
use Think\Think;
use Think\Upload;
class Image
{
    private static function defImg($type){
        $dif_img=array( 
            'project' => STATIC_SERVER_URL . '/public/images/project_default.png',
            'user' => STATIC_SERVER_URL . '/public/images/user_default.png',
            'user_girl' => STATIC_SERVER_URL . '/public/images/user_default_girl.png',
            'event' => STATIC_SERVER_URL . '/public/images/project_default.png',
            'banner' => STATIC_SERVER_URL . '/public/images/banner_default.jpg',
            'active' => STATIC_SERVER_URL . '/public/images/project_default.png'
        );
        $d=$dif_img[$type];
        return $d? $d :'javascript::';
    }

    /**
     * 居中裁剪
     * @param $img 图片名称 规格 xxxx_fs_xxxxxxxxxx_ext;
     * @param bool $arr 宽高集合 不填写则返回原图
     * @param bool $type 图片类型
     */
    public static function getUrlThumbCenter($img, $arr = false,$type=false){
        return self::getUrlThumb($img, $arr,$type,\Think\Image::IMAGE_THUMB_CENTER);
    }

    /**
     * 等比例缩放，不足的地方填充空白，防止图片扭曲
     * @param $img
     * @param bool $arr
     * @param bool $type
     * @return string
     */
    public static function getUrlThumbFilled($img, $arr = false,$type=false){
        return self::getUrlThumb($img, $arr,$type,\Think\Image::IMAGE_THUMB_FILLED);
    }
    /** 返回图片访问地址
     * 可自定义图片处理类型 默认居中裁剪
     * @param string $img  图片名称 规格 xxxx_fs_xxxxxxxxxx_ext;
     * @param bool $arr 宽高集合 不填写则返回原图
     * @return string 图片访问地址
     */
    public static function getUrl($img, $arr = false,$type=false,$thumb=\Think\Image::IMAGE_THUMB_CENTER)
    {
        return self::getUrlThumb($img, $arr,$type,$thumb);
    }
    /** 返回图片访问地址
     * 等比例缩放，不足的地方填充空白，防止图片扭曲
     * @param string $img  图片名称 规格 xxxx_fs_xxxxxxxxxx_ext;
     * @param bool $arr 宽高集合 不填写则返回原图
     * @param string $type 图片类型
     * @return string 图片访问地址
     */
    private static function getUrlThumb($img, $arr = false,$type=false,$thumb=\Think\Image::IMAGE_THUMB_FILLED){
	if(!$img){
            return self::defImg($type);
        }
        if (strstr($img, 'http://')) {
            return $img;;
        }
        $temp = explode('_', $img);
        list($fileType, $fileServer, $fileName, $fileExt) = $temp;
        $img_name=$fileName.'.'.$fileExt;
        if (!$arr) {
            return IMAGE_SERVER_URL . '/' . $fileType . '/' .$img_name;
        }
        if(count($arr)==1){
            $arr[1]=$arr[0];
        }
        if($arr[0]==0 && $arr[1]==0){
            return IMAGE_SERVER_URL . '/' . $fileType . '/' . $img_name;
        }
        $file_type_path=IMAGE_SERVER_PATH.'/'.$fileType;

        /**
         * 原图片
         */
        $filepath=$file_type_path.'/'.$img_name;
        if(!file_exists($filepath)){
            //原图不存在返回默认图片
            return self::defImg($type ? $type :$fileType);
        }

        //图片截取类型的路径
        $image = new \Think\Image();
        $image->open($filepath);
        $thumb=self::getThumb($image,$arr,$thumb);
        $new_img_width=$arr[0]==0?PHP_INT_MAX:$arr[0];
        $new_img_height=$arr[1]==0?PHP_INT_MAX:$arr[1];

        $file_thumb_path=$file_type_path.'/'.$thumb; 
        if(!is_dir($file_thumb_path)){
            //路径不存在 创建图片截取类型的路径
            mkdir($file_thumb_path, 0777, true);
        }

        //图片宽的路径
        $new_img_width_path=$file_thumb_path.'/'.$new_img_width;
        if(!is_dir($new_img_width_path)){
            //路径不存在 创建图片宽的路径
            mkdir($new_img_width_path, 0777, true);
        }
        //图片高的路径
        $new_img_height_path=$new_img_width_path.'/'.$new_img_height;
        if(!is_dir($new_img_height_path)){
            //高的路径不存在 创高高的路径
            mkdir($new_img_height_path, 0777, true);
        }
        //检查图片所在宽和高路径是否存在
        if(file_exists($new_img_height_path.'/'.$img_name)){
            //已经存在直接返回
            return IMAGE_SERVER_URL . '/' . $fileType . '/'.$thumb.'/'.$new_img_width.'/'.$new_img_height.'/' . $img_name;
        }
        //宽和高路径在不存在图片 生成图片

        $new_img_path=$new_img_height_path.'/'.$img_name;
        $image->thumb($arr[0],$arr[1],$thumb)->save($new_img_path);
        return IMAGE_SERVER_URL . '/' . $fileType . '/'.$thumb.'/' .$new_img_width.'/'.$new_img_height.'/' . $img_name;
    }

    /**上传图片
     * @param string $type 图片类型，决定图存放的位置
     * @param array $arr 上传结束后，返回图片的大小
     * @return array 图片上传信息
     */
    public static function upload($type,$arr=false){
        $upload = new Upload(); // 实例化上传类
        $upload->maxSize = 2 * 1024 * 1024; // 设置附件上传大小
        $upload->exts = array('jpg', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath =IMAGE_SERVER_PATH . "/$type/"; // 设置附件上传目录    // 上传文件
        if(!is_dir($upload->rootPath)){
            mkdir($upload->rootPath, 0777, true);
        }
        $upload->autoSub = false;
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $error = $upload->getError();
            if (strpos($error, 'upload_max_filesize') || strpos($error, 'MAX_FILE_SIZE')) {
                $error = '文件大小超出限制';
            }
            $result = array('status' => 0, 'info' => $error);
            return $result;
        }else{// 上传成功
            $info = reset($info);
            $newFileName = $type.'_fs_' . str_replace('.', '_', $info["savename"]);

            $result['url'] =  self::getUrl($newFileName,$arr);
            $result['imgName'] = $newFileName;
            $result['filePath'] =$upload->rootPath. $info["savename"];
            $result['info'] = $info;

            return $result;
        }
    }

    /**
     * 根据要取得图片的大小，返回要切割的方式
     * @param $img
     * @param bool $arr
     * @param bool $type
     * @param int $thumb
     */
    private static function getThumb($image, &$arr = false,$thumb){
        $width = $image->width(); // 返回图片的宽度
        $height = $image->height(); // 返回图片的高度
        if($arr[0]==0 || $arr[1]==0){
            if($width== $height ){
                if($arr[0]==0){
                    $arr[0]=$arr[1];
                }else{
                    $arr[1]=$arr[0];
                }
            }
            //等比例缩放
            return \Think\Image::IMAGE_THUMB_SCALE;
        }
        if( $width/$height==$arr[0]/$arr[1] ){
            //等比例缩放
            return \Think\Image::IMAGE_THUMB_SCALE;
        }
        //没有特殊情况，返回指定的类型
        return $thumb;
    }
    /**
     * 删除图片
     */
    public static function delImage($img){
        if(!$img){
            return;
        }
        if (strstr($img, 'http://')) {
            return;
        }
        $temp = explode('_', $img);
        list($fileType, $fileServer, $fileName, $fileExt) = $temp;
        $img_name=$fileName.'.'.$fileExt;
        $file = IMAGE_SERVER_PATH.'/'.$fileType . '/' .$img_name;
        $result = @unlink ($file); 
        return $result;
    }
    
}