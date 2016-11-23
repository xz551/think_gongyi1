<?php 
namespace Admin\Controller;
use Think\Controller;
use Admin\Model\ConcurModel;
use Lib\Image;
use Org\Util\String;
class ConcurController extends Controller{
	//互助审核页面
	public function Concurcheck($p=1,$pageSize=8){
		$r=D("Concur")->getinfo($p,$pageSize);
		$info=$r['info'];
		foreach ($info as $key=>$val){
			$user=json_decode(api("/User/getById",array('uid'=>$val['creator'])));
			$info[$key]['nickname']=$user->nickname;
			$info[$key]['image']=Image::getUrlThumbCenter($val['image'],array(206,157),'concur');
			$info[$key]['label'] = D('Common/CategoryServer')->getLabelName($val['label']);
			$province = D('Common/ProvinceCity')->getCity($val['provinceid']);
			$city = D('Common/ProvinceCity')->getCity($val['cityid']);
			$county = D('Common/ProvinceCity')->getCity($val['countyid']);
			$info[$key]['area'] = $province['class_name'].' '.$city['class_name'].' '.$county['class_name'];
			$info[$key]['start_time']=date("Y-m-d",$val['start_time']);
			$info[$key]['end_time']=date("Y-m-d",$val['end_time']);
			//求助项目
			if($val['money']){
				//求款项
			}
			if($val['is_supplies']){
				//求||捐物资
				$info[$key]['supplies']=D("ConcurSupplies")->concurSupplies($val['id']);
				//获取收件地址信息
				$mailAddress=D("T/ConcurSuppliesAddress")->getAddress($val['id'],$val['creator']);
				if($mailAddress){
					$info[$key]['mailAddress']=$mailAddress['address'];
					$info[$key]['mailName']=$mailAddress['name'];
					$info[$key]['mailPhone']=$mailAddress['phone'];
					$info[$key]['code']=$mailAddress['code'];
				}
			
			}
			if($val['is_service']){
				//求服务
				//服务信息
				$service=D("T/ConcurService")->service($val['id']);
				$info[$key]['servicesummary']=$service['summary'];
				$info[$key]['servicetime']=$service['time'];
				$info[$key]['serviceaddress']=$service['address'];
			}
		}
		$this->assign("info",$info);
		$this->assign("page",$r['page']);	
		$this->display("verify");
	}
	//ajax返回详情页
	public function detail($id){
		$concur=M("Concur")->where('id=%d',$id)->find();
		$this->assign('concur',$concur);
		$this->display("ajaxdetail");
	}
	//审核通过
	public function verifysuccess($id){
		$w=array(
			'id'=>$id,
			'status'=>403	
		);
		$map['status']=100;
		$r=M("Concur")->where($w)->save($map);
		if($r){
			$this->ajaxReturn(array('status'=>true,message=>'操作成功'));
		}else{
			$this->ajaxReturn(array('status'=>false,message=>'操作失败'));
		}
	}
	//审核不通过
	public function verifyrefuse($id,$reject){
		$map['status']=ConcurModel::STATUS_VERIFY_DENY;
		$map['reject_info']=$reject;
		$r=M("Concur")->where("id=%d",$id)->save($map);
		if($r){
			$this->redirect("Concurcheck");
		}else{
			$this->error("操作失败");
		}
	}
	//求助列表页面
	public function helplist(){
		$this->display("helplist");
	}
	//资源列表页面
	public function resourcelist(){
		$this->display("resourcelist");
	}
	//热点推荐
	public function hotrecommend(){
		$this->display("hotrecommend");
	}
}