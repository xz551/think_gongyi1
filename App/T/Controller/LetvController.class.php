<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/6/17
 * Time: 16:33
 */

namespace T\Controller;


use Lib\LetvCloudV1;
use Lib\UserSession;
use Think\Controller;

/**
 * 乐视视频操作控制器
 * Class LetvController
 * @package T\Controller
 */
class LetvController extends Controller {
    public function init($pid){
        tag('user_login');
        $letv=new LetvCloudV1();
        $uid=UserSession::getUser('uid');
        $video_name=sprintf("pid=%s-uid=%d",'s'.$pid,$uid);
        $result=$letv->videoUploadInit($video_name);
        echo $result;
    }

    /**
     * 查询上传进度
     * @param $url
     */
    public function uploadprogress($url){
        $letv=new LetvCloudV1();
        echo $letv->curl($url);
    }

    /**
     * 取得视频单个信息
     * @param $id
     */
    public function videoinfo($id){
        $letv=new LetvCloudV1();
        echo $letv->videoGet($id);
    }
}