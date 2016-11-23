<?php 
namespace Uc\Controller;
use Think\Controller;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
class ConcurController extends Controller{
	/**
	 * 我发起的求助||提供的资源页面
	 * @creator zdf
	 */
	public function index($uid,$type=0){
		if(User::getUser($uid)){
			$this->title = User::getNickname().' 的主页 | 用户中心';
			$this->assign("uid",$uid);
			$this->assign("type",$type);
			$this->display("index");
		}else{
			//跳转到提示页面(查看的用户不存在)
			$this->title = ' 系统消息 | 用户中心';
			$this->error('查看的用户不存在');
		}
	}
	/**
	 * ajax获取我发起的求助||提供的资源信息
	 * @creator zhu 
	 */
	public function ajaxGetConcur($uid,$type,$tag=0,$p=1,$pageSize=10){
		$concurInfo=D("Concur")->getConcur($uid,$type,$tag,$num=0,$p,$pageSize);
		$count=D("Concur")->getConcur($uid,$type,$tag,$num=1);
		if($concurInfo){
			$user=json_decode(api("/User/getById",array('uid'=>$uid)));
			foreach ($concurInfo as $k=>$v){
				$concurInfo[$k]['nickname']=$user->nickname;
				$default = ($user->gender==2)?'user_girl':'user';
				$concurInfo[$k]['photo']=Image::getUrlThumbCenter($user->photo,array(62),$default);
				$concurInfo[$k]['image']=Image::getUrlThumbCenter($v['image'],array(252,192),'concur');
				$concurInfo[$k]['create_date']=nowTime($v['create_date']);
			}
		}
		$this->assign("tag",$tag);
		$this->assign("type",$type);
		$this->assign("uid",$uid);
		$this->assign("userId",UserSession::getUserId());
		$this->assign('concurInfo',$concurInfo);
		$this->assign('page',ajax_page(count($count),$pageSize));
		if($type){
				$this->display("ajaxResource");
		}else{
			$this->display("ajaxDonate");
		}
	}
	/**
	 * 我捐助的求助||我申请的资源页面
	 */
	public function donate($uid,$type=0){
		if(User::getUser($uid)){
			$this->title = User::getNickname().' 的主页 | 用户中心';
			$this->assign("uid",$uid);
			$this->assign("type",$type);
			$this->display("donate");
		}else{
			//跳转到提示页面(查看的用户不存在)
			$this->title = ' 系统消息 | 用户中心';
			$this->error('查看的用户不存在');
		}
	}
	/**
	 * ajax返回我捐助的求助||我申请的资源信息
	 * @param unknown $uid
	 * @param unknown $type
	 * @param number $tag
	 * @param number $p
	 * @param number $pageSize
	 */
	public function ajaxGetConcur1($uid,$type,$tag=0,$p=1,$pageSize=10){
		$donateInfo=D("ConcurApply")->donate($uid,$type,$tag,$count=0,$p,$pageSize);
		
		if($donateInfo){
			$donCount=D("ConcurApply")->donate($uid,$type,$tag,$count=1);
			foreach ($donateInfo as $key=>$val){
			 	$donateInfo[$key]['image']=Image::getUrlThumbCenter($val['image'],array(252,192),'concur');
				//发起人的信息
				$user=json_decode(api("/User/getById",array('uid'=>$val['creator'])));
				$donateInfo[$key]['nickname']=$user->nickname;
				$default = ($user->gender==2)?'user_girl':'user';
				$donateInfo[$key]['photo']=Image::getUrlThumbCenter($user->photo,array(62),$default);
				$donateInfo[$key]['datetime']=date("Y年m月d日 H:i",strtotime($val['datetime']));
				if($val['updatetime']){
					$donateInfo[$key]['updatetime']=date("Y年m月d日 H:i",strtotime($val['updatetime']));
				}
				if($val['service']){
					$result=D("T/ConcurServiceApply")->isService($val['id'],$val['userid'],-1,$status=-2);
					if($result){
						//捐服务
						$result['datetime']=nowTime($result['datetime']);
						$result['updatetime']=nowTime($result['updatetime']);
					}
					$donateInfo[$key]['fuwu']=$result;
				}
				if($val['money']){
					//捐钱
				}
				if($val['supplies']){
					//捐物资
					$res=D("T/ConcurSuppliesApply")->isSupplies($val['id'],$val['userid'],-1,$status=-2);
				 	$r1=D("T/ConcurSuppliesApply")->getAlreadySupplies($val['id'],$val['userid'],$status=-2);
					$a="";
				 	if($res){
						foreach ($r1 as $k=>$value){
							$a .=$value['num']." x ".$value['name']."、";
						}
						$res['info']=rtrim($a,"、");
						$res['date_time']=nowTime($res['date_time']);
						$res['update_time']=nowTime($res['update_time']);
					}  
					$donateInfo[$key]['wuzi']=$res;
				} 
			}
		}
		$this->assign("type",$type);
		$this->assign("uid",$uid);
		$this->assign("userId",UserSession::getUserId());
		$this->assign("donateInfo",$donateInfo);
		$page=ajax_page(count($donCount),$pageSize);
		$this->assign("page",$page);
		$this->assign("tag",$tag);
		if($type){
			//申请的资源
			$this->display("ajaxResource1");
		}else{
			//捐助的捐助
			$this->display("ajaxDonate1");
		}
	}
	/**
	 * 我认证的真实求助
	 */
	public function approve($uid){
		if(User::getUser($uid)){
			$this->title = User::getNickname().' 的主页 | 用户中心';
			$this->assign("uid",$uid);
			$this->display("approve");
		}else{
			//跳转到提示页面(查看的用户不存在)
			$this->title = ' 系统消息 | 用户中心';
			$this->error('查看的用户不存在');
		}
	}
	/**
	 * ajax返回我认证的真实求助信息
	 */
	public function ajaxApprove($uid,$tag=0,$p=1,$pageSize=10){
		//$uid认证的求助信息
		$identifycation=D("ConcurVerify")->identification($uid,$tag,$p,$pageSize);
		//$uid认证的求助的总数
		$identCount=D("ConcurVerify")->identification($uid,$tag,$p,$pageSize,$Condition=1);
		$identCount=count($identCount);
		if($identifycation){
			foreach ($identifycation as $k=>$v){
				$user=json_decode(api("/User/getById",array('uid'=>$v['creator'])));
				$identifycation[$k]['nickname']=$user->nickname;
				$default = ($user->gender==2)?'user_girl':'user';
				$identifycation[$k]['photo']=Image::getUrlThumbCenter($user->photo,array(62),$default);
				$identifycation[$k]['image']=Image::getUrlThumbCenter($v['image'],array(252,192),'concur');
			}
		}
		$this->assign("tag",$tag);
		$this->assign("uid",$uid);
		$this->assign("userId",UserSession::getUserId());
		$this->assign('identifycation',$identifycation);
		$this->assign('page',ajax_page($identCount,$pageSize));
		$this->display("ajaxApprove");
	}
}