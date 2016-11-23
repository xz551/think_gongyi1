<?php 
/**
 * Created by PhpStorm.
 * User: zdf
 * Date: 2015/8/20
 * Time: 10:57
 */

namespace Admin\Model;
use Think\Model;
use Think\Page;
class ConcurVerifyModel extends Model{
	/**
	 * 某一求助项目下全部爱心认证员信息
	 */
	public function loves($id,$p,$pageSize,$tag){
		$prefix =  C('DB_PREFIX');
		$tb=$prefix.'concur_verify';
		$where=" concur_id=$id";
		if(!$tag){
			$start = ($p-1)*$pageSize;
			$limit = "limit $start,$pageSize";
		}else{
			$limit = "";
		}
		$sql="select * from $tb where $where order by datetime desc $limit";
		return $this->query($sql);
	}
}