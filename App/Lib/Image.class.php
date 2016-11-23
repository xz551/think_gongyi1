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
     * ���вü�
     * @param $img ͼƬ���� ��� xxxx_fs_xxxxxxxxxx_ext;
     * @param bool $arr ��߼��� ����д�򷵻�ԭͼ
     * @param bool $type ͼƬ����
     */
    public static function getUrlThumbCenter($img, $arr = false,$type=false){
        return self::getUrlThumb($img, $arr,$type,\Think\Image::IMAGE_THUMB_CENTER);
    }

    /**
     * �ȱ������ţ�����ĵط����հף���ֹͼƬŤ��
     * @param $img
     * @param bool $arr
     * @param bool $type
     * @return string
     */
    public static function getUrlThumbFilled($img, $arr = false,$type=false){
        return self::getUrlThumb($img, $arr,$type,\Think\Image::IMAGE_THUMB_FILLED);
    }
    /** ����ͼƬ���ʵ�ַ
     * ���Զ���ͼƬ�������� Ĭ�Ͼ��вü�
     * @param string $img  ͼƬ���� ��� xxxx_fs_xxxxxxxxxx_ext;
     * @param bool $arr ��߼��� ����д�򷵻�ԭͼ
     * @return string ͼƬ���ʵ�ַ
     */
    public static function getUrl($img, $arr = false,$type=false,$thumb=\Think\Image::IMAGE_THUMB_CENTER)
    {
        return self::getUrlThumb($img, $arr,$type,$thumb);
    }
    /** ����ͼƬ���ʵ�ַ
     * �ȱ������ţ�����ĵط����հף���ֹͼƬŤ��
     * @param string $img  ͼƬ���� ��� xxxx_fs_xxxxxxxxxx_ext;
     * @param bool $arr ��߼��� ����д�򷵻�ԭͼ
     * @param string $type ͼƬ����
     * @return string ͼƬ���ʵ�ַ
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
         * ԭͼƬ
         */
        $filepath=$file_type_path.'/'.$img_name;
        if(!file_exists($filepath)){
            //ԭͼ�����ڷ���Ĭ��ͼƬ
            return self::defImg($type ? $type :$fileType);
        }

        //ͼƬ��ȡ���͵�·��
        $image = new \Think\Image();
        $image->open($filepath);
        $thumb=self::getThumb($image,$arr,$thumb);
        $new_img_width=$arr[0]==0?PHP_INT_MAX:$arr[0];
        $new_img_height=$arr[1]==0?PHP_INT_MAX:$arr[1];

        $file_thumb_path=$file_type_path.'/'.$thumb; 
        if(!is_dir($file_thumb_path)){
            //·�������� ����ͼƬ��ȡ���͵�·��
            mkdir($file_thumb_path, 0777, true);
        }

        //ͼƬ���·��
        $new_img_width_path=$file_thumb_path.'/'.$new_img_width;
        if(!is_dir($new_img_width_path)){
            //·�������� ����ͼƬ���·��
            mkdir($new_img_width_path, 0777, true);
        }
        //ͼƬ�ߵ�·��
        $new_img_height_path=$new_img_width_path.'/'.$new_img_height;
        if(!is_dir($new_img_height_path)){
            //�ߵ�·�������� ���߸ߵ�·��
            mkdir($new_img_height_path, 0777, true);
        }
        //���ͼƬ���ڿ�͸�·���Ƿ����
        if(file_exists($new_img_height_path.'/'.$img_name)){
            //�Ѿ�����ֱ�ӷ���
            return IMAGE_SERVER_URL . '/' . $fileType . '/'.$thumb.'/'.$new_img_width.'/'.$new_img_height.'/' . $img_name;
        }
        //��͸�·���ڲ�����ͼƬ ����ͼƬ

        $new_img_path=$new_img_height_path.'/'.$img_name;
        $image->thumb($arr[0],$arr[1],$thumb)->save($new_img_path);
        return IMAGE_SERVER_URL . '/' . $fileType . '/'.$thumb.'/' .$new_img_width.'/'.$new_img_height.'/' . $img_name;
    }

    /**�ϴ�ͼƬ
     * @param string $type ͼƬ���ͣ�����ͼ��ŵ�λ��
     * @param array $arr �ϴ������󣬷���ͼƬ�Ĵ�С
     * @return array ͼƬ�ϴ���Ϣ
     */
    public static function upload($type,$arr=false){
        $upload = new Upload(); // ʵ�����ϴ���
        $upload->maxSize = 2 * 1024 * 1024; // ���ø����ϴ���С
        $upload->exts = array('jpg', 'png', 'jpeg'); // ���ø����ϴ�����
        $upload->rootPath =IMAGE_SERVER_PATH . "/$type/"; // ���ø����ϴ�Ŀ¼    // �ϴ��ļ�
        if(!is_dir($upload->rootPath)){
            mkdir($upload->rootPath, 0777, true);
        }
        $upload->autoSub = false;
        $info = $upload->upload();
        if (!$info) {// �ϴ�������ʾ������Ϣ
            $error = $upload->getError();
            if (strpos($error, 'upload_max_filesize') || strpos($error, 'MAX_FILE_SIZE')) {
                $error = '�ļ���С��������';
            }
            $result = array('status' => 0, 'info' => $error);
            return $result;
        }else{// �ϴ��ɹ�
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
     * ����Ҫȡ��ͼƬ�Ĵ�С������Ҫ�и�ķ�ʽ
     * @param $img
     * @param bool $arr
     * @param bool $type
     * @param int $thumb
     */
    private static function getThumb($image, &$arr = false,$thumb){
        $width = $image->width(); // ����ͼƬ�Ŀ��
        $height = $image->height(); // ����ͼƬ�ĸ߶�
        if($arr[0]==0 || $arr[1]==0){
            if($width== $height ){
                if($arr[0]==0){
                    $arr[0]=$arr[1];
                }else{
                    $arr[1]=$arr[0];
                }
            }
            //�ȱ�������
            return \Think\Image::IMAGE_THUMB_SCALE;
        }
        if( $width/$height==$arr[0]/$arr[1] ){
            //�ȱ�������
            return \Think\Image::IMAGE_THUMB_SCALE;
        }
        //û���������������ָ��������
        return $thumb;
    }
    /**
     * ɾ��ͼƬ
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