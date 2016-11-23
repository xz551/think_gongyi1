<?php
namespace Uc\Controller;
use Think\Controller;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
use T\Common\Api\CityApi as CityApi;
class VolGroupController extends Controller{
	//当前用户所在组织信息
	public function index($uid){
		$count = D('T/OrganizationUser')->getOrgNum($uid);
		if($count){
			$this->title = User::getNickname().' 的主页 | 用户中心';
			$this->assign("uid",$uid);
			$this->display("index");
		}else{
			//所查看的页面不存在
			$this->title = ' 系统消息 | 用户中心';
			$this->error('查看的页面不存在');
		}
		
	}
	public function ajaxGetVolGroup($uid,$p=1,$pagenum=20){
		//判断用户是否登陆
		if(UserSession::getUser()){
			//判断是访问的自己的账户还是别人的账户
			if(UserSession::getUser('uid') == $uid){
				$isMine = 1;    //访问的自己的账户
			}else{
				$isMine = 0;    //访问的别人的账户
			}	
		}else{
			$isMine=2;
		}
		$this->assign('isMine',$isMine);
        $group = D('T/OrganizationUser')->getOrgInfo($p,$pagenum,$uid);   //获取用户所在组织的信息
        $count = D('T/OrganizationUser')->getOrgNum($uid);                  //获取用户参加了多少个组织
		$city = new CityApi();
		if(group){
			$group[0]['count']=$count;
			foreach($group as $k=>$v){
				$group[$k]['photo'] =Image::getUrlThumbCenter($v['photo'], array(84));//  UploadedFile::getFileUrl($v['photo'], array(100, 75),'user');
				$group[$k]['provinceid'] =$city->getName($v['provinceid']);
				$group[$k]['cityid'] = $city->getName($v['cityid']);
			}
		}
		$this->assign("group",$group);
		$this->assign("page",ajax_page($count,$pagenum));
		$this->display("ajaxGetVolGroup");
	}
	//返回收信人的信息
	public function getUser($toid){
		$toName=json_decode(api("/User/getById",array('uid'=>$toid)));
		if($toName){
			$this->ajaxReturn(array('name'=>$toName->nickname,'uid'=>$toName->uid));
		}
	}
	//发送私信
	public function message($content,$toid){
		tag('db_begin');
		//判断一下$toid是否存在
		$isCount=D("User")->checkUser($toid);
		 if($isCount){
			$user=UserSession::getUser();
			if($user){
				//判断一下$toid和$from_uid是否相等
				if($toid != $user['uid']){
					$from_user=json_decode(api("/User/getById",array('uid'=>$toid)));
					$data['avatarUrl']=Image::getUrl($user['photo'], array(50));
					$data['username']=$from_user->nickname;
					$data['content']=$content;
					$data=json_encode($data);
					$r=D("Messagebox")->addMessage($toid,$user['uid'],$data);
					$this->ajaxReturn($r);
				}else{
					echo 0;
				}	
			}else{
				echo -2;
			}
			
		}else{
				echo -1;
		}
		
	}
}