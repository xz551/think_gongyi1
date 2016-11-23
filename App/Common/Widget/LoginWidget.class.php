<?php
/**
 * 迷你登录框
 */
namespace Common\Widget; 
use Think\Controller;
class LoginWidget extends Controller{
    public function showLogin(){
    	layout(false);
        $this->display(T("Common@Widget/Login:showLogin"));
    }
    
}
