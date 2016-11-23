<?php
namespace Uc\Model;
use Think\Model;
use Org\Util\CMail;
class MessageboxModel extends Model{
	protected $connection = 'USER_CONTER'; //数据库连接
	public function addMessage($toid,$from_uid,$data,$status=0){
		$map['from_uid']=$from_uid;
		$map['to_uid']=$toid;
		$map['data']=$data;
		$map['status']=$status;
		$map['create_date']=time();
		$r=$this->add($map);
		if($r){
			$map['from_uid']=$toid;
			$map['to_uid']=$from_uid;
			$map['status']=2;
			return $this->add($map);
		}
	}
	//邮箱注册成功，发送邮件
	public function sendMsg($uid,$template,$arr=array()){
		//return;
		$_data_fun='_data_'.$template;
		$user=D('User')->find($uid);
		if(!$user){
			return false;
		}
		$arr=$this->$_data_fun($user,$arr);
		CMail::send($user['email'],$template,$arr);
	}
	//
	public function _data_activeemail($user,$arr){
		$activeKey = strlen($user['uid']).$user['uid'].md5(strlen($user['uid']).$user['uid'].$user['status'].$user['email']).md5(uniqid(mt_rand(), true));
		$url=UCENTER.'/user/activeEmail/code/'.$activeKey;
		return array(
			'name'=>$user['nickname'],
			'url'=>$url	
		);
	}
}