<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/9/15
 * Time: 09:38
 */

namespace T\Controller;


use Lib\User;
use Lib\UserSession;
use Think\Controller;

class AuthorajaxController extends Controller
{
    public function _initialize()
    {
        tag('user_login');
        layout(false);
    }

    private function return_error($msg = '')
    {
        $this->ajaxReturn(array('status' => -1, 'msg' => $msg));
        exit;
    }

    private function return_success($msg = '')
    {
        $this->ajaxReturn(array('status' => 1, 'msg' => $msg));
        exit;
    }

    /**
     * 检测是否存在登录名信息
     */
    public function is_exsis(){
        if(User::exists_login(UserSession::getUserId())){
            $this->return_success('存在用户登录信息');
        }else{
            $this->return_error('不存在用户登陆信息');
        }
    }

}