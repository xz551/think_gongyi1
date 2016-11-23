<?php 
namespace Uc\Controller;
use Think\Controller;
use Lib\UserSession;
use Lib\User;
use M\Controller\SmsController;
use Lib\Sms;
use M\Controller\CodeController;
use Uc\Business\WxBusiness;

class LoginController extends Controller{
	//登陆页面
	public function index(){
		layout(false);
        if(IS_POST){
        	$smscode=I("smscode");
        	$code=I("code");
        	$returnurl=I("returnurl");
        	$remember=I('remember');
        	if($smscode && $code){
        		//手机通过短信验证码登陆提交过来的数据
        		$phone=UserSession::get_phone_login_verify_status();
        		if(empty($phone)){
        			$this->error('手机号码不能为空');
        		}
        		UserSession::set_phone_login_verify_status(null);
        		$sms=Sms::verify($phone, $smscode, $code);
        		if($sms['status']== 1){
        			$this->error("登陆失败");
        		}
        		$user=D("User")->checkPhone($phone);
        		if($user['status'] !=-1){
        			if(!$user['phone_status']){
        				$data['phone_status']=1;
        				D("User")->where("uid=%d",$user['uid'])->save($data);
        			}
        			$time=$remember?(3600*24*7):0;
        			UserSession::login($user['uid'],$time);
        			$returnurl=$returnurl?urlSafeBase64_decode($returnurl):SERVER_VISIT_URL;
        			redirect($returnurl);
        		}else{
        			$this->error('用户已被屏蔽');
        		}
        	}
        }else{
        	$returnurl=$_GET['returnurl'];
        	$returnurl=$returnurl?urlSafeBase64_decode($returnurl):SERVER_VISIT_URL;
        	$this->title="登录 | 用户中心";
          	UserSession::isLogin()?redirect($returnurl):$this->display("index");
        }
	}
	/**
	 *普通登陆 提交过来的数据
	 **/
	public function login($LoginForm_username,$LoginForm_password,$remember,$returnurl=''){
		if(check_email($LoginForm_username)){
			$type=1;
		}elseif(verify_phone($LoginForm_username)){
			$type=2;
		}
		$uid = D("User")->login($LoginForm_username,$LoginForm_password,$type);
		if($uid>0){
			$time=$remember?(3600*24*7):0;
        	UserSession::login($uid,$time);
			$returnurl=urlSafeBase64_decode($returnurl);
			$returnurl=$returnurl?$returnurl:SERVER_VISIT_URL;
			redirect($returnurl);
		}else{
			$this->error("登陆失败");
		}
	}
	/**
	 * 普通登陆的前台验证
	 * @param unknown $username 邮箱/手机号码
	 * @param unknown $password 密码
	 */
	public function elogin($username,$password){
		if(check_email($username)){
			$type=1;
		}elseif(verify_phone($username)){
			$type=2;
		}
		$uid = D("User")->login($username,$password,$type);
		if($uid==-1){
			$this->ajaxReturn(array('status'=>-1,'msg'=>'用户名或密码错误'));
		}elseif($uid==0){
			$this->ajaxReturn(array('status'=>0,'msg'=>'账号不存在'));
		}else{
			$this->ajaxReturn(array('status'=>1));
		}
	}
	/**
	 * 找回密码第一步页面
	 */
	public function findPassword1(){
		if(!UserSession::getUser()){
			layout(false);
			$this->title="找回密码1 | 用户中心";
			$this->assign('tag',1);
			$this->display("find_pwd1");
		}else{
			redirect(SERVER_VISIT_URL);
		}
	}
	/**
	 * ajax检测邮箱/电话/身份证是否被注册
	 * @param unknown $ip_account 邮箱/电话/身份证
	 */
	public function check($ip_account){
		if(check_email($ip_account)){
			$r=D("User")->checkEmail($ip_account);
			if($r){
				echo '{"ok":""}';
			}else{
				echo '{"error":"该邮箱未注册"}';
			}
		}elseif(verify_phone($ip_account)){
			$r=D("User")->checkPhone($ip_account);
			if($r){
				echo '{"ok":""}';
			}else{
				echo '{"error":"该手机号未注册"}';
			}
		}elseif(check_card($ip_account)){
			$r=D("Volunteer")->checkCard($ip_account);
			if($r){
				echo '{"ok":""}';
			}else{
				echo '{"error":"该身份证未被注册使用"}';
			}
		}
	}
	/**
	 * 找回密码第二步页面
	 */
	public function findPassword2(){
		if(!UserSession::getUser()){
			layout(false);
			$this->assign('tag',2);
			$this->title="找回密码2 | 用户中心";
			if(IS_POST){
				$ip_account=I("ip_account");
				$ip_yzcode=I('ip_yzcode');
				if($ip_account && $ip_yzcode){
					$r1=D("User")->checkEmail($ip_account);
					$r2=D("User")->checkPhone($ip_account);
					$r3=D("Volunteer")->checkCard($ip_account);
					$result=CodeController::check_verify($ip_yzcode);
					if(($r1 || $r2 || $r3) && $result){
						$user=$r1?$r1:($r2?$r2:$r3);
						$uid=$user['uid'];
						$user=D("User")->getUser($uid);
						$this->assign("user",$user);
						session('fid',$uid);
						if($r2 || ($r3 && $user['phone'])){
							$this->display("find_phone_pwd2");
						}
						if($r1 || ($r3 && $user['email'])){
							$this->display("find_email_pwd2");
						}
					}else{
						$this->redirect('/Uc/Login/findPassword1');
					}
				}
			}else{
				$uid=session('fid');
				if($uid){
					$tag=$_GET['tag']?$_GET['tag']:0;
					$user=D("User")->getUser($uid);
					$this->assign("user",$user);
					if($tag && $user['email']){
						$this->display("find_email_pwd2");
					}
					if($user['phone']){
						$this->display("find_phone_pwd2");
					}
					if($user['email']){
						$this->display("find_email_pwd2");
					}
				}else{
					$this->redirect('/Uc/Login/findPassword1');
				}
			}
		}else{
			redirect(SERVER_VISIT_URL);
		}
	}
	/**
	 * 验证验证码
	 * @param unknown $email 邮箱
	 * @param unknown $ecode 邮箱验证码	
	 */
	public function ckCode($email,$ecode){
		if(!check_email($email)){
			$this->ajaxReturn(array('status' => -1, 'msg' => '邮箱错误'));
		}
		//验证邮箱是否存在
		$user=D('User')->where("email='%s'",$email)->find();
		if(!$user){
			$this->ajaxReturn(array('status' => -2, 'msg' => '邮箱不存在'));
		}
		$result=MailController::verify($email,$ecode,1,0);
		if(!empty($result)){
			$this->ajaxReturn(array('status' => -3, 'msg' => '邮箱验证码错误'));
		}else{
			$this->ajaxReturn(array('status' => 1));
		}
	}
	/**
	 * 找回密码第三步:修改密码页面
	 */
	public function findPassword3(){
		if(!UserSession::getUser()){
			layout(false);
			$this->title="找回密码3 | 用户中心";
			$this->assign('tag',3);
			if(IS_POST){
				$phone=I("phone");
				$email=I("email");
				$smscode=I("smscode");
				$code=I("code");
				$this->assign('token',time());
				if($phone && $code && $smscode){
					if(session('smscode') == $smscode){
						$this->redirect('/Uc/Login/findPassword2');
					}
					UserSession::set_phone_login_verify_status(null);
					$sms=Sms::verify($phone, $smscode, $code);
					if($sms['status']==-1){
						session('smscode',$smscode);
						$this->display("find_pwd3");
					}else{
						$this->error($sms['msg']);
					}
				}
				if($email && $code){
					if(!check_email($email)){
						$this->error('邮箱错误');
					}
					//验证邮箱是否存在
					$count=D('User')->where("email='%s'",$email)->count();
					if(!$count){
						$this->error('邮箱不存在');
					}
					$result=MailController::verify($email,$code,1,1);
					if(!empty($result)){
						$this->redirect('/Uc/Login/findPassword2');
					}else{
						$this->display("find_pwd3");
					}
				}
			}else{
				$this->redirect('/Uc/Login/findPassword1');
			}	
		}else{
			redirect(SERVER_VISIT_URL);
		}
	}
	const wx_login_code_session='wx_bind_code_session';
	/**
	 * 获取微信登陆的二维码
	 */
	public function get_login_weixin_qrcode(){
		layout(false);
		//检测用户是否已经登录
		if(UserSession::islogin()){
			$this->ajaxReturn(array('status'=>-1,'msg'=>'您已经登录'));
		}
		//获取登录code
		$code=WxBusiness::bind_code();
		if(!$code){
			$this->ajaxReturn(array('status'=>-1,'msg'=>'获取标识失败'));
		}
		//通过微信接口 获取微信参数二维码
		$result=api(WX_API_URL.'/qrcode/login/code/'.$code);
		session(self::wx_login_code_session,$code);
		echo $result;
		exit;
	}
	/**
	 * 检查是否已经绑定了
	 */
	public function check_wx_bind(){
		$code=session(self::wx_login_code_session);
		$wx_login=D('WxLogin')->where(array('login_code'=>$code,'status'=>1))->find();
		if(empty($wx_login['uid'])){
			$this->ajaxReturn(array('status'=>-1));
		}else{
			//将状态修改成-1
			D('WxLogin')->save(array(
				'status'=>-1,
				'id'=>$wx_login['id']
			));
			$uid=$wx_login['uid'];
			UserSession::login($uid);
			WxBusiness::clear_code();
			$this->ajaxReturn(array('status'=>1));
		}
	}

	/**
	 * 找回密码第四步：重置密码成功页面
	 */
	public function findPassword4(){
		if(!UserSession::getUser()){
			layout(false);
			$this->title="找回密码4 | 用户中心";
			$this->assign('tag',4);
			if(IS_POST){
				tag('db_begin');
				$password=I('password');
				$repwd=I('repwd');
				$token=I('token');
				if($password && $repwd && $token){
					if(session('token') == $token){
						$this->error("表单已过期，请重新输入","/Uc/Login/findPassword2");
					}else{
						$user=D("User");
						$data=$user->create();
						if($data){
							$map['password']=md5($password);
							$map['update_date']=time();
							$uid=session('fid');
							$r=$user->where('uid=%d',$uid)->save($map);
							if($r){
								session('token',$token);
								$this->display('find_pwd4');
							}else{
								$this->error("重置密码失败，请稍后再试");
							}
						}else{
							$this->error($user->getError());
						}
					}
				
				}
			}else{
				$this->redirect('/Uc/Login/findPassword1');
			}	
		}else{
			redirect(SERVER_VISIT_URL);
		}
	}
}