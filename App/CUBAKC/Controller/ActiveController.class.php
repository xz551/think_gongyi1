<?php
namespace UC\Controller;
use Think\Controller;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
class ActiveController extends  Controller{
	//活动页面
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
	//获取用户参加||组织发起的相关活动信息
	public function ajaxGetActive($uid,$p=1,$pageSize=10){
		$act=D("T/Active")->getActive($uid,$p,$pageSize);
		if($act){
			foreach ($act as $key=>$val){
				//发起方的信息
				$user=json_decode(api("/User/getById",array('uid'=>$val['uid'])));
				$userPhoto=Image::getUrlThumbCenter($user->photo, array(62),$user->gender==2?'user_girl':'user');// UploadedFile::getFileUrl($user['photo'], array(60, 60),'user');
				$act[$key]['nickname']=$user->nickname;
				$act[$key]['userPhoto']=$userPhoto;
				//活动信息
				$act[$key]['image']=Image::getUrlThumbCenter($val['image'], array(120),'active');// UploadedFile::getFileUrl($val['image'], array(120, 120),'active');
				$act[$key]['create_date']=nowTime($val['create_date']);//date('Y年m月d日 H:i',$val['create_date']);
				//登陆用户的uid
				$act[$key]['userId']=UserSession::getUser('uid');
				//被浏览用户的type
				$act[$key]['type']=User::getType();//User::getUser($uid)->type;
			}
		}
		if(User::getType() == 21){
			$w=array('uid'=>$uid);
			if($uid != UserSession::getUser('uid')){
				$w['status']=1;
			}
			$count=M("Active")->where($w)->count();
		}else{
			$count=D('T/Active')->actCount($uid);
		}
		$this->assign("act",$act);
		$this->assign("page",ajax_page($count,$pageSize));
		$this->display("ajaxGetActive");
	}
}