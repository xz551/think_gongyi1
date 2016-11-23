<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/24
 * Time: 9:20
 */

namespace M\Controller;


use M\Business\RegBusiness;
use M\Business\UserBusiness;
use M\Session\MUserSession;
use Think\Controller;
use Think\Verify;
use Org\Util\String;

class RegController extends Controller
{

    public function regiset(){
        $this->title='账号注册';
        MUserSession::setRegPhone(null);
        $this->display('regiset');
    }

    /**进入校验手机号 、短信验证码的界面
     * @param $code
     * @param $phone
     */
    public function vcode($ckcode,$telphone){
        MUserSession::setRegPhone(null);
        if (!preg_match('/^1\d{10}$/', $telphone)) {
            echo "-1 手机号错误";
            eixt;
        }
        $this->title='账号注册';
        $verify = new Verify();
        $verify->reset=true;
        $v=$verify->check($ckcode);
        if($v){
            $v= RegBusiness::verify_phone($telphone);
            if($v['status']==-1){
                $this->redirect('Reg/regiset','',5,'手机号已经存在');
                exit;
            }
            $this->phone1=substr($telphone,0,3);
            $this->phone2=substr($telphone,3,4);
            $this->phone3=substr($telphone,7);
            $smscode=String::randString(10);
            session(SmsController::sms_code_verify. $telphone,$smscode);
            $this->smscode=$smscode;
            $this->display('vcode');
        }else{
            $this->redirect('Reg/regiset');
        }
    }
    private function verify_phone(){
        $phone=MUserSession::getRegPhone();
        if(empty($phone)){
            $this->redirect('Reg/regiset');
        }
        $v=RegBusiness::verify_phone($phone);
        if($v['status']==-1){
            $this->redirect('Reg/regiset','',5,'手机号已经存在');
            exit;
        }
    }
    public function setpwd(){
        $this->title='账号注册';
        $this->verify_phone();
        $this->display();
    }

    /**
     * 执行注册
     * 填写 手机号 昵称 密码
     *
     */
    public function regdo($upwd,$uname){
        $this->verify_phone();
        if(!preg_match('/^.{6,15}$/', $upwd)){
            $this->error('密码长度为6到15位');
        }
        if (!verify_name($uname)) {
            $this->error('昵称必须2到10位合法字符');
        }
        //执行注册
        $upwd=md5($upwd);
        $result=D('User')->where('phone!=%d',MUserSession::getRegPhone())->add(
            array(
                'phone'=>MUserSession::getRegPhone(),
                'phone_status'=>1,
                'password'=>$upwd,
                'type'=>10,
                'nickname'=>$uname,
                'create_date'=>time(),
                'update_date'=>time()
            )
        );
        if($result){
            //成功
            if(MUserSession::getLoginType()==MUserSession::LOGIN_TYPE_OAUTH
            ||
            MUserSession::getLoginType()==MUserSession::LOGIN_TYPE_WEIXIN){
                //绑定
                //验证帐号是否绑定过
                $user=D('User')->find(MUserSession::getUserId());
                if(empty($user['password'])){
                    //执行绑定
                    UserBusiness::bind($result,MUserSession::getLoginType());
                }
                //登陆替换
                MUserSession::login($result);
                //跳转
                redirect(MUserSession::getReturnUrl());
            }
        }else{
            //失败
            $this->error('注册失败，稍后重试');
        }
    }

}