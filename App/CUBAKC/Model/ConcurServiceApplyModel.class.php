<?php 
namespace UC\Model;
use Think\Model;
class ConcurServiceApplyModel extends Model{
	/**
	 * 我捐助的求助
	 */
	public function donate($uid,$p,$pageSize){
		$start=($p-1)*$pageSize;
		$limit="limit $start,$pageSize";
		$prefix =  C('DB_PREFIX');
		$tb1 = $prefix.'concur_service_apply';
		$tb2 = $prefix.'concur_supplies_apply';
		$w1="$tb1.apply_uid = $uid and $tb1.status <> -2";
		$w2="$tb2.user_id = $uid and $tb2.status <> -2";
		
		$sql="select $tb1.concur_id,$tb1.apply_uid,$tb1.datetime as time from $tb1 where $w1 union 
			select $tb2.concur_id,$tb2.user_id,$tb2.date_time as time from $tb2 where $w2 order by time desc $limit";
		return $this->query($sql);
	} 
}