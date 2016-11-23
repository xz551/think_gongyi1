<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/2/10
 * Time: 13:24
 */

namespace Common\Model;

use Think\Model;
use Lib\UserSession;
class UserModel extends Model {
    protected $connection =  'USER_CONTER'; //数据库连接
    /**
     * 用户状态常量
     * 0：用户注册成功，但是邮箱未验证
     * 1：用户邮箱验证成功
     * -1：用户有已经被屏蔽
     */
    const USER_STATUS_UNEMAIL = 0;
    const USER_STATUS_ENEMAIL = 1;
    const USER_STATUS_DISABLE = -1;
    
    const USER_PHONE_STATUS_NOT_VERIFIED = 0;
    const USER_PHONE_STATUS_VERIFIED = 1;
    
    /**
     * 用户类型常量
     * 个人账户  10 普通用户  11 认证用户
     * 组织账户
     * 20 未填写资料的组织
     * 21 填写了资料的组织
     */
    const USER_TYPE_NORMAL = 10;
    const USER_TYPE_VOLUNTEER = 11;
    const USER_TYPE_UNORG = 20;
    const USER_TYPE_ORG = 21;
    protected $_validate = array(
    		/* 验证手机号码 */
    		array('phone','verify_phone','手机格式不正确',2,'function'),
    		array('phone','','该手机号已经被注册',2,'unique'),
    			
    		/* 验证邮箱 */
    		array('email','email', '邮箱格式不正确',2), //邮箱格式不正确
    		array('email','', '该邮箱已经被注册',2,'unique'), //邮箱被占用
    			
    		/* 验证用户名*/
    		array('nickname','2,24','请输入2到24个字符',0,'length'),//用户名长度
    		array('nickname','verify_name','用户名仅支持中英文、数字、下划线或减号',0,'function'),//用户名仅支持中英文、数字、下划线或减号
    		/* 验证密码 */
    		array('password','6,24','密码太短(最小值为6个字符串)',0,'length'), //
    		// 验证确认密码是否和密码一致
    		array('repwd','password','确认密码跟密码不一致',0,'confirm'),
    );
 //获取用户资料
    public function getUserInfo($id){
        $m['uid'] = array('IN',$id);
        return $this->where($m)->select();
    }
   
   //检测用户是否存在
   public function checkUser($uid){
   	$m['uid'] = $uid;
	return $this->where($m)->count();
   }
   
   /**
    * 检测用户是否为认证用户
    * 非认证用户返回1，认证用户返回2， 认证组织返回1
    */
   public function checkAuth($uid=0){
        //检测是否为资料完善的用户或组织
       if(!$uid){
           $uid = UserSession::getUser('uid');
       }
        $_u = array(
            'uid'    => $uid,
            'status' => array('neq',-1),
            'type'   => array('in','11,21'),
        ); 
        $user = $this->where($_u)->find();
        if(!$user) return 0;    
        $_w = array(
                 'uid' => $uid,
                 'status'=>1,
        );
        //检测是否为认证用户
        if($user['type'] == 11){ 
            if(D('T/Volunteer')->where($_w)->count()) return 2;
            else return 0;
        //检测是否为认证组织
        }elseif($user['type'] == 21){
            if(D('T/Organization')->where($_w)->count()) return 1;
            else return 0;
        }else{
            return 0;
	}
   }
       
    //检测用户及密码是否正确
    public function checkUserPwd($map){
        return $this->where($map)->count();
    }
    //根据用户id获取用户资料
    public function getUser($uid){
    	return $this->where("uid=%d",$uid)->find();
    }
    
    
    /**
     * 判断用户邮箱是否被注册
     */
    public function checkEmail($email){
    	return $this->where('email="%s"',$email)->find();
    
    }
    /**
     * 判断用户手机号是否被注册
     */
    public function checkPhone($phone){
    	return $this->where('phone="%s"',$phone)->find();
    
    }
    /**
     * 判断用户昵称是否被使用
     */
    public function checkName($nickname){
    	return $this->where('nickname="%s"',$nickname)->count();
    
    }
    /**
     * 用户登录认证
     * @param string $usrename 用户名
     * @param string $password 用户密码
     * @param integer $type 用户名类型(1-邮箱，2-手机)
     * return integer 登录成功-用户ID,登录失败-错误编号
     */
    public function login($username,$password,$type=1){
    	$map = array();
    	switch($type){
    		case 1:
    			$map['email'] = $username;
    			break;
    		case 2:
    			$map['phone'] = $username;
    			break;
    		default:
    			return 0;    //参数错误
    	}
    	$u = $this->where($map)->count();
    	if($u){
    		$map['password'] = md5($password);
    		/* 获取用户数据 */
    		$user = $this->where($map)->find();
    		if(is_array($user) && ($user['status'] != -1)){
    			return $user['uid'];
    		}else{
    			return -1;   //用户不存在或密码错误
    		}
    	}else{
    		return 0;//账号不存在
    	}
    	
    }
}