<?php
namespace M\Model;
use Lib\Image;
use Think\Model;
use Think\Page;

class ActiveModel extends Model{
	/** 根据编号取得活动详情 进行中1 已结束2 即将开始3
	 * @param $actId
	 * @return mixed
	 */
	public function getActive($actId){
		$info=$this->getActiveList('',1,0,0,0,0,'',$actId,array(500,312));
		if($info){
			$info=$info[0];
			if($info['status']==\T\Model\ActiveModel::ACTIVE_STATUS_NORMAL){
				//有值 切已经审核通过 填充信息
				//类别标签转变为 文字描述
				$server_tag=$info['server_tag_id'];
				$info['server_label']=D('CategoryServer')->getLabelName($server_tag);
				//取得报名人数
				$info['join_count']=M('active_join')->where("active_id =%d and status=1",$actId)->count();
				$provinceid=$info['provinceid'];
				$cityid=$info['cityid'];
				$province=D('ProvinceCity')->getCity($provinceid); 
				$city=D('ProvinceCity')->getCity($cityid);
				$info['province']=$province['class_name'];
				$info['city']=$city['class_name'];
			}
		}
		return $info;
	}
	/** 取得活动所有状态
	 * @return array
	 */
	public function getStatus(){
		return array(
				'0' => '待审核',
				//'1'=>'审核通过',
				'-1' => '审核不通过',
				'2' => '即将开始',
				'3' => '进行中',
				'4' => '已结束'
		);
	}
	public function getActiveJoin($uid,$p=1,$pagesize=5,$img=array(140,105)){
		$data=M('ActiveJoin as aj')->join("left join tb_active as a on aj.active_id=a.id")
			->where("aj.`status` <> -1 and aj.uid=$uid")->field(" aj.active_id,a.`name`,aj.`status`,aj.join_date,a.image")->page($p.','.$pagesize)->select();
		foreach($data as $key=>$value){
			$show_image=$value['image'];
			$data[$key]['image']= Image::getUrl($show_image,$img);

		}
		return $data;
	}

	/**
	 * @param int $p 显示页数
	 * @param int $pagesize 每页显示条数
	 * @param int $area 区域
	 * @param int $label 类别标签
	 * @param int $type 类型 线上 线下
	 * @param string $status 状态 进行中1 已结束2 即将开始3
	 * @return mixed
	 */
	public function getActiveList($last_id='',$pagesize=2, $area = 0, $label = 0, $type = 0,$status='0',$actname='',$id='',$img=array(130, 97)){
		$cache_key="m_active_list_".$last_id."_".$pagesize."_".$area."_".$label."_".$type."_".$status."_".$actname;
		$data=S($cache_key);
		if($id=="" && $data){
			return $data;
		}
		$sql=" SELECT a.*,GROUP_CONCAT(server_tag_id) as server_tag_id from tb_active as a".
		 "   LEFT JOIN tb_active_server_tag as  ast on a.id=ast.active_id " ;
		 $time=time();
		if($id!==''){
			$w=sprintf(" where a.id=%d",$id);
			$sql.=$w;
		}else{
			$w=' where a.status='.\T\Model\ActiveModel::ACTIVE_STATUS_NORMAL;
			if($last_id!==''){
				$w.= sprintf("  and a.id<%d ",$last_id);
			}
			if(!empty($area)){
				$w.= sprintf("  and a.provinceid=%d ",$area);
			}
			if(!empty($type)){
				$w.= sprintf("  and a.type=%d ",$type);
			}
			if($actname!==''){
				$w.= sprintf("  and a.name like '%%%s%%' ",$actname);
			}
			
			if(!empty($status)){
				if($status==1){
					//进行中
					$w.=' and start_time<'.$time.' and end_time>'.$time;
				}else if($status==3){
					//即将开始
					$w.=' and start_time>='.$time;
				}else if($status==2){
					//已经结束
					$w.=' and end_time<'.$time;
				}
			}
			$sql.=$w;
			$sql.="  GROUP BY ast.active_id " ;
			if(!empty($label)){
				$sql.=sprintf( "  HAVING CONCAT(',',server_tag_id,',') like '%%,%d,%%' " ,$label);
			}
			$sql.=sprintf(" order by a.id desc limit 0,%d",$pagesize) ;
		}
		$data = $this->query($sql);
		foreach ($data as $key => $value) {
			//活动图片
			$image = \Lib\Image::getUrlThumbCenter($value['image'], $img);
			$data[$key]['image']=$image;
			//补充信息
			if($pagesize!=2){
				//不是首页，补充信息
				//创建者
				$activeCreatorData = getApiContent(UCENTER.'/api/user/safeinfo?uid='.$value['uid'],false,true);

				$start_time=$value['start_time'];
				$end_time=$value['end_time'];
				$status='';
				$status_id='';
				if($time<$start_time){
					$status='即将开始';
					$status_id=3;
				}else if($start_time < $time && $time<$end_time){
					$status='进行中';
					$status_id=1;
				}else if($end_time < $time){
					$status='已结束';
					$status_id=2;
				}
				$data[$key]['creator']=$activeCreatorData['nickname'];
				$data[$key]['status_label']=$status;
				$data[$key]['status_id']=$status_id;
			}
		}
		S($cache_key,$data,60*10);
		return $data;
	}
}