<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace M\Widget;
use Lib\Image;
use M\Session\MUserSession;

/**
 * Description of HeaderWidget
 *
* @author Administrator
*/
class LayoutWidget extends WController{

    public function footer(){
        layout(false);
        $this->display("Widget/Layout:footer");
    }
    public function header(){
        layout(false);
        $this->display("Widget/Layout:header");
    }
    public function nav(){
        layout(false);
        if(!MUserSession::islogin()){
            //未登录
            $this->display("Widget/Layout:nav");
        }else{
            //已经登录
            $user=MUserSession::getUser();
            $user->gender=$user->gender==1?'男': ($user->gender==2?'女':'未知');
            $user_photo=Image::getUrl($user->photo,array(120),$user->gender==2?'user_girl':'user');
            $user->photo=$user_photo;
            $this->user=$user;
            $this->display("Widget/Layout:nav_user");
        }

    }
    public function tag(){
        layout(false);
        $this->label=D('CategoryServer')->getAllLabel();
        $this->display("Widget/Layout:tag");
    }
    public function province(){
        layout(false);
        $this->city=D('ProvinceCity')->getChildrenCity();
        $this->display("Widget/Layout:province");
    }

}
