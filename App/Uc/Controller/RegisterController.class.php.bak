<?php 
namespace Uc\Controller;
use Think\Controller;
use Lib\UserSession;
use Common\Model\UserModel;
use M\Controller\CodeController;

class RegisterController extends Controller{
	//注册首页
	public function index(){
		layout(false);
		$this->title="注册 | 用户中心";
		$this->display("index");
	}
	//个人用户注册
	public function userRegister(){
		layout(false);
		$this->title="用户注册 | 用户中心";
		$this->assign("type",0);
		$this->display("user_register");
	}
	//组织用户注册
	public function orgRegister(){
		layout(false);
		$this->title="组织注册 | 用户中心";
		$this->assign("type",1);
		$this->display("org_register");
	}
	/**
	 * 用户邮箱注册提交的数据
	 * @param unknown $nickname 用户昵称
	 * @param unknown $password 密码
	 * @param unknown $type     0代表个人用户注册 1代表组织注册
	 * @param unknown $email    注册邮箱
	 * @param unknown $ip_yzcode验证码
	 */
	public function form_email_register($nickname,$password,$type,$email,$ip_yzcode,$returnurl){
		tag('db_begin');
		$result=CodeController::check_verify($ip_yzcode);
		if(!$result){
			$this->error("验证码错误");
		}
		$user=D("User");
		$data=$user->create();
		if($data){
			if($data['email']){
				$data['password']=md5($password);
				$data['create_date']=time();
				$data['update_date']=time();
			}else{
				$this->error($user->getError());
			}
			$data['type']=$type?UserModel::USER_TYPE_UNORG:UserModel::USER_TYPE_NORMAL;
			$uid=$user->add($data);
			if($uid){
				if($type){
					D("Organization")->addOrg($uid,$nickname);
				}
				UserSession::login($uid);
				D("Messagebox")->sendMsg($uid,"activeemail");
				$this->redirect('success',array('phone_email'=>urlSafeBase64_encode($email),'type'=>$type,'tag'=>1,'returnurl'=>$returnurl));//注册成功提示页
			}else{
				$this->error("注册失败");
			}
		} else {
			$this->error($user->getError());
        }
	}
	/**
	 * 用户手机注册提交的数据
	 * @param unknown $nickname 用户昵称
	 * @param unknown $password 密码
	 * @param unknown $phone    注册手机
	 */
	public function form_phone_register($nickname,$password,$phone,$returnurl){
		tag('db_begin');
		$user=D("User");
		$data=$user->create();
		if($data){
			if($data['phone']){
				$data['password']=md5($password);
				$data['type']=UserModel::USER_TYPE_NORMAL;
				$data['create_date']=time();
				$data['update_date']=time();
				$data['phone_status']=UserModel::USER_PHONE_STATUS_VERIFIED;
			}else{
				$this->error($user->getError());
			}
			$uid=$user->add($data);
			if($uid){
				UserSession::login($uid);
				$this->redirect('success',array('phone_email'=>urlSafeBase64_encode($phone),'tag'=>0,'returnurl'=>$returnurl));//注册成功提示页
			}else{
				$this->error("注册失败");
			}
		}else{
			$this->error($user->getError());
		}
	}
	/**
	 * 个人注册条款
	 */
	public function user_clause(){
		layout(false);
		$this->title="个人注册条款 | 用户中心";
		$this->display("user_tiaokuan");
	} 	
	/**
	 * 组织注册条款
	 */
	public function org_clause(){
		layout(false);
		$this->title="组织注册条款 | 用户中心";
		$this->display("org_tiaokuan");
	}
	/**
	 * ajax检测验证码是否正确
	 */
	public function ckVerify(){
		$code = I('ip_yzcode');
		$verify = new \Think\Verify();
		$verify->reset=false;
		if($verify->check($code)){
			echo '{"ok":""}';
		}else{
			echo '{"error":"验证码错误"}';
		}
	}
	/**
	 * ajax检测邮箱是否被注册
	 */
	public function ckEmail($email){
		$r=D("User")->checkEmail($email);
		if($r){
			echo '{"error":"该邮箱已经被注册"}';
		}else{
			echo '{"ok":""}';
		}
	}
	/**
	 * ajax检测电话是否被注册
	 */
	public function ckPhone($phone){
		$r=D("User")->checkPhone($phone);
		if($r){
			echo '{"error":"该手机号已经被注册"}';
		}else{
			echo '{"ok":""}';
		}
	}
	/**
	 * ajax检测昵称是否被使用
	 */
	public function ckName($nickname,$type){
		$r=D("User")->checkName($nickname);
		if($r){
			if($type){
				echo '{"error":"组织名称被占用"}';
			}else{
				echo '{"error":"该昵称已经被使用"}';
			}
		}else{
			echo '{"ok":""}';
		}
	}
	//注册成功页面
	public function success(){
		layout ( false );
		$this->title="注册成功 | 用户中心";
		$phone_email=I('phone_email');
		$phone_email=urlSafeBase64_decode($phone_email);
		$type=I('type');
		$tag=I('tag');
		$returnurl=I('returnurl');
		if(check_email($phone_email)){
			$emailDomain = explode('@', $phone_email);
			$email='http://mail.'.$emailDomain['1'];
			$this->assign('email',$email);
		}
		$returnurl=urlSafeBase64_decode($returnurl);
		$returnurl=$returnurl?$returnurl:'/uc/'.UserSession::getUser('uid');
		$url = urlSafeBase64_encode(YIJUAN_VISIT_URL . '/uc');
		$this->assign("phone_email",$phone_email);
		$this->assign("type",$type);
		$this->assign("tag",$tag);
		$this->assign('uid',UserSession::getUserId());
		$this->assign("returnurl",$returnurl);
		$this->assign('url',$url);
		$this->display("success");
	}
}