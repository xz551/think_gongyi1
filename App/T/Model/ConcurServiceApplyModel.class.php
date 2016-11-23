<?php
namespace T\Model;
use Lib\User;
use Lib\Image;
use Think\Model;
use Lib\UserSession;
class ConcurServiceApplyModel extends Model{
    
     /**
     * 定义互助当前状态
     */
    const STATUS_VERIFY_DENY    = -1;       //未通过
    const STATUS_WAITFORCHECK   = 0;        //待审核状态
    const STATUS_NORMAL         = 1;        //审核通过
    const STATUS_ENDED          = 888;      //撤销申请
    //获取服务申请人
	public function getSerUser($service_id,$p,$pageSize){
		$w=array(
			'concur_id'=>$service_id,
			'status'=>array('in','0,1,-1')	
		);
		$count=$this->where($w)->count();
		$page = page($count, $pageSize);
		$data=$this->where($w)->page($p . ',' . $pageSize)->order("datetime desc")->select();
		return array('data'=>$data,'page'=>$page);
	}
	/**
	 * 获取某一求/捐助服务下通过的申请人人数 ||未通过的申请人人数||待审核的申请人人数
	 */
	public function userCount($id){
		$sql="select status,count(1) from tb_concur_service_apply where concur_id = $id group by status";
		return $this->query($sql);
	}
	/**
	 * 接受申请人的申请
	 */
	public function accept($uid,$id,$type){
		$w=array(
				'apply_uid'=>$uid,
				'concur_id'=>$id,
				'status'=>self::STATUS_WAITFORCHECK
		);
		$map['status']=self::STATUS_NORMAL;
		$map['updatetime']=time();
		if($type){
			$res=1;
			//首先判断是否还有其他的申请者
			$where=array(
					'apply_uid'=>array("neq",$uid),
					'concur_id'=>$id,
					'status'=>self::STATUS_WAITFORCHECK
			);
			$is_exist=$this->where($where)->count();
			$data['status']=self::STATUS_VERIFY_DENY;
			$data['updatetime']=time();
			if($is_exist){
				//拒绝其他申请人
				$res=$this->where($where)->save($data);
			}
			//资源项目状态变为已完成
			$result=D("Concur")->updateConcur($id);
			if($res && $result){
				return $this->where($w)->save($map);
			}else{
				return 0;
			}
			
		}else{
			return $this->where($w)->save($map);
		}
		
	}
	/**
	 * 拒绝申请人的申请
	 */
	public function refuse($uid,$id,$text){
		$w=array(
				'apply_uid'=>$uid,
				'concur_id'=>$id,
				'status'=>self::STATUS_WAITFORCHECK
		);
		$map['status']=self::STATUS_VERIFY_DENY;
		$map['updatetime']=time();
		$map['reason']=$text;
		return $this->where($w)->save($map);
	}
	/**
	 * 某一求助项目下捐款，捐物，捐服务名单
	 * 
	 */
	public function donName($id,$tag,$status,$p=1,$pageSize=1){
		$start=($p-1)*$pageSize;
		$prefix =  C('DB_PREFIX');
		$tb1 = $prefix.'concur_service_apply';
		$tb2 = $prefix.'concur_supplies_apply';
		$w1="$tb1.concur_id = $id ";
		$w2="$tb2.concur_id = $id ";
		$time1=$tb1.'.datetime';
		$time2=$tb2.'.date_time';
		if($tag==1){
			$time1=$tb1.'.updatetime';
			$time2=$tb2.'.update_time';
			$limit="limit $start,$pageSize";
			$w1 .=" and $tb1.status=$status";
			$w2 .=" and $tb2.status=$status";
		}elseif($tag==-1){
			$limit="limit $start,$pageSize";
			$w1 .=" and $tb1.status !=$status";
			$w2 .=" and $tb2.status !=$status";
		}elseif($tag==0){
			$limit="";
			$w1 .=" and $tb1.status=$status";
			$w2 .=" and $tb2.status=$status";
		}elseif($tag==-2){
			$limit="";
			$w1 .=" and $tb1.status !=$status";
			$w2 .=" and $tb2.status !=$status";
		}
		$sql="select $tb1.concur_id,$tb1.apply_uid,$time1 as time from $tb1 where $w1 union all
			  select $tb2.concur_id,$tb2.user_id,$time2 as time from $tb2 where $w2 order by time desc $limit";
		return $this->query($sql);
	}
	/**
	 * //判断是否有$id,$apply_uid的一条记录
	 */
	public function isService($id,$apply_uid,$tag,$status){
		$w=array(
			'concur_id'=>$id,
			'apply_uid'=>$apply_uid		
		);
		if($tag==1){
			$w['status']=$status;
		}else{
			$w['status']=array('neq',$status);
		}
		return $this->where($w)->find();
	}
	//
	public function service($id,$apply_uid,$time,$tag){
		$w=array(
			'concur_id'=>$id,
			'apply_uid'=>$apply_uid,
			'status'=>array('neq',-2)			
		);
		if($tag==1){
			$w['updatetime']=$time;	
		}else{
			$w['datetime']=$time;
		}
		return $this->where($w)->find();
	}
}
