<?php
namespace M\Model;
use Think\Model;
class DiscuseModel extends Model{

	/**
	 * 项目讨论
	 * @var INT
	 */
	const DISCUSE_TYPE_PROJECT = 1;
	/**
	 * 项目反馈
	 * @var INT
	 */
	const DISCUSE_TYPE_PROJECTBACK = 3;
	/**
	 * 活动讨论区
	 * @var unknown_type
	 */
	const DISCUSE_TYPE_ACTIVE = 4;
	/**
	 * 问答类型讨论区
	 */
	const DISCUZE_TYPE_QA = 5;
	/**
	 * 活动图片
	 * @var unknown_type
	 */
	const DISCUSE_TYPE_ACTIVE_PHOTO = 6;
	/**
	 * 留言正常
	 * @var INT
	 */
	const DISCUSE_STATUS_NORMAL = 1;
	/**
	 * 留言删除
	 * @var INT
	 */
	const DISCUSE_STATUS_DELETE = -1;
	/**
	 * 不是楼主
	 * @var INT
	 */
	const DISCUSE_NOT_CREATOR = 0;
	
	/**
	 * 是楼主
	 * @var INT
	 */
	const DISCUSE_IS_CREATOR = 1;
	//项目反馈
	public function feedback($id){
		$prefix=C('DB_PREFIX');
		$w=array(
			$prefix.'discuse.relation_id'=>$id,
			$prefix.'discuse.type'=>self::DISCUSE_TYPE_PROJECTBACK,
			$prefix.'discuse.status'=>self::DISCUSE_STATUS_NORMAL		
		);
		return $this->field($prefix.'discuse.content,'.$prefix.'discuse.create_date,'.$prefix.'discuse.userphoto,pbe.image,v.video_id')
		->join('left join '.$prefix.'project_back_ext as pbe on '.$prefix.'discuse.id=pbe.did')->join('left join '.$prefix.'video as v on pbe.video=v.id')->where($w)->select();
	}
	//活动图片
	public function activePic($id){
		$prefix=C('DB_PREFIX');
		$w=array(
			$prefix.'discuse.relation_id'=>$id,
			$prefix.'discuse.status'=>self::DISCUSE_STATUS_NORMAL,
			$prefix.'discuse.type'=>self::DISCUSE_TYPE_ACTIVE_PHOTO	
		);
		return $this->field('pbe.image')->join('right join '.$prefix.'project_back_ext as pbe on '.$prefix.'discuse.id=pbe.did')->where($w)->select();
	}
	//用户参与活动上传的所有活动图片
	public function allActivePic($uid,$p,$pageSize){
		$start = ($p-1)*$pageSize;
		$prefix=C('DB_PREFIX');
		$w=array(
				$prefix.'discuse.user_id'=>$uid,
				$prefix.'discuse.status'=>self::DISCUSE_STATUS_NORMAL,
				$prefix.'discuse.type'=>self::DISCUSE_TYPE_ACTIVE_PHOTO
		);
		return $this->field('pbe.image,'.$prefix.'discuse.id,'.$prefix.'discuse.content,'.$prefix.'discuse.create_date,'.$prefix.'discuse.relation_id')
		->join('right join '.$prefix.'project_back_ext as pbe on '.$prefix.'discuse.id=pbe.did')->order($prefix.'discuse.create_date desc')->where($w)->limit($start,$pageSize)->select();
	}
	//用户参与活动上传的所有活动图片个数
	public function allActPicCount($uid){
		$prefix=C('DB_PREFIX');
		$w=array(
				$prefix.'discuse.user_id'=>$uid,
				$prefix.'discuse.status'=>self::DISCUSE_STATUS_NORMAL,
				$prefix.'discuse.type'=>self::DISCUSE_TYPE_ACTIVE_PHOTO
		);
		return $this->join('right join '.$prefix.'project_back_ext as pbe on '.$prefix.'discuse.id=pbe.did')->where($w)->count();
	}
	
}