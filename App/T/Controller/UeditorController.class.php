<?php
namespace T\Controller;
use Think\Controller;
use Think\Upload;
class UeditorController extends Controller{
    public function index(){
        header('Access-Control-Allow-Origin: *');
        $action = $_GET['action'];
        $config = $this->config();
        switch($action){
            case 'config':
                $result = json_encode($config);
                break;
            case 'uploadimage':
                $cfg = array(
                    "pathFormat" => $config['imagePathFormat'],
                    "maxSize" => $config['imageMaxSize'],
                    "allowFiles" => $config['imageAllowFiles']
                );
                $fieldName = $config['imageFieldName'];
                $result = $this->upimg($cfg,$fieldName);
                break;
            default:
                $result = json_encode(array('state'=>'请求地址出错'));
                break;
        }
        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        } 
    }  
    
    private function upimg($config,$fieldName){
        /*
        $upload = new Upload(); // 实例化上传类
        $upload->maxSize = 2 * 1024 * 1024; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath =IMAGE_SERVER_PATH . "/project/"; // 设置附件上传目录    // 上传文件
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
        */
        return json_encode(     
            array(
                "state" => "success",          //上传状态，上传成功时必须返回"SUCCESS"
                "url" => "http://www.baidu.com/img/imgname.jpg",            //返回的地址
                "title" => "imgname.jpg",          //新文件名
                "original" => $fieldName,       //原始文件名
                "type" => ".jpj",            //文件类型
                "size" => "102400"          //文件大小
            )
        );      
    }
    
    //配置文件
    private function config(){
        return array(
           /*上传图片配置项*/
            'imageActionName'=>'uploadimage',   //执行上传图片的action的名称
            'imageFieldName'=>'upfile',         //提交的图片表单名称
            'imageMaxSize'=>'2048000',          //上传大小限制，单位B
            'imageAllowFiles'=> array(".png", ".jpg", ".jpeg", ".gif", ".bmp"), //上传图片格式显示
            'imageCompressEnable'=>true,    //是否压缩图片
            'imageCompressBorder'=>1600,    //图片压缩最长边限制
            'imageInsertAlign'=>'none',     //插入的图片浮动方式
            'imageUrlPrefix'=>'',           //图片访问路径前缀
            'imagePathFormat'=> "/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}"   //上传保存路径
        );
    }
        
}
