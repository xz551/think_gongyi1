<?php 
namespace Uc\Model;
use Think\Model;
use Lib\UserSession;
class ConcurApplyModel extends Model{
	/**
	 * 我捐助的求助||我申请的资源
	 */
	public function donate($uid,$type,$tag,$count,$p,$pageSize){
		if(!$count){
			$start = ($p-1)*$pageSize;
			$limit = " limit $start,$pageSize";
		}else{
			$limit = "";
		}
		$prefix =  C('DB_PREFIX');
		$tb1=$prefix.'concur';
		$tb2=$prefix.'concur_apply';
		$w="$tb2.userid=$uid ";
		if($type){
			$w .= "&& $tb1.type=1 ";
		}else{
			$w .= "&& $tb1.type=0 ";
		}
		if($tag==1){
			//捐款项
			if($uid == UserSession::getUserId()){
				$w .=" && $tb2.money <> 0";
			}else{
				$w .=" && $tb2.money = 2";
			}
		}elseif($tag==-1){
			//捐物资
			if($uid == UserSession::getUserId()){
				$w .=" && $tb2.supplies <> 0";
			}else{
				$w .=" && $tb2.supplies = 2";
			}
		}elseif($tag==-2){
			//捐服务
			if($uid == UserSession::getUserId()){
				$w .=" && $tb2.service <> 0";
			}else{
				$w .=" && $tb2.service = 2";
			}
		}else{
			if($uid != UserSession::getUserId()){
				$w .=" && ($tb2.money =2 or $tb2.service =2 or $tb2.supplies = 2)";
			}else{
				$w .=" && ($tb2.money <> 0 or $tb2.service <> 0 or $tb2.supplies <> 0)";
			}
		}
		
		$sql="select $tb1.id,$tb1.title,$tb1.type,$tb1.image,$tb1.status,$tb1.creator,$tb2.userid,$tb2.supplies,$tb2.service,$tb2.money,$tb2.datetime,$tb2.updatetime from $tb2 left join $tb1 on $tb1.id = $tb2.concur_id where $w order by $tb2.datetime desc $limit";
		return $this->query($sql);
	}
	/**
	 * 接受捐物资的申请人的申请
	 */
	public function suppliesAccept($uid,$concur_id){
		$w=array(
				'userid'=>$uid,
				'concur_id'=>$concur_id,
				'supplies'=>1
		);
		$map['supplies']=2;
		$map['updatetime']=date("Y-m-d H:i:s",time());
		return $this->where($w)->save($map);
	}
	/**
	 * 拒绝捐物资的申请人的申请
	 */
	public function suppliesRefuse($uid,$concur_id){
		$w=array(
				'userid'=>$uid,
				'concur_id'=>$concur_id,
				'supplies'=>1
		);
		$map['supplies']=-1;
		$map['updatetime']=date("Y-m-d H:i:s",time());
		return $this->where($w)->save($map);
	}
	/**
	 * 接受捐服务的申请人的申请
	 */
	public function serviceAccept($uid,$concur_id){
		$w=array(
				'userid'=>$uid,
				'concur_id'=>$concur_id,
				'service'=>1
		);
		$map['service']=2;
		$map['updatetime']=date("Y-m-d H:i:s",time());
		return $this->where($w)->save($map);
	}
	/**
	 * 拒绝捐服务的申请人的申请
	 */
	public function serviceRefuse($uid,$concur_id){
		$w=array(
				'userid'=>$uid,
				'concur_id'=>$concur_id,
				'service'=>1
		);
		$map['service']=-1;
		$map['updatetime']=date("Y-m-d H:i:s",time());
		return $this->where($w)->save($map);
	}
}