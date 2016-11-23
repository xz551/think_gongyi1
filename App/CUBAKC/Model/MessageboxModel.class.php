<?php
namespace UC\Model;
use Think\Model;
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
}