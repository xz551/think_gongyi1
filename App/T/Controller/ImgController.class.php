<?php
namespace T\Controller;
use Lib\Image\UploadedFile;
use Lib\Image\UploadImage;
use Think\Controller;
use Think\Image;
use Think\Upload;


// 用户上传头像
class ImgController extends Controller {


    /**
     * 异步上传处理
     * @param string $type 图片类型
     * @param array $size 返回的默认图片大小
     */
    public function upload($type='tmp',$w=0,$h=0) {
        $result= \Lib\Image::upload($type,array($w,$h));
        if(IS_AJAX){
            $this->ajaxReturn($result);
        }else{
            echo json_encode($result);
        }

    }
    /**
     * 异步上传图片 废弃
     */
    public function uploadImg($type='tmp',$w=100,$h=100) {
        
        
        $this->upload($type,$w,$h);
    }
    
    public function getUploadImg($newFileName,$type){ 
    	 $image = \Lib\Image::getUrl($newFileName, array(60));//UploadedFile::getFileUrl($newFileName, array(100,100), 'default');
         return $image;
    }
        
     /**
     * 编辑器上传图片
     */
    public function uploadEditorImg() {
        $type = I('get.type');
        if(empty($type)){
            $type = 'project';
        }
        $upload = new Upload(); // 实例化上传类
        $upload->maxSize = 2 * 1024 * 1024; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath =IMAGE_SERVER_PATH . "/$type/"; // 设置附件上传目录    // 上传文件
        $upload->autoSub = false;
        $info = $upload->upload();    
	if (!$info) {// 上传错误提示错误信息        
            $error = $upload->getError();
            if (strpos($error, 'upload_max_filesize') || strpos($error, 'MAX_FILE_SIZE')) {
                $error = '文件大小超出限制';
            }
            $result = array('error' => 1, 'message' => $error);
            echo json_encode($result);
        }else{// 上传成功
            $result['url'] = IMAGE_SERVER_URL.'/'.$type.'/'.$info['imgFile']["savename"];
            $result['error'] = 0;
            echo json_encode($result);
        }
    }

    
    
    
}
