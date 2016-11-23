<?php
namespace T\Controller;
use Think\Controller;
class OpenvController extends Controller{
	public function index($id=1){

		$rs=D("CourseVideo")->saveView($id);
		$video=D("CourseVideo")->video($id);
		$video['view']=pv($video['view']);
		$video_type_id=$video["video_type_id"];
		$otherVideo=D("CourseVideo")->otherVideo($id,$video_type_id);
        $this->title="益视频-".$video['video_name'];
		$this->assign("video",$video);
		$this->assign("otherVideo",$otherVideo);
		$this->display();
	}
	
}