<?php 
namespace Admin\Model;
use Think\Model;
class ConcurSuppliesModel extends Model{
	/**
	 * 定义互助申请当前状态
	 */
	const STATUS_VERIFY_DENY    = -1;       //未通过
	const STATUS_WAITFORCHECK   = 0;        //待审核状态
	const STATUS_NORMAL         = 1;        //审核通过
	//const STATUS_ENDED          = -2;      //撤销申请
	public function concurSupplies($id){
		$prefix =  C('DB_PREFIX');
		$tb1=$prefix.'concur';
		$tb2=$prefix.'concur_supplies';
		$w=array(
				$tb2.'.concur_id'=>$id
		);
		$data=$this->field("$tb1.title,$tb1.money,$tb1.is_supplies,$tb1.type,$tb2.id,$tb2.concur_id,$tb2.name,$tb2.num")->join("left join $tb1 on $tb1.id=$tb2.concur_id")->where($w)->select();
		$a="";
		foreach ($data as $k=>$v){
			$a .=$v['num']." x ".$v['name']."、";
		}
		return rtrim($a,"、");
	} 
}