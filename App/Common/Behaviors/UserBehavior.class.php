<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/10/17
 * Time: 15:07
 */

namespace Common\Behaviors;

use T\Model\OrganizationModel;
use Common\Model\UserModel;
use Think\Behavior;
use Lib\UserSession;

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
        $user = UserSession::getUser();
        if(!$user){        	
        	if (IS_AJAX) {
        		echo  '{"login":-1}';
        	}else{
        		$url = urlSafeBase64_encode('http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        		
        		if (session('ref') == 'uc') {
        			session('ref', null);
        			//登陆后进入的系统 反复登陆操作 执行退出
        			redirect(UCENTER . '/user/logout.html?returnurl=' . $url);
        		} else {
        			//跳转到登录界面
        			$rerul = UCENTER . '/user/login.html?returnurl=' . $url;
        			//记录调整方式
        			session('ref', 'uc');
        			redirect($rerul);
        		}
        	}
        	exit();
        }
        
    }

}
