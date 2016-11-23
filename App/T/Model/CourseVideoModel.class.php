<?php
namespace T\Model;
use Think\Model;

class CourseVideoModel extends Model{
	public function allVideo($pageSize=12,$p=1,$id=1){
		$w=array("status"=>1);
		if($id != 1){
			$w['video_type_id']=$id;
		}
		$count=$this->where($w)->count();
		$page=page($count,$pageSize);
		$video=$this->where($w)->page($p . ',' . $pageSize)->order("update_time desc")->select();
		return array('video'=>$video,'page'=>$page);
	}
	public function otherVideo($id,$video_type_id){
		$w=array("status"=>1);
		$h=array("status"=>1);
		$w['id'] =array("neq",$id);
		$w['video_type_id']=$video_type_id;
		$otherVideo=$this->where($w)->limit(4)->order("view desc")->select();
		$count=count($otherVideo);
		if($count < 4){
			$h['video_type_id']=array("neq",$video_type_id);
			$videos=$this->where($h)->limit(4-$count)->order("view desc")->select();
			foreach ($videos as $val){
				$otherVideo[]=$val;
			}
		}
			return $otherVideo;
		
	}
	//获取每一个视频
	public function Video($id){
		$w=array("status"=>1,"id"=>$id);
		return $this->where($w)->find();
	}
	
	public function saveview($id){
		$w=array("status"=>1,"id"=>$id);
		return $this->where($w)->setInc('view',1);
	}
}