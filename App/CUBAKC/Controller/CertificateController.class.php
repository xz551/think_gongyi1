<?php
namespace UC\Controller;
use Think\Controller;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
class CertificateController extends Controller{
	public function index($uid){
		if(User::isUser($uid) && user::getUser()->uid == UserSession::getUser('uid')){
				$w=array('uid'=>$uid,'status'=>1);
				$cartificateUser=M("CartificateUser")->where($w)->select();
				if($cartificateUser){
					foreach ($cartificateUser as $k=>$v){
						$cartificateUser[$k]['cart']=Image::getUrl($v['path'], array(0));
						$cartificateUser[$k]['cart1']=Image::getUrl($v['path'], array(0,198));
					}
				}
				$this->title = User::getNickname().' 的主页 | 用户中心';
				$this->assign("uid",$uid);
				$this->assign("cartificateUser",$cartificateUser);
				$this->display("index");
		}else{
			//跳转到提示页面
			$this->title = ' 系统消息 | 用户中心';
			$this->error('查看的页面不存在');
		}
		
	}
}
