<?php
namespace Uc\Controller;
use Think\Controller;
use Lib\User;
class ResumesController extends Controller{
	//加载志愿履历及时长页面
	public function index($uid){
		if(User::isUser($uid)){
			$num=D("T/ProjectRecruitDetail")->getUserTimeNum($uid);
			$this->title = User::getNickname().' 的主页 | 用户中心';
			$this->assign("num",$num);
			$this->assign("uid",$uid);
			$this->display("index");
		}else{
			//查看的页面不存在
			$this->title = ' 系统消息 | 用户中心';
			$this->error('查看的页面不存在');
		}
	}
	//服务时长分页
	public function ajaxGetUserTime($uid,$p=1,$pageSize=10){
		$getTimeInfo=$this->getTimeInfo($uid,$p,$pageSize);
		$count=D('T/ProjectRecruitDetail')->getUserTimeCount($uid);
		$this->assign("timeInfo",$getTimeInfo);
		$this->assign("page",ajax_page($count,$pageSize));
		$this->display("ajaxGetUserTime");
	}
	//当前用户的时长记录
	public function getTimeInfo($uid,$p,$pageSize){
		//项目名称 组织名称 所获时长 获取时间
		$result = D('T/ProjectRecruitDetail')->getUserTimeInfo($uid,$p,$pageSize);
		if($result){
			foreach($result as $k=>$v){
				$result[$k]['allot_time'] = date("Y-m-d H:i:s",$v['allot_time']);
				$result[$k]['orgname'] = D('T/Organization')->getOrgName($v['creator']);
			}
		}
		return $result;
	}
}