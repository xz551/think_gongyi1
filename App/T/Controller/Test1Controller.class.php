<?php 
namespace T\Controller;
use Think\Controller;
use Lib\Image;
use Lib\UserSession;
use T\Common\Api\emsApi;
class Test1Controller extends Controller{
	public function index(){
		$id=I('id');
		//求助信息
		$concur=M("Concur")->where("id=%d",$id)->find();
		$concur['image']=Image::getUrl($concur['image'],array(354,199));
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
							$ems=ems($content->expSpellName,$content->mailNo);
							//更新物流信息
							D("ConcurSuppliesApply")->updateLogistics($supplies['user_id'],$id,$ems);
						}
					}
				}
			}
			//发起人的邮寄地址
			$address=D("ConcurSuppliesAddress")->getAddress($id,$concur['creator']);
			$ems=emsApi::getEms();
			$this->assign('ems',$ems);
			$this->assign("address",$address);
			$this->assign("supplies",$supplies);
		}else{
			$this->assign('is_supplies','false');
		}
		//讨论区功能
		$default = (UserSession::getUser('gender')==2)?'user_girl':'user';
		$userPhoto =Image::getUrl(UserSession::getUser('photo'),array(60,60),$default);
		$this->assign('userPhoto',$userPhoto);
		$this->assign('uid',UserSession::getUser('uid'));
		$this->assign('sid',$id);
		$this->assign("concur",$concur);
		dump($concur);
		//爱心动态功能
		$this->display("index");
	}
	//ajax爱心动态
	public function love($id,$p=1,$pageSize=8,$tag=-1,$status=-2){
		
		//捐助名单
		$donName=D("ConcurServiceApply")->donName($id,$p,$pageSize,$tag);
		foreach ($donName as $k=>$v){
			//判断一下服务表是否有记录
			$r=D("ConcurServiceApply")->isService($id,$v['apply_uid'],$v['time']);
			//判断一下物资表中是否有记录
			$res=D("ConcurSuppliesApply")->isSupplies($id,$v['apply_uid'],$v['time']);
			//判断一下$id是求助项目还是捐助项目
			$concur=D("Concur")->getConcurById($id);
			if($concur['type']){
				if($r){
					$donName[$k]['service']="希望申请服务";
				}
				if($res){
					//申请人想要捐助/救助的物资
					$result=D("ConcurSuppliesApply")->getAlreadySupplies($id,$v['apply_uid'],$status);
					$a="希望申请";
					if($result){
						foreach ($result as $key=>$val){
							$a .=$val['num']." x ".$val['name']."、";
						}
						$donName[$k]['supplies']=rtrim($a,"、");
					}
				}	
			}else{
				if($r){
					$donName[$k]['service']="希望提供服务";
				}
				if($res){
					//申请人想要捐助/救助的物资
					$result=D("ConcurSuppliesApply")->getAlreadySupplies($id,$v['apply_uid'],$status);
					$a="希望捐助";
					if($result){
						foreach ($result as $key=>$val){
							$a .=$val['num']." x ".$val['name']."、";
						}
						$donName[$k]['supplies']=rtrim($a,"、");
					}
				}	
			}
			//捐助人的信息
			$user=json_decode(api("/User/getById",array('uid'=>$v['apply_uid'])));
			$province=M("ProvinceCity")->where(array("id"=>$user->provinceid))->find();
			$city=M("ProvinceCity")->where(array("id"=>$user->cityid))->find();
			$donName[$k]['userAddress']=$province['class_name'].' '.$city['class_name'];
			$donName[$k]['image']=Image::getUrlThumbCenter($user->photo,array(0),$user->gender==2?'user_girl':'user');
			$donName[$k]['nickname']=$user->nickname;
			$donName[$k]['time']=time_day($v['time']);
		}
		$this->assign("donName",$donName);
		$this->display("love");
	}
	/**
	 * 分享话题
	 */
	public function shareSub(){
		layout(false);
		//查看用户是否绑定 1.qq  2.weibo  3.renren
		$list = D('UserOauth2')->getBindingType();
		$this->assign('list',$list);
		//mb_strlen($str,'UTF8');
		//当前地址
		$sid = I('sid');
		$url = SERVER_VISIT_URL.U('t/Test1/index',array('id'=>$sid));
		$content = "“益”起来，更精彩！我在中青公益中发现这个话题 “".I('name')."” 很不错哦，你也来参加吧！@中青公益 聚合青年公益力量。".$url;
		$this->assign('num',140-mb_strlen($content,'UTF8'));
		$this->assign('content',$content);
		$this->display("Subject:shareSub");
	}
}
?>