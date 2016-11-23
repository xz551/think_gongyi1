<?php
namespace T\Widget;
use Lib\Image;
use Lib\UserSession;
use Lib\User;
use T\Common\Api\emsApi;
class DonateWidget extends WController {
	public function left_top(){

		$this->display("Widget/Donate:left_top");
	}
	public function main_top(){
		
		$this->display("Widget/Donate:main_top");
	}
    /**
     * 详情页里面的求助功能
     */
    public function main_donate(){
    	$id=I('id');
    	//求助信息
    	$concur=D("Concur")->getConcurById($id);
    	$this->assign("concur",$concur);
    	$this->display('Widget/Donate:main_donate');
    }
    /**
     * 详情页里面的物资求助功能里的地址
     */
    public function address(){
    	$this->display('Widget/Donate:address');
    }
    /**
     * 详情页里面的物资求助功能
     */
    public function sup(){
    	$id=I('id');
    	//求助信息
    	$concur=D("Concur")->getConcurById($id);
    	//判断是否是发起人自浏览
    	if($concur['creator']==UserSession::getUser('uid')){
    		$this->assign("tag",1);
    	}else{
    		$this->assign("tag",0);
    	}
    	//判断是否有物资求助
    	if($concur['is_supplies']){
    		//捐助人信息
    		$w=array(
    				'concur_id'=>$id,
    				'user_id'=>UserSession::getUser('uid'),
    				'status'=>1
    		);
    		$supplies=M("ConcurSuppliesApply")->where($w)->find();
    		//物流详情
    		if($supplies['content']){
    			$logistics_number=explode(",",$supplies['logistics_number']);
    			$content=json_decode($supplies['content']);
    			$data="";
    			//foreach ($content->data as $k=>$v){
    			//$data .="<li>".$v->time." ".$v->context."</li>";
    			//}
    			for($i=count($content->data)-1;$i>=0;$i--){
    				$data .="<li>".$content->data[$i]->time." ".$content->data[$i]->context."</li>";
    			}
    			$content->data=$data;
    			$supplies['content']=$content;
    			if($content->data){
    				if(time()-$supplies['update_time']>3600){
    					if($content->status !=3){
    						$ems=ems($logistics_number[1],$logistics_number[0]);
    						//更新物流信息
    						D("ConcurSuppliesApply")->updateLogistics($supplies['user_id'],$id,$ems);
    					}
    				}
    			}
    			//物流单号
    			$supplies['logistics_number']=$logistics_number[0];
    			//物流公司名称
    			$supplies['expTextName']=$logistics_number[2];
    		}
    		//发起人的邮寄地址
    		$address=D("ConcurSuppliesAddress")->getAddress($id,$concur['creator']);
    		$ems=emsApi::getEms();
    		$this->assign('ems',$ems);
    		$this->assign("address",$address);
    		$this->assign("supplies",$supplies);
    		$this->display('Widget/Donate:sup');
    	}
    }
    /**
     * 详情页里面的讨论功能
     */
    public function reply(){
    	$id=I('id');
    	//讨论区功能
    	//判断用户有没有登录，如果没有登录则显示返回地址
    	if(UserSession::getUser()){
    		$this->assign('islogin',1);
    		//判断用户是否是认证用户
    		$isVip=User::isVip(UserSession::getUser('uid'));
    		$this->assign("isVip",$isVip);
    	}else{
    		$this->assign('islogin',0);
    		$reurl =  urlSafeBase64_encode(SERVER_VISIT_URL.U('t/Concurinfo/index',array('id'=>$id)).'#discuss');
    		$reurl2 =  urlSafeBase64_encode(SERVER_VISIT_URL.U('t/Concurinfo/index',array('id'=>$id)));
    		$this->assign('login',UCENTER.'/user/login.html?returnurl='.$reurl);
    		$this->assign('login2',UCENTER.'/user/login.html?returnurl='.$reurl2);
    	}
    	
    	$default = (UserSession::getUser('gender')==2)?'user_girl':'user';
    	$userPhoto =Image::getUrl(UserSession::getUser('photo'),array(60,60),$default);
    	$this->assign('userPhoto',$userPhoto);
    	$this->assign('uid',UserSession::getUser('uid'));
    	$this->assign('sid',$id);
    	$this->display("Widget/Donate:reply");
    }
 
}