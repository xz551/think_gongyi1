<?php 
namespace UC\Model;
use Think\Model;
class ConcurVerifyModel extends Model{
	/**
	 * 某一个用户认证的求助信息
	 */
	public function identification($uid,$tag,$p,$pageSize,$Condition=0){
		$start = ($p-1)*$pageSize;
		if(!$Condition){
			$limit = " limit $start,$pageSize";
		}else{
			$limit="";
		}
		$prefix =  C('DB_PREFIX');
		$tb1=$prefix.'concur';
		$tb2=$prefix.'concur_verify';
		$w = "$tb2.userid = $uid ";
		if($tag==1){
			//求款项
			$w .=" && $tb1.money <> 0";
		}elseif($tag==-1){
			//求物资
			$w .=" && $tb1.is_supplies !=0";
		}elseif($tag==-2){
			//求服务
			$w .=" && $tb1.is_service !=0";
		}
		$sql="select * from $tb2 left join $tb1 on $tb1.id=$tb2.concur_id where $w order by datetime desc $limit";
		return $this->query($sql);
	}
}
