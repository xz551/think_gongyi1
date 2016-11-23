<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/19
 * Time: 15:10
 */

namespace M\Controller;


use Lib\UserSession;
use M\Business\UserBusiness;
use M\Session\MUserSession;
use Think\Controller;

class AuthController extends Controller
{
    public function index(){
        if(isset($_GET['openid'])){
            //微信登录返回
            $uid=UserBusiness::getUidByOpenId($_GET['openid']);
            MUserSession::setLoginType(MUserSession::LOGIN_TYPE_WEIXIN);
        }else{
            $uid=UserSession::getUserId();
            MUserSession::setLoginType(MUserSession::LOGIN_TYPE_OAUTH);
        }
        if($uid){
            MUserSession::login($uid);
            $this->redirect('sel_bind');

        }else{
            $this->error('系统错误','/m');
        }
    }

    /**
     * 选择绑定
     */
    public function sel_bind(){
        //检测用户是否绑定了帐号
        $user=D('T/User')->find(MUserSession::getUserId());
        if(empty($user['password'])){
            //网站登录密码为空 尚没有绑定帐号 跳转绑定
            $this->url=MUserSession::getReturnUrl();
            $this->display('sel_bind');
            exit;
        }
        redirect(MUserSession::getReturnUrl());
        //跳转
    }

    /**
     * 绑定已经有的帐号
     * 登录已经有的帐号 登陆绑定
     */
    public function login(){
        $this->url=MUserSession::getReturnUrl();
        $this->display('login');
    }
    /**
     * 执行帐号绑定
     */
    public function bind($uid){
        $type=MUserSession::getLoginType();
        if($type==MUserSession::LOGIN_TYPE_GONGYI){
            $this->return_error('禁止操作');
        }
        //验证帐号是否绑定过
        $user=D('T/User')->find(MUserSession::getUserId());
        if(!empty($user['password'])){
            $this->return_error('您已经绑定过，不要重复操作');
        }
        //检查传递过来的帐号，是否绑定过别的帐号
        //检查登录方式
        $type=MUserSession::getLoginType();
        $bind_result='';
        if($type==MUserSession::LOGIN_TYPE_OAUTH){
            //第三方 微博 人人 帐号登陆
            $oauth=D('T/UserOath2')->where('uid=%d and status=0 ',$uid)->find();
            if($oauth){
                $this->return_error('该帐号已经绑定过别的授权帐号');
            }else{
                //执行绑定
                $bind_result=UserBusiness::bind($uid,$type);
            }
        }else if($type==MUserSession::LOGIN_TYPE_WEIXIN){
            //微信登录
            $WxUser=D('WxUser')->where('userid=%d',$uid)->find();
            if($WxUser){
                $this->return_error('该帐号已经绑定过别的授权帐号');
            }else{
                //执行绑定
                $bind_result=UserBusiness::bind($uid,$type);
            }
        }else{
            $this->return_error('禁止操作');
        }
        if(is_string($bind_result)){
            $this->return_error($bind_result);
        }else{
            //新的id登陆
            MUserSession::login($uid);
            $this->return_success('绑定成功');
        }

    }
    private function return_error($msg){
        $this->ajaxReturn(array('status'=>-1,'msg'=>$msg));
        exit;
    }
    private function return_success($msg){
        $this->ajaxReturn(array('status'=>1,'msg'=>$msg));
        exit;
    }
}