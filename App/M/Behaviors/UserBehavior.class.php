<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/10/17
 * Time: 15:07
 */

namespace M\Behaviors;

use M\Session\MUserSession;
use Think\Behavior;


class UserBehavior extends Behavior
{
    /**
     * 执行行为 run方法是Behavior唯一的接口
     *
     * @access public
     * @param mixed $params
     *            行为参数
     * @return void
     */
    public function run(&$params)
    {
        $user =MUserSession::islogin();
        if(!$user){
			$url = urlSafeBase64_encode(M_SITE . '/' . $_SERVER['REQUEST_URI']);
			$login_url='/m/user/login.html?returnurl='.$url;
        	if (IS_AJAX) {
        		echo  '{"login":"-1","login_url":"'.$login_url.'"}';
        	}else{
        		if (session('ref') == 'uc') {
        			session('ref', null);
					redirect($login_url);
        			//登陆后进入的系统 反复登陆操作 执行退出
        		} else {
        			//跳转到登录界面
        			//记录调整方式
        			session('ref', 'uc');
        			redirect($login_url);
        		}
        	}
        	exit();
        }
        
    }

}
