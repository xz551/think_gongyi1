<?php 
namespace Uc\Model;
use Lib\User;
use Lib\Image;
use Think\Model;
use Lib\UserSession;
class ConcurModel extends Model{
	/**
	 * 定义互助当前状态
	 */
	const STATUS_VERIFY_DENY    = -1;       //互助审核失败
	const STATUS_EDITING        = 404;      //项目编辑中,未发布
	const STATUS_WAITFORCHECK   = 403;      //项目审核状态
	const STATUS_NORMAL         = 100;      //正常状态&招募状态
	const STATUS_ENDED          = 888;      //项目完成(已结束)
	const STATUS_CLOSED         = 889;      //项目关闭
	/**
	 * 
	 * 获取当前登陆者发起的求助信息||提供资源的信息
	 */
	public function getConcur($uid,$type,$tag,$num,$p,$pageSize){
		if(!$num){
			$start = ($p-1)*$pageSize;
			$limit = "limit $start,$pageSize";
		}else{
			$limit = "";
		}
		$prefix =  C('DB_PREFIX');
		$tb1=$prefix.'concur';
		$where = "creator=$uid ";
		if($type){
			$where .= "&& type=1 ";
		}else{
			$where .= "&& type=0 ";
		}
		if($tag==1){
			//求款项
			$where .=" && money !=0";
		}elseif($tag==-1){
			//求物资
			$where .=" && is_supplies !=0";
		}elseif($tag==-2){
			//求服务
			$where .=" && is_service !=0";
		}
		if($uid != UserSession::getUserId()){
			$where .=" && status in(".self::STATUS_ENDED.",".self::STATUS_NORMAL.")";
		}
		$sql="select * from $tb1 where $where order by update_date desc $limit";
		return $this->query($sql);
	}
}