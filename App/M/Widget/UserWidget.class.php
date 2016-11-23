<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/14
 * Time: 17:30
 */

namespace M\Widget;


use Lib\Image;
use M\Session\MUserSession;

class UserWidget extends WController
{
    /**
     * 用户资料卡片
     * 头像
     * 昵称
     * 类别 个人、组织
     * 服务时长
     *
     */
    public function card(){

        $user=MUserSession::getUser();
        $uid=MUserSession::getUserId();

        $user->gender=$user->gender==1?'男': ($user->gender==2?'女':'未知');

        $user_photo=Image::getUrl($user->photo,array(120),$user->gender==2?'user_girl':'user');
        $user->photo=$user_photo;

	    $this->user=$user;
        $server_time=M('project_recruit_detail')->where(['user_id'=>$uid])->sum('server_time');
        $this->server_time=$server_time?$server_time:0;
        $this->display("Widget/User:card");
    }

    /**
     * 我的项目
     */
    public function project($data=null){ 
        if($data===null){
            $uid=MUserSession::getUserId(); 
            $data=D('Project')->getProjectJoin($uid);
        }
        $this->data=$data;
        $this->display("Widget/User:project");
    }

    /**
     * 我的活动
     */
    public function active($data=null){
        if($data===null){
            $uid=MUserSession::getUserId();
            $data=D('Active')->getActiveJoin($uid);
        }
        $this->data=$data;
        $this->display("Widget/User:active");
    }
}