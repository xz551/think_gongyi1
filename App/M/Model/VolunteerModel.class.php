<?php
namespace M\Model;
use Think\Model;
class VolunteerModel extends Model{
	protected $connection =  'USER_CONTER'; //数据库连接
	public function volunteer($uid){
		return $this->where("uid=%d",$uid)->find();
	}
}
