<?php
namespace M\Controller;
use Think\Controller;
use Common\Api\CityApi;
class DonateController extends Controller{
	public function index($id){
		//获取指定求助信息
		$donate=D("Project")->donate($id);
		$this->title=$donate['name'];
		if($donate['status']==2||$donate['status']==3||$donate['status']==4){
			$donate['address']=CityApi::getName($donate['provinceid'])."-".CityApi::getName($donate['cityid'])."-".$donate['address'];
			//发起方信息
			$user=json_decode(api("/User/getById",array('uid'=>$donate['creator'])));
			//求助发起方地址
			$address=CityApi::getName($user->provinceid)."-".CityApi::getName($user->cityid);
			$initiator=array(
				'nickname'=>$user->nickname,
				'address'=>$address,
				'email'=>$user->email
			);
			//发起方是志愿者
			if($user->type==11){
				$volunteer=D("Volunteer")->volunteer($donate['creator']);
				$initiator['contact']=$volunteer['real_name'];
				$initiator['phone']=$volunteer['phone'];
			}else{
				//发起方是组织
				$organization=D("Organization")->organization($donate['creator']);
				$initiator['contact']=$organization['contact'];
				$initiator['phone']=$organization['phone'];
				$initiator['summary']=$organization['summary'];
			}
			$status=D("Project")->getStatus();
			
			$this->assign("donate",$donate);
			$this->assign("initiator",$initiator);
			$this->assign("status",$status);
            $this->assign('pc_url',YI_JUAN. '/raisegoods/view/id/'.$id.'.html');
			$this->display("index",array("id"=>$id));
		}else{
			//查看的物资求助不存在或还没有通过审核(统一一个提示页面)
			 redirect(U('/m/Tpl/Index','msg='.urlSafeBase64_encode('查看的物资求助不存在或还没有通过审核')));
		}
	}
}