<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace T\Widget;
use Lib\UserSession;
use T\Model\UCenterModel;
/**
 * Description of HeaderWidget
 *
 * @author Administrator
 */
class LayoutWidget extends WController{
    
    public function header(){
        $u=UserSession::getUser();     
        if($u){
           $noticeNum =  api("/User/getUnreadNoticeNum",array('uid'=>UserSession::getUser('uid'))); //通知数
           $messageboxNum= api("/User/getMessageboxNum",array('uid'=>UserSession::getUser('uid'))); //私信数
           $islogin = 1;
            //获取url地址
            $this->assign('privateLetter',UCENTER.'/inbox/messagebox/mainbox.html');
            $this->assign('edit',UCENTER.'/accountinfo/base.html');
            $url = urlSafeBase64_encode(SERVER_VISIT_URL);
            $this->assign('logout',UCENTER.'/user/logout.html?returnurl='.$url);
            $this->assign('notice',UCENTER.'/inbox/notification/mylist.html');
        }else{
           $url = urlSafeBase64_encode(YIJUAN_VISIT_URL.'/'.$_SERVER['REQUEST_URI']);
           $this->assign('login',UCENTER.'/user/login.html?returnurl='.$url);
           $this->assign('register',UCENTER.'/user/register.html');
           $isLogin = 0;
        }                      
        $this->assign('isLogin',$islogin);
        $this->assign('user',$u);
        $this->assign('noticeNum',$noticeNum);
        $this->assign('messageboxNum',$messageboxNum);
        $this->display("Widget/Layout:header2");
    }
    public function footer(){
        $this->display("Widget/Layout:footer");
    }
}
