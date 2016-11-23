<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/10/17
 * Time: 15:07
 */
namespace Admin\Behaviors;

use Home\Model\UserModel;
use Think\Behavior;
use Lib\UserSession;

class UserBehavior extends Behavior {

    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @param mixed $params 行为参数
     * @return void
     */
    public function run(&$params)
    {
        //["UC_1fee2e4c643b61fe99dae6b4f7779abd__id"] => string(1) "2"
        //UCENTERKEY
        if(!session(UCENTERKEY.'__id')){
            redirect("/site/login.html");exit;
        }

    }
}