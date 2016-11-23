<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/9/10
 * Time: 14:58
 */

namespace Lib;
use M\Business\RegBusiness;
use M\Session\MUserSession;
use Org\Util\String;
use Think\Log;

/**发送短信封装的类
 * Class Sms
 * @package Lib
 */
class Sms
{

    /**
     * 短信验证安全码 存储标记前缀
     */
    const sms_code_verify = 'sms_code_verify';
    /**
     * 短信验证码 存储标记前缀
     */
    const sms_coce_send = 'sms_coce_send';
    /**
     * 短信验证码 发送时间 存储标记前缀
     */
    const sms_code_send_time = 'sms_code_send_time';

    /**
     * 发送手机验证码
     * @param $phone
     * @param $smscode
     * @param int $type
     */
    public static function sendsms($phone, $smscode)
    {
	
	

        $verify_result=self::_data_verify($phone, $smscode, 1234);
        if(is_array($verify_result)){
            return $verify_result;
        }
        //上次发送验证码的时间
        $send_time_key = self::sms_code_send_time . $phone;
        $send_time = S($send_time_key);
        //与当前时间比较60内不能重复发送
        if (!empty($send_time)) {
            $time_c = time() - intval($send_time);
            if ($time_c < 59) {
                return array('status' => -4, 'msg' => (60 - $time_c) . '秒后再操作');

            }
        }
        //保存发送时间
        S($send_time_key, time());
        //获取已经发送的验证码
        $key = self::sms_coce_send . $phone;
        $code = String::randString(4, 1);
        $sresult=S($key, $code,600);
        Log::write('缓存结果：'.json_encode($sresult));

        $msg = "【中青公益】您的验证码是" . $code . "，在10分钟之内有效。如非本人操作请忽略此条短信。";
        Log::write($phone . ':' . $msg);
	$result =sms($phone, $msg);
	Log::write('验证码：'.$msg);
        return array('status' => 1, 'msg' => '发送成功', 'sms' => $result);
    }

    /**验证码手机验证码
     * @param $phone 手机号
     * @param $smscode 安全验证码
     * @param $code 手机验证码
     */
    public  static function verify($phone, $smscode, $code)
    {
        \Lib\UserSession::set_phone_login_verify_status(null);
        $verify_result=self::_data_verify($phone, $smscode, $code);
        if(is_array($verify_result)){
            return $verify_result;
        }
        //验证发送过来的短信验证码是否正确

        $key = self::sms_coce_send . $phone;
        $code_session = S($key);
        if (empty($code_session)) {

            return  (array('status' => -3, 'msg' => '短信验证码失效，请重新获取'));
        }
        if ($code_session != $code) {
            return (array('status' => -2, 'msg' => '短信验证码错误，重新输入'));
        }
        $vpresult = RegBusiness::verify_phone($phone);
        if ($vpresult['status'] == 1) {
            //验证码失效
            session($key, null);
            MUserSession::setRegPhone($phone);
        } else {
            //存在手机号
            \Lib\UserSession::set_phone_login_verify_status($phone);
        }
        return ($vpresult);
    }

    /**数据验证
     * @param $phone
     * @param $smscode
     */
    private  static function _data_verify($phone, $smscode, $code)
    {
        if (!verify_phone($phone)) {
            return (array('status' => -4, 'msg' => '手机号错误'));
        }
        if ($smscode !== 'join_project' && $smscode !=='login') {
            /**
             * 发送短信的安全码
             */
            $sms_code_verify = S(self::sms_code_verify . $phone);
            if (empty($sms_code_verify)) {
                //return (array('status' => -2, 'msg' => '验证失效，请刷新重新操作'));

            }
            /**
             * 发送短信的安全码不正确
             */
            if ($sms_code_verify != $smscode) {
                //return (array('status' => -3, 'msg' => '验证失效，请刷新重新操作'));

            }
        }else if($smscode=='login'){
        	//发送登录手机验证码
        	//判断是否已经登录，已经登录就不发送了
        	if(UserSession::islogin()){
        		return (array('status' => -6, 'msg' => '已经登录'));
        	}else{
        		$vpresult = RegBusiness::verify_phone($phone);
        		if($vpresult['status']==1){
        			return array('status' => -7, 'msg' => '手机号不存在',);
        		}else{
        			return true;
        		}
        	}
        	
        }else {
            tag('m_user_login');
        }
        if (!empty($code)) {
            if (!preg_match('/^\d{4}$/', $code)) {
                return (array('status' => -5, 'msg' => '短信验证码错误'));
            }
        }
        return true;
    }

    /**获取发送短信的安全token 即发送短信的smscode 安全码
     * @param $phone 手机号
     * @param $type 获取类型 0：手机号不存在时获取，1：手机号存在是获取
     */
    public static function get_token($phone,$type=0){
        $v=RegBusiness::verify_phone($phone);
        if(($type==1 && $v['status']==-1 || ($type==0 && $v['status']==1))){
            $smscode = String::randString(10);
            S(self::sms_code_verify . $phone, $smscode);
            $v['smscode']=$smscode;
        }
        return $v;
    }
}