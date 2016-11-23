<?php
namespace UC\Model;
use Think\Model;
class EvaluateModel extends Model{
	//添加评价
	public function addEvaluate($uid,$pid,$level,$content){
		$map['userID'] = $uid;
		$map['proID'] = $pid;
		$map['level'] = $level;
		$map['content'] = $content;
		$map['create_time'] = time();
		return $this->add($map);
	}
	 
	//查看用户是否评价过
	public function checkR($uid,$pid){
		$map['proID'] = $pid;
		$map['userID'] = $uid;
		return $this->where($map)->count();
	}
}
