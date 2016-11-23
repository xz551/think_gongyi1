<?php
namespace M\Model;
use Think\Model;
class ProjectRecruitDetailModel extends Model{
	const STATUS_JOIN = 100;   //进入项目参与
	const STATUS_CACLED = 101; //取消项目参与
	const STATUS_SNAP_PASS = 200;  //临时选定入选名单
	const STATUS_PASS = 201; //入选
	const STATUS_ARRIVE = 203; // 到场参加
	const STATUS_IMPORT=300;//项目导入参加

	public function getJobUserCount($project_id,$job_id){
		$w=array(
			'project_id'=>$project_id,
			'job_id'=>$job_id,
			'status'=>array('neq',101)		
		);
		return $this->where($w)->count();
	}
	public function getUserCount($project_id){
		$w=array(
				'project_id'=>$project_id,
				'status'=>array('neq',101)
		);
		return $this->where($w)->count();
	}
	public function ruxuanCount($project_id){
		$w=array(
				'project_id'=>$project_id,
				'status'=>array('eq',201)
		);
		return $this->where($w)->count();
	}
	public function proJoin($project_id){
		$w=array(
				'project_id'=>$project_id,
				'status'=>array('neq',101)
		);
		return $this->where($w)->select();
	}
}