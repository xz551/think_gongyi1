<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/21
 * Time: 14:53
 */

namespace M\Controller;


use Lib\Sms;
use M\Business\RegBusiness;
use M\Session\MUserSession;
use Session\UserSession;
use Think\Controller;
use Org\Util\String;
use Think\Log;

/**
 * 手机短信操作类
 * Class SmsController
 * @package M\Controller
 */
class SmsController extends Controller
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
    public function sendsms($phone,$smscode){
        $result=Sms::sendsms($phone,$smscode);
        $this->ajaxReturn($result);
    }

    /**验证码手机验证码
     * @param $phone 手机号
     * @param $smscode 安全验证码
     * @param $code 手机验证码
     */
    public function verify($phone,$smscode,$code){
        $result=Sms::verify($phone,$smscode,$code);
        $this->ajaxReturn($result);
    }

    /**
     * @param $phone
     * @param $type
     */
    public function token($phone,$type=0){
        $result=Sms::get_token($phone,$type);
        $this->ajaxReturn($result);
    }
}