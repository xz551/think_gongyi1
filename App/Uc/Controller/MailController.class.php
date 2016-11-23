<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/9/9
 * Time: 16:14
 */

namespace Uc\Controller;

use Lib\UserSession;
use Org\Util\CMail;
use Org\Util\String;
use Think\Controller;

class MailController extends Controller
{
    /**
     * 发送验证存存储键
     */
    const cach_code_key='mail_session_code_key';

    const code_send_time='code_send_time';

    /**发送邮箱验证码
     * @param $mail 邮箱
     */
    public function sendcode($mail,$tag=0){
        if(!check_email($mail)){
            $this->return_error('邮箱错误');
        }
        //验证邮箱是否存在
        $user=D("User")->checkEmail($mail);
        if(!$tag){
        	if($user){
        		$this->return_error('邮箱已经存在');
        	}
        }
        //发送验证码
        $send_time_key=self::code_send_time.$mail;
        $send_time=S($send_time_key);
        $c_time=time()- (empty($send_time)?0:intval($send_time));
        if($c_time<60){
            $this->return_error((60-$c_time).'秒后重试');
        }

        $code_key=self::cach_code_key.$mail;

        $code=String::randString(6,1);
        S($code_key,$code,6000);
        CMail::send($mail,CMail::send_code_to_mail,array('code'=>$code,'nickname'=>$user['nickname']));
        $this->return_success('发送成功');
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

    /**验证邮箱
     * @param $mail 邮箱
     */
    public function verify($mail,$code,$t=0,$a=0){
        $code_key=self::cach_code_key.$mail;
        $cache_code=S($code_key);
        if($code===$cache_code){
            if($a==1){
                S($code_key,null);
            }
            if($t==0){
                echo '';
            }else{
                return '';
            }
        }else{
            if($t==0){
                echo '';
            }else{
                return '验证码错误';
            }
        }
    }
}