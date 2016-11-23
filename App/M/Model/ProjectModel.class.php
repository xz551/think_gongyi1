<?php
namespace M\Model;
use Lib\Image;
use M\Session\MUserSession;
use Think\Model;
class ProjectModel extends Model{
	/**
	 * 定义项目类型
	 */
	const TYPE_RECRUIT_VOLUNTEER = 1; //招募志愿者项目
	const TYPE_EVENT_SHOW = 2;  //征集志愿项目
	const TYPE_ROISE_GOODS = 3; //创建物资捐助项目
	
	/**
	 * 定义项目当前状态
	 */
	const STATUS_VERIFY_DENY = -1;       // 项目审核失败
	const STATUS_EDITING = 404;      //项目编辑中,未发布
	const STATUS_WAITFORCHECK = 403;      // 项目审核状态
	const STATUS_NORMAL = 100;      //正常状态&招募状态
	const STATUS_ENDED = 888;      //项目完成
	const STATUS_CLOSED = 889;      //项目关闭
	//招募项目信息
	public function recruitProject($id){
		$prefix=C('DB_PREFIX');
		$w=array(
			$prefix.'project.id'=>$id,
			$prefix.'project.type'=>self::TYPE_RECRUIT_VOLUNTEER	
		);
		$pro=$this->field($prefix."project.id,".$prefix."project.name,".$prefix."project.creator,".$prefix."project.show_image,".$prefix."project.status,".$prefix."project.summary,".
				$prefix."project.description,".$prefix."project.server_for,".$prefix."project.begin_time,".$prefix."project.end_time,".$prefix."project.provinceid,".
				$prefix."project.cityid,".$prefix."project.address,prj.name as jobname,prj.id as jobid,prj.description as des,prj.need_condition,prj.need_count,
				prv.volunteer_cost,prv.begin_time as bt,prv.end_time as et")->join("left join ".$prefix."project_recruit_job as prj on prj.project_id=".$prefix."project.id")
		->join("left join ".$prefix."project_recruit_volunteer as prv on ".$prefix."project.id=prv.id")->where($w)->select();
		return $pro;
	}
	//项目或求助状态
	public function getStatus(){
		return array(
				'888' => '报名已结束',
				'100'=>'报名进行中',
				'2'=>'等待认捐',
				'3'=>'认捐已完成',
				'4'=>'认捐已关闭'
				
		);
	}
	//求助信息
	public function donate($id){
		$donate=$this->where("id=%d and type=%d",$id,self::TYPE_ROISE_GOODS)->find();
		if($donate['status']==self::STATUS_NORMAL){
			//等待认捐
			$donate['status']=2;
		}elseif($donate['status']==self::STATUS_ENDED){
			//认捐已完成
			$donate['status']=3;
		}elseif($donate['status']==self::STATUS_CLOSED){
			//认捐已关闭
			$donate['status']=4;
		}
		return $donate;
	}
	//项目专题下项目的信息
	public function pro($id){
		$w=array(
			'event'=>$id,
			'type'=>self::TYPE_EVENT_SHOW,
			'status'=>self::STATUS_NORMAL		
		);
		return $this->where($w)->order('id desc')->select();
	}
	
	//项目专题下项目的个数
	public function proCount($id){
		$w=array(
			'event'=>$id,
			'type'=>self::TYPE_EVENT_SHOW,
			'status'=>self::STATUS_NORMAL		
		);
		return $this->where($w)->count();
	}
	//指定项目的信息
	public function proDetail($id){
		$prefix=C('DB_PREFIX');
		return $this->field($prefix.'project.id,'.$prefix.'project.name,'.$prefix.'project.creator,'.$prefix.'project.show_image,'.$prefix.'project.summary,'.
				$prefix.'project.create_date,'.$prefix.'project.server_for,'.$prefix.'project.provinceid,'.$prefix.'project.cityid,'.$prefix.'project.description,e.host')
		->join('left join '.$prefix.'event as e on '.$prefix.'project.event=e.id')->where($prefix."project.id=%d",$id)->find();
	}

	/**
	 * 用户参与的项目
	 * @param $uid 用户编号
	 * @param int $p 页数
	 * @param int $pagesize 每页显示几条
	 */
	public function getProjectJoin($uid,$p=1,$pagesize=5,$img=array(140,105)){
		$start=($p-1)*$pagesize;
		$sql=" SELECT p.show_image, p.id,p.`name`,prj.`name` as job_name,prd.user_id,prd.`status`,UNIX_TIMESTAMP(prd.create_date) as create_date".
			" from tb_project_recruit_detail as prd".
			" LEFT JOIN tb_project as p on prd.project_id=p.id".
			" LEFT JOIN tb_project_recruit_job as prj on prd.job_id=prj.id".
			" WHERE prd.user_id=$uid and prd.`status` <> ".ProjectRecruitDetailModel::STATUS_CACLED.
			" ORDER BY prd.create_date DESC".
			" LIMIT $start,$pagesize ";
		$data=M()->query($sql);
		foreach($data as $key=>$value){
			$show_image=$value['show_image'];
			$data[$key]['show_image']=Image::getUrl($show_image,$img);

		}
		return $data;
	}

	/**取得项目列表
	 * @param $uid
	 * @param int $p
	 * @param int $pagesize
	 */
	public function getProjectHot($limit=2){
		$project=$this->product_hot_data($limit);
		//补充报名岗位
		foreach ($project as $key => $value) {
			$pid=$value['id'];
			if($limit>2){
				$activeCreatorData = getApiContent(UCENTER.'/api/user/safeinfo?uid='.$value['creator'],false,true);
				$project[$key]['creator']=$activeCreatorData['nickname'];
				//状态

			}else{
				$project[$key]['job']=M('project_recruit_job')->where('project_id='.$pid.' and status=0')->select();
			}
			$image =Image::getUrlThumbCenter($value['show_image'], array(130, 97));
			$project[$key]['show_img']=$image;
		}
		return $project;
	}

	/** 推荐项目的数据
	 * @param $limit
	 * @param int $area
	 * @param int $label
	 * @param string $status
	 * @param string $actname
	 * @param string $id
	 * @param array $img
	 * @return mixed
	 */
	private function product_hot_data($limit,$area = 0, $label = 0,$status='0',$actname='',$id=''){
		$sql='SELECT 1 as hot, p.id,	p.creator,	p.`name`,	p.`status`,	p.show_image,'.
			' UNIX_TIMESTAMP(p.begin_time) AS begin_time, UNIX_TIMESTAMP(p.end_time) AS end_time,'.
			' GROUP_CONCAT(server_tag_id) AS server_tag_id'.
			' FROM 	tb_project_hot AS ph'.
			' LEFT JOIN tb_project AS p ON ph.project_id = p.id'.
			' LEFT JOIN tb_project_category_server_tag_list AS pc ON p.id = pc.project_id ';
		$sql = $this->_search_where('', $limit, $area, $label, $status, $actname, $id, $sql);
		$data = $this->query($sql);
		return $data ? $data:array();
	}

	/**查询搜索所有项目
	 * @param int $p 显示第几页
	 * @param int $pagesize 每页显示几条
	 * @param int $area 项目地域
	 * @param int $label 项目类别标签
	 * @param string $status 项目状态 1报名进行中 2报名已结束
	 * @param string $actname 项目名称
	 * @param string $id 项目编号 查询单个
	 * @param array $img 项目图片显示格式
	 * @return array()
	 */
	public function getAll($last_id='',$pagesize=2, $area = 0, $label = 0,$status='0',$actname='',$id='',$img=array(130, 97)){
		$cache_key="m_project_list_".$last_id."_".$pagesize."_".$area."_".$label."_".$status."_".$actname;
		$data=S($cache_key);
		if($id=="" && $data){
			return $data;
		}
		$data=$this->_search_data($last_id,$pagesize,$area,$label,$status,$actname,$id);
		foreach ($data as $key => $value) {
			$activeCreatorData = getApiContent(UCENTER.'/api/user/safeinfo?uid='.$value['creator'],false,true);
			$data[$key]['creator']=$activeCreatorData['nickname'];
			$image =Image::getUrlThumbCenter($value['show_image'],$img);
			$data[$key]['show_img']=$image;
		}
		S($cache_key,$data,60*10);
		return $data;

	}

	/**搜索数据
	 * @param string $last_id
	 * @param int $pagesize
	 * @param int $area
	 * @param int $label
	 * @param string $status
	 * @param string $actname
	 * @param string $id
	 * @return array|mixed
	 */
	private function _search_data($last_id='',$pagesize=2, $area = 0, $label = 0,$status='0',$actname='',$id=''){
		if($last_id==='' && $id=='') {
			$hot = $this->product_hot_data(6, $area, $label, $status, $actname, $id);
		}else{
			$hot=array();
		}
		$sql='SELECT  0 as hot,p.description, p.id,p.provinceid,p.cityid,p.address,	p.creator,p.server_for,	p.`name`,	p.`status`,	p.show_image,	UNIX_TIMESTAMP(p.begin_time) AS begin_time, UNIX_TIMESTAMP(p.end_time) AS end_time,	GROUP_CONCAT(server_tag_id) AS server_tag_id '.
			' from tb_project as p LEFT JOIN tb_project_category_server_tag_list as pc'.
			' on p.id=pc.project_id ';
		$sql = $this->_search_where($last_id, $pagesize, $area, $label, $status, $actname, $id, $sql);
		$data = $this->query($sql);
		$data= $data?$data:array();
		$data=array_merge($hot,$data);
		return $data;
	}

	/**
	 * @param $p
	 * @param $pagesize
	 * @param $area
	 * @param $label
	 * @param $status
	 * @param $actname
	 * @param $id
	 * @param $sql
	 * @return string
	 */
	private function _search_where($last_id='', $pagesize, $area, $label, $status, $actname, $id, $sql)
	{
		$w = ' where 1=1 ';
		if (!empty($id)) {
			$w .= sprintf("  and p.id=%d ", $id);
			return $sql.$w;
		}
		if($pagesize>6){
			$w.=" and  p.id not in (SELECT project_id from tb_project_hot) ";
		}
		if($last_id!=''){
			$w.= sprintf("  and p.id<%d ", $last_id);
		}
		if (!empty($area)) {
			$w .= sprintf("  and p.provinceid=%d ", $area);
		}
		if ($actname !== '') {
			$w .= sprintf("  and p.name like '%%%s%%' ", $actname);
		}
		if ($status==1) {
			//项目进行中
			$w .= ' and p.status in (100) ';
		} else if($status==2){
			$w .= ' and p.status in (888,889) ';
		}else{
			$w .= ' and p.status in (100,888,889) ';
		}

		$sql .= $w;
		$sql .= ' GROUP BY pc.project_id';
		if (!empty($label)) {
			$sql .= sprintf("  HAVING CONCAT(',',server_tag_id,',') like '%%,%d,%%' ", $label);
		}
		$sql .= sprintf(" order by p.id desc limit 0,%d", $pagesize);
		return $sql;
	}

	/**根据编号取得项目信息
	 * @param $id 项目编号
	 */
	public function getInfo($id,$img){
		$info=$this->_search_data('','','','','','',$id);

		if($info) {
			$info=$info[0];
			if($info['status']==\T\Model\ProjectModel::STATUS_NORMAL || $info['status']==\T\Model\ProjectModel::STATUS_CLOSED || $info['status']==\T\Model\ProjectModel::STATUS_ENDED ) {
				$job_recruit_sql = "SELECT j.*,count(1) as count,MAX(CASE rd.user_id WHEN " .
					(MUserSession::getUserId() ? MUserSession::getUserId() : -1) .
					"  THEN 1 ELSE 0 END) as isjoin  from tb_project_recruit_job as j".
					" LEFT JOIN tb_project_recruit_detail as  rd ".
					" on j.id=rd.job_id and rd.`status`<>101  ".
					"  where j.project_id=%d GROUP BY j.id;";
				$job_recruit_sql = sprintf($job_recruit_sql, $id);

				//填充数据
				//类别标签
				$server_tag_id = $info['server_tag_id'];
				$info['server_tag'] = D('CategoryServer')->getLabelName($server_tag_id);

				//图片
				$image = \Lib\Image::getUrlThumbCenter($info['show_image'], $img);
				$info['show_image'] = $image;
				//招聘的岗位
				$info['job'] = $this->query($job_recruit_sql);
				$activeCreatorData = getApiContent(UCENTER.'/api/user/safeinfo?uid='.$info['creator'],false,true);
				$info['creator']=$activeCreatorData['nickname'];
				//加载费用保障
				//SELECT * from tb_project_recruit_volunteer where id=288
				$info['volun']=M('project_recruit_volunteer')->field('volunteer_cost,UNIX_TIMESTAMP(begin_time) AS begin_time,UNIX_TIMESTAMP(end_time) AS end_time,question,volunteer_time')->find($id);
				//省市
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
}