<?php
namespace Uc\Controller;
use Think\Controller;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
class DonateController extends Controller{
	//求助页面
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
	//获取用户参加的相关求助信息
	public function ajaxGetDonate($uid,$p=1,$pageSize=10){
		$donate=D("T/Project")->getDon($uid,$p,$pageSize);
		if($donate){
			foreach ($donate as $key=>$val){
				$user=json_decode(api("/User/getById",array('uid'=>$val['creator'])));
				$userPhoto=Image::getUrlThumbCenter($user->photo, array(60),$user->gender==2?'user_girl':'user');// UploadedFile::getFileUrl($user['photo'], array(60, 60),'user');
				$donate[$key]['nickname']=$user->nickname;
				$donate[$key]['userPhoto']=$userPhoto;
				$donate[$key]['create_date']=nowTime(strtotime($val['create_date']));//date('Y年m月d日 H:i',strtotime($val['create_date']));
				$donate[$key]['userId']=UserSession::getUser('uid');
				$donate[$key]['utype']=User::getType();//User::getUser($uid)->type;
			}
		}
		$count=D("T/Project")->getDonCount($uid);
		$this->assign("donate",$donate);
		$this->assign("page",ajax_page($count,$pageSize));
		$this->display("ajaxGetDonate");
	}
}