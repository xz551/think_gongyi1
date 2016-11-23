<?php
namespace UC\Controller;
use Think\Controller;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
class ProjectController extends Controller{
	//项目页面
	public function index($uid){
		if(User::getUser($uid)){
			$this->title = User::getNickname().' 的主页 | 用户中心';
			$this->assign("uid",$uid);
			$this->display("index");
		}else{
			//跳转到提示页面(查看的用户不存在)
			$this->title = ' 系统消息 | 用户中心';
			$this->error('查看的用户不存在');
		}
	}
	 //获取用户参加、发起的||组织发起的相关项目的详细信息
	public function ajaxGetProject($uid,$p=1,$pageSize=10){
		$pro=D('T/Project')->getPro($uid,$p,$pageSize);
		if($pro){
			foreach ($pro as $key=>$val){
				$user=json_decode(api("/User/getById",array('uid'=>$val['creator'])));
				$userPhoto=Image::getUrlThumbCenter($user->photo, array(62),$user->gender==2?'user_girl':'user');// UploadedFile::getFileUrl($user['photo'], array(60, 60),'user');
				$pro[$key]['nickname']=$user->nickname;
				$pro[$key]['userPhoto']=$userPhoto;
				$pro[$key]['show_image']=Image::getUrlThumbCenter($val['show_image'], array(120),'project');//UploadedFile::getFileUrl($val['show_image'], array(120, 120),'project');
				$pro[$key]['create_date']=nowTime(strtotime($val['create_date']));//date('Y年m月d日 H:i',strtotime($val['create_date']));
				$pro[$key]['userId']=UserSession::getUser('uid');
			}
		}
		if(User::getType($uid) == 21){
			$count=D("T/Project")->getOrgProCount($uid);
		}else{
			$count=D("T/Project")->getProCount($uid);
		}
		$this->assign("pro",$pro);
		$this->assign("page",ajax_page($count,$pageSize));
		$this->display("ajaxGetProject");
	}
}