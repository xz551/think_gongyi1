<?php
namespace Uc\Controller;
use Think\Controller;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
class PictureWallController extends Controller{
	public function index(){
        	tag('user_login');
		$uid=UserSession::getUser('uid');
			$this->title = User::getNickname($uid).' 的主页 | 用户中心';
			$count=D("M/Discuse")->allActPicCount($uid);
			$this->assign("count",$count);
			$this->assign("uid",$uid);
			$this->display("index");
	}
	public function ajaxGetPicture($uid,$p=1,$pageSize=18){
		$allActivePic=D('M/Discuse')->allActivePic($uid,$p,$pageSize);
		$count=D("M/Discuse")->allActPicCount($uid);
		$page=ajax_page($count,$pageSize);
		foreach ($allActivePic as $key=>$val){
			$allActivePic[$key]['image']=Image::getUrl($val['image'], array(318,0),'active');
			$allActivePic[$key]['date']=date("Y-m-d",$val['create_date']);
		}
		$this->ajaxReturn(array('pic'=>$allActivePic,'page'=>$page));
	} 
}