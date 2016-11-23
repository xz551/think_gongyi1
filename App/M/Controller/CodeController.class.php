<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/20
 * Time: 17:12
 */

namespace M\Controller;


use M\Business\RegBusiness;
use M\Controller\SmsController;
use Org\Util\String;
use Think\Controller;
use Think\Verify;

class CodeController extends Controller
{
    /**
     * 生成验证码
     */
    public function index($reset=0){
        $verify=new Verify();
        $verify->useCurve=false;
        $verify->useNoise=false;
        $verify->imageH=30;
        $verify->imageW=90;
        $verify->fontSize=12;
        $verify->reset=$reset;
        $verify->fontttf='5.ttf';
        $verify->codeSet='123456789';
        $verify->length=4;
        $verify->entry();
    }

    /**
 * 验证输入的验证码
 * @param $code
 * @param $phone
 */
    public function verify($code,$phone){
        if (!preg_match('/^1\d{10}$/', $phone)) {
            echo "-1 手机号错误";
            eixt;
        }
        $verify = new Verify();
        $verify->reset=false;
        $v=$verify->check($code);
        if($v){
            $v=RegBusiness::verify_phone($phone);
            if($v['status']==1) {
                $smscode = String::randString(10);
                session(SmsController::sms_code_verify . $phone, $smscode);
            }
            $this->ajaxReturn($v);

        }else{
            $this->ajaxReturn(array('status'=>-1,'msg'=>'验证码错误'));
        }
    }
    /**
     * 后台验证输入的验证码
     */
    public function check_verify($ip_yzcode){
    	$verify = new Verify();
    	$v=$verify->check($ip_yzcode);
    	if($v){
    		$verify->reset=true;
    		return true;
    	}else{
    		return false;
    	}
    }
    /**
     * 验证手机号
     * @param unknown $phone 手机号
     * @param number $tag	0代表注册 1代表登陆
     */
    public function phone_verify($phone,$tag=0){
    	$v=RegBusiness::verify_phone($phone);
    	if(($tag && $v['status']==-1 || (!$tag && $v['status']==1))){
    		$smscode = String::randString(10);
    		session(SmsController::sms_code_verify . $phone, $smscode);
    		$v['smscode']=$smscode;
    	}
    	$this->ajaxReturn($v);
    }

}