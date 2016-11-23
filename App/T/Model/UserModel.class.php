<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;
class UserModel extends Model
{
    protected $connection =  'USER_CONTER'; //数据库连接
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
   
   //检测当前登录用户是否为认证用户
   public function checkAuth(){
        //检测是否为资料完善的用户或组织
        $_u = array(
            'uid'    => UserSession::getUser('uid'),
            'status' => array('neq',-1),
            'type'   => array('in','11,21'),
        ); 
        $user = $this->where($_u)->find();
        if(!$user) return 0;    
        $_w = array(
                 'uid' => UserSession::getUser('uid'),
                 'status'=>1,
        );
        //检测是否为认证用户
        if($user['type'] == 11){ 
            return D('Volunteer')->where($_w)->count();
        //检测是否为认证组织
        }elseif($user['type'] == 21){
            return D('Organization')->where($_w)->count();
        }else{
            return 0;
	}
   }
   
   
   //检查当前登录用户是否为认证组织用户
    public function checkIsOrg(){
        $map['uid'] = UserSession::getUser('uid');
        $map['type'] = 21;
        $map['status'] = 1;
        return $this->where($map)->count();
    }
    
    //检测用户及密码是否正确
    public function checkUserPwd($map){
        return $this->where($map)->count();
    }
    //根据用户id获取用户资料
    public function getUser($uid){
    	return $this->where("uid=%d",$uid)->find();
    }
    
}