<?php
namespace T\Controller;
use Think\Controller;

class UserController extends Controller{
    public function checkUser(){
        tag('db_begin');
        //验证用户名密码是否正确
        if(strpos(I('name'),'@')){
            $map['email'] = I('name');
        }else{
            $map['phone'] = I('name');
        }
        $map['password'] = md5(I('pwd'));
        echo D('User')->checkUserPwd($map);
    }
}