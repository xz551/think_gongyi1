<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;
class ConcurSuppliesApplyModel extends Model{
	/**
	 * 定义互助当前状态
	 */
	const STATUS_VERIFY_DENY    = -1;       //未通过
	const STATUS_WAITFORCHECK   = 0;        //待审核状态
	const STATUS_NORMAL         = 1;        //审核通过
	//const STATUS_ENDED          = 888;      //撤销申请
    /**
     * 获取物资申请的内容
     * @param $id 互助表id
     * @param $uid 可选项，如果为0则表示所有用户的申请，不为0表示某个用户的申请
     * @param status 可选项，默认为1，则表示获取已经接受的申请 0表示请求状态的  -1表示拒绝的  -2表示全部
     */
    public function getAlreadySupplies($id,$uid=0,$status=1){
        $prefix =  C('DB_PREFIX');
        $applyTab = $prefix.'concur_supplies_apply';
        $suppliesTab = $prefix.'concur_supplies';
        $detailsTab = $prefix.'concur_supplies_apply_details';
        $map[$applyTab.'.concur_id'] = $id;
        //如果$status 传递-2则表示获取所有的
        if($status != -2){
            $map[$applyTab.'.status'] = $status;
        }else{
            $map[$applyTab.'.status'] = array('neq',-2);
        }
        //如果传递uid则表示获取某个用户的申请信息
        if($uid){
            $map[$applyTab.'.user_id'] = $uid;
        }
        return $this->where($map)->field("$suppliesTab.name,$suppliesTab.id,$detailsTab.num,$detailsTab.supplies_id")
                ->join("$detailsTab ON $applyTab.id = $detailsTab.apply_id")
                ->join("$suppliesTab ON $suppliesTab.id =$detailsTab.supplies_id")->select();
    }
    /**
     * 某一求助某一物资的已捐助信息
     */
    public function getConSup($supplies_id,$concur_id){
    	$prefix =  C('DB_PREFIX');
    	$applyTab = $prefix.'concur_supplies_apply';
    	$detailsTab = $prefix.'concur_supplies_apply_details';
    	$w=array(
    			$applyTab.'.concur_id'=>$concur_id,
    			$detailsTab.'.supplies_id'=>$supplies_id,
    			$applyTab.'.status'=>self::STATUS_NORMAL
    	);
    	return $this->field("$detailsTab.num")->join("left join $detailsTab on $detailsTab.apply_id=$applyTab.id")->where($w)->select();
    }
 	/**
 	 * 某一求/捐助下物资申请人
 	 */
    public function getSupUser($concur_id,$p,$pageSize){
    	$w=array(
    			'concur_id'=>$concur_id,
    			'status'=>array('in','0,1,-1')
    	);
    	$count=$this->where($w)->count();
    	$page = page($count, $pageSize);
    	$data=$this->where($w)->page($p . ',' . $pageSize)->order("date_time desc")->select();
    	return array('data'=>$data,'page'=>$page);
    }
    /**
     * 获取某一求/捐助物资下通过的申请人人数||未通过的申请人人数||待审核的申请人人数
     */
    public function userCount($id){
    	$sql="select status,count(1) from tb_concur_supplies_apply where concur_id = $id group by status";
    	return $this->query($sql);
    }
    /**
     * 接受申请人的申请
     */
    public function accept($uid,$concur_id){
    	$w=array(
    			'user_id'=>$uid,
    			'concur_id'=>$concur_id,
    			'status'=>self::STATUS_WAITFORCHECK
    	);
    	$map['status']=self::STATUS_NORMAL;
    	$map['update_time']=time();
    	return $this->where($w)->save($map);
    }
    /**
     * 拒绝申请人的申请
     */
    public function refuse($uid,$concur_id,$text){
    	$w=array(
    			'user_id'=>$uid,
    			'concur_id'=>$concur_id,
    			'status'=>self::STATUS_WAITFORCHECK
    	);
    	$map['status']=self::STATUS_VERIFY_DENY;
    	$map['update_time']=time();
    	$map['reason']=$text;
    	return $this->where($w)->save($map);
    }
    /**
     * 添加物流信息
     */
    public function logistics($concur_id,$uid,$text,$content,$status=1){
    	$w=array(
    			'user_id'=>$uid,
    			'concur_id'=>$concur_id,
    			'status'=>self::STATUS_NORMAL
    	);
    	$map['logistics_number']=$text;
    	$map['content']=$content;
    	$map['update_time']=time();
    	return $this->where($w)->save($map);
    }
	//更新物流信息
	public function updateLogistics($user_id,$id,$context,$status=1){
		$w=array(
				'user_id'=>$user_id,
				'concur_id'=>$id,
				'status'=>self::STATUS_NORMAL
		);
		$map['context']=$context;
		$map['update_time']=time();
		return $this->where($w)->save($map);
	}
	/**
	 * 判断是否有$id,$user_id的一条记录
	 */
	public function isSupplies($id,$user_id,$tag,$status){
		$w=array(
			'concur_id'=>$id,
			'user_id'=>$user_id			
		);
		if($tag==1){
			$w['status']=$status;
		}else{
			$w['status']=array('neq',$status);
		}
		return $this->where($w)->find();
	}
	//
	public function supplies($id,$apply_uid,$time,$tag){
		$w=array(
				'concur_id'=>$id,
				'user_uid'=>$apply_uid,
				'status'=>array('neq',-2)
		);
		if($tag==1){
			$w['update_time']=$time;
		}else{
			$w['date_time']=$time;
		}
		return $this->where($w)->find();
	}
}
