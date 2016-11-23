<?php 
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/8/04
 * Time: 11:07
 */

namespace Admin\Model;
use Think\Model;
use Think\Page;
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
	//获得待审核状态下的互助信息
	public function getinfo($p,$pageSize){
		$w=array("status"=>self::STATUS_WAITFORCHECK);
		$count=$this->where($w)->count();
		$page=new Page($count,$pageSize);
		$totalPages = ceil($page->totalRows / $page->listRows); //总页数
		$info=$this->where($w)->order("create_date desc")->limit($page->firstRow.','.$page->listRows)->select();
		$page=pagestring($p,$totalPages);
		return array('info'=>$info,'page'=>$page);
	}
	/**
	 * 获取热门推荐的求助/资源项目
	 */
	public function concurinfo($type){
		$prefix=C('DB_PREFIX');
		return $this->join('right join '.$prefix.'concur_hot as chr on '.$prefix.'concur.id=chr.concur_id')->where($prefix.'concur.type=%d',$type)->limit(6)->select();
	}
	/**
	 * 获取进行中和已完成的求助/资源项目
	 */
	public function infoing($type,$p,$pageSize,$tag,$cid,$title,$status){
		$prefix =  C('DB_PREFIX');
		$tb=$prefix.'concur';
		$where=" type=$type ";
		if(!$tag){
			$start = ($p-1)*$pageSize;
			$limit = "limit $start,$pageSize";
		}else{
			$limit = "";
		}
		if($cid){
			$where .=" && id=$cid";
		}
		if($title){
			$where .=" && title like '%$title%'";
		}
		if($status){
			$where .=" && status=$status";
		}else{
			$where .=" && status in(".self::STATUS_ENDED.",".self::STATUS_NORMAL.")";
		}
		$sql="select * from $tb where $where order by update_date desc $limit";
		return $this->query($sql);
	}
	/**
	 * 获取求助/资源项目信息
	 */
	public function helplist($id,$title,$creator,$concur_genre,$concur_status,$type,$tag,$p,$pageSize){
		$prefix =  C('DB_PREFIX');
		$tb=$prefix.'concur';
		$where=" type=$type ";
		if($id){
			$where .=" && id=$id";
		}
		if($title){
			$where .=" && title like '%$title%'";
		}
		if($creator){
			$where .=" && creator like '%$creator%'";
		}
		/* if($concur_genre==1){
			//求moeny
			
		}else */if($concur_genre==2){
			//求物资
			$where .=" && is_supplies=1";
		}elseif($concur_genre==3){
			//求服务
			$where .=" && is_service=1";
		}
		if($concur_status){
			$where .=" && status=$concur_status";
		}
		if(!$tag){
			$start = ($p-1)*$pageSize;
			$limit = "limit $start,$pageSize";
		}else{
			$limit = "";
		}
		$sql="select * from $tb where $where order by update_date desc $limit";
		return $this->query($sql);
	}
	/**
	 * 求助/资源项目的状态
	 */
	public function status(){
		return array(
			self::STATUS_WAITFORCHECK=>'待审核',	
			self::STATUS_ENDED=>'已完成',
			self::STATUS_NORMAL=>'进行中',
			self::STATUS_VERIFY_DENY=>'未通过审核',
			self::STATUS_EDITING=>'草稿'	
		);
	}
	/**
	 * 求助项目类型
	 */
	public function genre(){
		return array(
			/* 1=>'求款项', */
			2=>'求物资',
			3=>'求服务'		
		);
	}
	/**
	 * 资源项目类型
	 */
	public function type(){
		return array(
			/* 1=>'捐款项', */
			2=>'捐物资',
			3=>'捐服务'		
		);
	}
}