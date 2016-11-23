<?php
namespace T\Controller;
use Think\Controller;
use Lib\Image;
use Lib\UserSession;
use Lib\User;
use T\Common\Api\emsApi;
class DonateController extends Controller{
	//求助列表页
	public function index($p=1,$pageSize=15,$status=0,$type=0,$label=0,$pro=0,$tag=0){
		$concurInfo=D("Concur")->getConcurInfo($p,$pageSize,$status,$type,$label,$pro,$tag);
		$count=D("Concur")->getConNum($status,$type,$label,$pro,$tag);
		if($concurInfo){
			foreach ($concurInfo as $k=>$v){
				$user=json_decode(api("/User/getById",array('uid'=>$v['creator'])));
				$concurInfo[$k]['nickname']=$user->nickname;
				$concurInfo[$k]['image']=Image::getUrlThumbCenter($v['image'],array(0),'concur');
				if($v['is_supplies']){
					$concurInfo[$k]['supplies']=D("ConcurSupplies")->concurS($v['id']);
				}
				if($v['is_service']){
					$service=M("ConcurService")->where("concur_id=%d",$v['id'])->find();
					$concurInfo[$k]['summary']=$service['summary'];
				}
			}
		}
		//获得标签
		$r = D('CategoryServer')->order('`order`')->select();
		//获得地域
		$province=D('ProvinceCity')->province();
		$this->assign("status",$status);
		$this->assign("type",$type);
		$this->assign("label",$label);
		$this->assign("pro",$pro);
		$this->assign("tag",$tag);
		$this->assign('concurInfo',$concurInfo);
		$this->assign('page',page($count[0]['count(*)'],$pageSize));
		$this->assign('list',$r);
		$this->assign('province',$province);
		if($tag){
			$this->title="爱心资源";
			$this->display("resource");
		}else{
			$this->title="求助项目";
			$this->display("index");
		}
	}
	/**
	 * 点击我要求助||我有资源
	 */
	public function help($tag){
		$user=UserSession::getUser();
		if($user){
			//判断是否是认证用户
			if(User::isVip($user['uid'])){
				$res=1;	
			}else{
				$res=0;
			}
		}else{
			//用户没有登录，提示用户登陆
			$res=-1;
		}
		$this->ajaxReturn(array('res'=>$res,'tag'=>$tag));
	}
	/**
	 * 服务求/捐助管理
	 */
	public function serviceManager($id,$p=1,$pageSize=15){
		//判断用户登录
		tag('user_login');
		//判断$id是否是求助服务信息||捐服务信息
		$isService=D("Concur")->getConcur($id);
		if($isService){
			if($isService['is_service']){
				if($isService['status']==100 || $isService['status']==888){
					if($isService['type']){
						$this->title="服务申请管理";
					}else{
						$this->title="服务捐助管理";
					}
					//申请该服务的人员
					$serviceUser=D("ConcurServiceApply")->getSerUser($id,$p,$pageSize);
					//服务信息
					$service=D("ConcurService")->service($id);
					//同意申请的人数
					$service['userPassCount']=0;
					//拒绝申请的人数
					$service['userNpassCount']=0;
					//待处理申请的人数
					$service['userAuthstrCount']=0;
					//同意申请的人数||拒绝申请的人数||待处理申请的人数的集合
					$userCount=D("ConcurServiceApply")->userCount($id);
					foreach ($userCount as $key=>$val){
						if($val['status']==1){
							$service['userPassCount']=$val['count(1)'];
						}elseif($val['status']==-1){
							$service['userNpassCount']=$val['count(1)'];
						}elseif($val['status']==0){
							$service['userAuthstrCount']=$val['count(1)'];
						}
					}
					$users=array();
					if($serviceUser['data']){
						foreach ($serviceUser['data'] as $k=>$v){
							//申请人的信息
							$user=json_decode(api("/User/getById",array('uid'=>$v['apply_uid'])));
							$province=M("ProvinceCity")->where(array("id"=>$user->provinceid))->find();
							$city=M("ProvinceCity")->where(array("id"=>$user->cityid))->find();
							if($user->type==11){
								//个人getById($uid)
								$volUser=json_decode(api("/Volunteer/getById",array('uid'=>$v['apply_uid'])));
								$real_name=$volUser->real_name;
								$phone=$volUser->vol_phone;
							}else{
								//组织
								$volUser=json_decode(api("/Organization/getInfo",array('orgid'=>$v['apply_uid'])));
								$real_name=$volUser->contact;
								$phone=$volUser->phone;
							}
							$users[$k]['status']=$v['status'];
							$users[$k]['image']=Image::getUrlThumbCenter($user->photo,array(),$user->gender==2?'user_girl':'user');
							$users[$k]['nickname']=$user->nickname;
							$users[$k]['userAddress']=$province['class_name'].' '.$city['class_name'];
							$users[$k]['email']=$user->email;
							$users[$k]['real_name']=$real_name;
							$users[$k]['type']=$user->type;
							$users[$k]['uid']=$user->uid;
							$users[$k]['phone']=$phone;
						}
					}
					$this->assign("isService",$isService);
					$this->assign("service",$service);
					$this->assign("users",$users);
					$this->assign("page",$serviceUser['page']);
					$this->display("Manager/service");
				}else{
					$this->title = ' 系统消息';
					$this->error("查看的求助不存在或没有通过审核，无法进行相关操作");
				}
			}else{
				$this->title = ' 系统消息 ';
				$this->error("该页面不存在");
			}
	
			
		}else{
			$this->title = ' 系统消息 ';
			$this->error("查看的求助不存在或没有通过审核，无法进行相关操作");
		}
	}
	/**
	 * 物资求/捐助管理
	 *
	 */
	public function suppliesManager($id,$p=1,$pageSize=15){
		//判断用户登录
		tag('user_login');
		//获取当前登陆用户发起的某一求助物资信息||捐物资信息
		$getConcur=D("Concur")->getConcur($id);
		if($getConcur ){
			if($getConcur['is_supplies']){
				if($getConcur['status']==100 || $getConcur['status']==888){
					if($getConcur['type']){
						$this->title="物资申请管理";
					}else{
						$this->title="物资捐助管理";
					}
					//某一求/捐助下物资申请人
					$supUser=D("ConcurSuppliesApply")->getSupUser($id,$p,$pageSize);
					//物资信息
					$supplies=D("ConcurSupplies")->concurS($id);
					//通过申请人数
					$supplies[0]['userPassCount']=0;
					//未通过申请人数
					$supplies[0]['userNpassCount']=0;
					//待审核申请人数
					$supplies[0]['userAuthstrCount']=0;
					//同意申请的人数||拒绝申请的人数||待处理申请的人数的集合
					$userCount=D("ConcurSuppliesApply")->userCount($id);
					foreach ($userCount as $key=>$val){
						if($val['status']==1){
							$supplies[0]['userPassCount']=$val['count(1)'];
						}elseif($val['status']==-1){
							$supplies[0]['userNpassCount']=$val['count(1)'];
						}elseif($val['status']==0){
							$supplies[0]['userAuthstrCount']=$val['count(1)'];
						}
					}
					$users=array();
					if($supUser['data']){
						foreach ($supUser['data'] as $key=>$vol){
							//申请人的信息
							$user=json_decode(api("/User/getById",array('uid'=>$vol['user_id'])));
							$province=M("ProvinceCity")->where(array("id"=>$user->provinceid))->find();
							$city=M("ProvinceCity")->where(array("id"=>$user->cityid))->find();
							if($user->type==11){
								//个人getById($uid)
								$volUser=json_decode(api("/Volunteer/getById",array('uid'=>$vol['user_id'])));
								$real_name=$volUser->real_name;
								$nickname=$user->nickname;
								$phone=$volUser->vol_phone;
							}else{
								//组织
								$volUser=json_decode(api("/Organization/getInfo",array('orgid'=>$vol['user_id'])));
								$real_name=$volUser->contact;
								$nickname=$volUser->org_name;
								$phone=$volUser->phone;
							}
							if($supplies[0]['type']){
								//邮寄地址
								$mailAddress=D("ConcurSuppliesAddress")->getAddress($id,$vol['user_id']);
								$users[$key]['mailAddress']=$mailAddress['address'];
								$users[$key]['mailName']=$mailAddress['name'];
								$users[$key]['mailPhone']=$mailAddress['phone'];
								$users[$key]['code']=$mailAddress['code'];
							}
							//物流详情
							if($vol['content']){
								$logistics_number=explode(",",$vol['logistics_number']);
								$content=json_decode($vol['content']);
								$data="";
								//foreach ($content->data as $k=>$v){
								//$data .="<li>".$v->time." ".$v->context."</li>";
								//}
								for($i=count($content->data)-1;$i>=0;$i--){
									$data .="<li title='".$content->data[$i]->time." ".$content->data[$i]->context."'>".$content->data[$i]->time." ".$content->data[$i]->context."</li>";
								}
								$content->data=$data;
								$users[$key]['logistics']=$content;
								if($content->data){
									if(time()-$vol['update_time']>3600){
										if($content->status !=3){
											$ems=ems($logistics_number[1],$logistics_number[0]);
											//更新物流信息
											D("ConcurSuppliesApply")->updateLogistics($vol['user_id'],$id,$ems);
										}
									}
								}
								//物流单号
								$users[$key]['logistics_number']=$logistics_number[0];
								//物流公司名称
								$users[$key]['expTextName']=$logistics_number[2];
							}
							$users[$key]['status']=$vol['status'];
							$users[$key]['image']=Image::getUrlThumbCenter($user->photo,array(0),$user->gender==2?'user_girl':'user');
							$users[$key]['nickname']=$nickname;
							$users[$key]['address']=$province['class_name'].' '.$city['class_name'];
							$users[$key]['email']=$user->email;
							$users[$key]['type']=$user->type;
							$users[$key]['uid']=$user->uid;
							$users[$key]['real_name']=$real_name;
							$users[$key]['phone']=$phone;
							//申请人想要捐助/救助的物资
							$r=D("ConcurSuppliesApply")->getAlreadySupplies($id,$vol['user_id'],$status=-2);
							$a="";
							foreach ($r as $k=>$v){
								$a .=$v['num']." x ".$v['name']."、";
							}
							$a=rtrim($a,"、");
							$users[$key]['supplies']=$a;
							$users[$key]['count']=mb_strlen($a,'utf8');
						}
					}
					$ems=emsApi::getEms();
					$this->assign("getConcur",$getConcur);
					$this->assign("supplies",$supplies);
					$this->assign("users",$users);
					$this->assign("page",$supUser['page']);
					$this->assign("ems",$ems);
					$this->display("Manager/supplies");
				}else{
					$this->title = ' 系统消息 ';
					$this->error("查看的求助不存在或没有通过审核，无法进行相关操作");
				}
			}else{
				$this->title = ' 系统消息 ';
				$this->error("该页面不存在");
			}
		}else{
			$this->title = ' 系统消息 ';
			$this->error("查看的求助不存在或没有通过审核，无法进行相关操作");
		}
	}
	/**
	 * 求款项管理
	 *
	 */
	public function moneyManager($id,$p=1,$pageSize=5,$tag=-1){
			//判断用户登录
			tag('user_login');
			//求款信息
			$moneyInfo=M("Concur")->where("id=%d",$id)->find();
			$this->assign("moneyInfo",$moneyInfo);
			$this->display("Manager/money");
		
		
	}
	/**
	 * 接受服务||物资申请人的申请
	 */
	public function fwReceive($uid,$id,$type,$tag){
		//判断$id是否是当前登陆发布的信息
		$isConcur=D("Concur")->getConcur($id);
		$res="";
		if($isConcur){
			$arr = array(
					'id'=>$id,
					'title'=>$isConcur['title'],
					'type'=>$isConcur['type'],
					'tag'=>$tag
			);
			if($tag){
				//物资
				$res=D("ConcurSuppliesApply")->accept($uid,$id);
				//D("Uc/ConcurApply")->suppliesAccept($uid,$id);
			}else{
				//服务
				$res=D("ConcurServiceApply")->accept($uid,$id,$type);
				//D("Uc/ConcurApply")->serviceAccept($uid,$id);
			}
			if($res){
				//添加站内消息
				D("Notification")->sendMsg($uid,'supplies_request_success',$arr,1);
			}
		}
		$this->ajaxReturn(array('res'=>$res,'type'=>$type));
	}
	/**
	 * 拒绝服务||物资申请人的申请
	 */
	public function fwReject($uid,$id,$text,$type,$tag){
		//判断$id是否是当前登陆发布的信息
		$isConcur=D("Concur")->getConcur($id);
		$res="";
		if($isConcur){
			$arr = array(
					'id'=>$id,
					'title'=>$isConcur['title'],
					'type'=>$isConcur['type'],
					'text'=>$text,
					'tag'=>$tag
			);
			if($tag){
				//物资
				$res=D("ConcurSuppliesApply")->refuse($uid,$id,$text);
				//D("Uc/ConcurApply")->suppliesRefuse($uid,$id);
			}else{
				//服务
				$res=D("ConcurServiceApply")->refuse($uid,$id,$text);
				//D("Uc/ConcurApply")->serviceRefuse($uid,$id);
			}
			if($res){
				//添加站内消息
				D("Notification")->sendMsg($uid,'supplies_request_fail',$arr,1);
			}
		}
		$this->ajaxReturn(array('res'=>$res,'type'=>$type));
	}
	/**
	 * 物资申请管理页添加物流信息
	 */
	public function addExpress($id,$uid,$text,$logistics){
		$r=ems($logistics,$text);
		$res=json_decode($r,true);
    	 if($res['status'] > 0){
    	 	$expTextName=emsApi::getEms($logistics);
			//判断$id是否是当前登陆发布的信息
			$isConcur=D("Concur")->getConcur($id);
			if($isConcur){
				//添加物流信息
				$textName = $text.",".$logistics.",".$expTextName;
				$data=D("ConcurSuppliesApply")->logistics($id,$uid,$textName,$r);
				if($data){
					$arr=array(
						'id'=>$id,
						'title'=>$isConcur['title'],
					);
					//添加站内消息
					D("Notification")->sendMsg($uid,'apply_logistics_complete',$arr);
					$this->ajaxReturn(array('res'=>$r,'expTextName'=>$expTextName,'mailNo'=>$text));
				}
				
			}
		}
		
	}
	/**
	 * 详情页中添加物流信息
	 */
	public function express($id,$text,$logistics){
		$uid=UserSession::getUser('uid');
		$r=ems($logistics,$text);
		$res=json_decode($r,true);
		if($res['status'] > 0){
			//添加物流信息
			$expTextName=emsApi::getEms($logistics);
			$textName = $text.",".$logistics.",".$expTextName;
			$data=D("ConcurSuppliesApply")->logistics($id,$uid,$textName,$r);
			if($data){
				$info=D("Concur")->getConcurById($id);
				$arr=array(
						'id'=>$id,
						'title'=>$info['title'],
				);
				//添加站内消息
				D("Notification")->sendMsg($info['creator'],'donate_logistics_complete',$arr);
				$this->ajaxReturn(array('res'=>$r,'expTextName'=>$expTextName,'mailNo'=>$text));
			}
		}
		
	}
}