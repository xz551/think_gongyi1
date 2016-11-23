<?php
namespace M\Model;
use Think\Model;
class OrganizationModel extends Model{
	protected $connection =  'USER_CONTER'; //数据库连接
	public function organization($uid){
		return $this->where("uid=%d",$uid)->find();
	}
}