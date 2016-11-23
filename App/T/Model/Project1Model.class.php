<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;
use Lib\User;
use Lib\Image;
use Think\Model;
use Lib\Image\UploadedFile;
use Lib\City;
use Lib\Helper;
use Lib\UserSession;

/**
 * Description of ProjectModel
 *
 * @author Administrator
 */
class Project1Model extends Model {
    
    /**
     * 定义项目类型
     */
    const TYPE_RECRUIT_VOLUNTEER = 1; //招募志愿者项目
    const TYPE_EVENT_SHOW = 2;  //征集志愿项目
    const TYPE_ROISE_GOODS = 3; //创建物资捐助项目

    /**
     * 定义项目当前状态
     */
    const STATUS_VERIFY_DENY    = -1;       // 项目审核失败
    const STATUS_EDITING        = 404;      //项目编辑中,未发布
    const STATUS_WAITFORCHECK   = 403;      // 项目审核状态
    const STATUS_NORMAL         = 100;      //正常状态&招募状态
    const STATUS_ENDED          = 888;      //项目完成
    const STATUS_CLOSED         = 889;      //项目关闭
    
    private $_PROJECT_INFO_BY_TYPE_KEY='_PROJECT_INFO_BY_TYPE_KEY'; 
    /**
     * 根据项目类型获取 项目集合信息
     * @param type $type
     * @param type $limit
     */
    public function getInfoByType($type=0,$limit=4,$nameleng=21,$status=100){
        $key=$this->_PROJECT_INFO_BY_TYPE_KEY.$type.$limit.$nameleng.$status;
        $project=S($key); 
        if($project){
            return $project;
        }
        //status asc,create_date desc
        if(is_array($status)){
            $w['status'] = array('in',$status);
        }else{
            $w=array('status'=>$status);
        }
        if($type){
            $w['type']=$type;
        }
        $project= M('Project')->where($w)->order('status asc,create_date desc')->limit($limit)->select();
        foreach ($project as $key => $value) {
            $projectid=$value['id'];
            //图片
            $image =Image::getUrl($value['show_image'], array(177, 124));// UploadedFile::getFileUrl($value['show_image'], array(177, 124), 'project');
            //发布者
            $projectCreatorData = getApiContent(UCENTER.'/api/user/safeinfo/uid/'.$value['creator'],false,true); 
            //服务区域
            $server_area = sprintf('%s %s', City::getName($value['provinceid']), City::getName($value['cityid']));
            //已报名
            $count=D('ProjectJoin')->joinCount($projectid);
            //招募人数
            $needcount=D('ProjectRecruitJob')->getRecruitNeedCount($projectid);
            
            $project[$key]['show_image']=$image;
            $project[$key]['creator']=Helper::Utf8Substr($projectCreatorData['nickname'],0,13);
            $project[$key]['name']=Helper::Utf8Substr($project[$key]['name'],0,$nameleng);
            $project[$key]['area']=$server_area;
            $project[$key]['usercount']=$count;
            $project[$key]['needcount']=$needcount;
            $project[$key]['url']=$this->ProjectViewUrl($projectid,$type); 
        }
        S($key,$project,C('CACHE_TIME'));
        return $project;
    }
    
    /**
    * 获取招募项目信息
    * @author WuWangCheng
    * @param type $pageIndex
    * @param type $pageSize
    * @param type $type
    * @param type $status
    * @param type $label
    * @param type $provi
    * @return $project
    */
    public function getRecruitInfo($pageIndex=1,$pageSize=16,$type=1,$status=100,$label=0,$provi=0){
        //缓存名称：这个不能是固定的，需要实时改变的。所以根据需要变的条件拼成的名字
        $cacheName = 'GET_RECRUIT_DATA_'.$pageIndex.'_'.$label.'_'.$provi;
        //如果缓存存在则直接调用缓存的数据否则走下面的进行数据库查询然后再缓存起来
        if(S($cacheName)){
            return S($cacheName);
        }
        $where['type']=$type;
        $where['status'] = is_array($status)?array('in',$status):$status;
        //类别标签默认是0，如果不是0的情况下进行条件查询
        if($label != 0){
            $where['id'] = array('in',$this->getProjectIdByServerTagId($label));
        }
        //地域标签默认是0，如果不是0的情况下进行条件查询
        if($provi != 0){
           $where['provinceid'] = $provi;
        }
        $project= $this->where($where)->order('create_date desc,status asc')->page($pageIndex.','.$pageSize)->select();
        foreach ($project as $key => $value) {
            $projectid=$value['id'];
            //图片
            $image =Image::getUrl($value['show_image'], array(177, 124));// UploadedFile::getFileUrl($value['show_image'], array(177, 124), 'project');
            //发布者
            $projectCreatorData = getApiContent(UCENTER.'/api/user/safeinfo/uid/'.$value['creator'],false,true); 
            //服务区域
            $server_area = sprintf('%s %s', City::getName($value['provinceid']), City::getName($value['cityid']));
            //岗位信息(岗位个数和需要人数)
            $job_info = $this->getJobNum($projectid);
            //截止日期
            $end_date = $this->getEndDate($value['end_time'],$value['begin_time']);
            //数据整理
            $project[$key]['show_image']=$image;
            $project[$key]['creator']=Helper::Utf8Substr($projectCreatorData['nickname'],0,13);
            $project[$key]['name']=Helper::Utf8Substr($project[$key]['name'],0,$nameleng);
            $project[$key]['area']=$server_area;
            $project[$key]['url']=$this->ProjectViewUrl($projectid,$type);
	    $project[$key]['job_num']= $job_info['job_num'];
            $project[$key]['need_num']= $job_info['need_count'];
            $project[$key]['clos_date'] = $end_date['clos_date'];
            $project[$key]['percent'] = $end_date['percent'];
                
        }
        //将上面的数据缓存起来
        S($cacheName,$project,C('CACHE_TIME'));
        return $project;
    }
    /**
     * 获取截止日期
     * @author WuWangCheng
     * @param type $e_time
     * @param type $b_time
     * @param type 0:时间戳 1：时间格式字符串
     * @return $end_date(剩的天数和截止百分比)
     * 
     */
    public function getEndDate($e_time,$b_time,$type=0){
        //0字符串格式时间 1时间戳
        if($type==0){
            $end_time = strtotime(date('Y-m-d',strtotime($e_time)));
            $beg_time = strtotime(date('Y-m-d',strtotime($b_time)));
        }else{
            $end_time = strtotime(date('Y-m-d',$e_time));
            $beg_time = strtotime(date('Y-m-d',$b_time));
        }
        $now_time = strtotime(date('Y-m-d',time()));
        //截止日期若小于当前日期则项目已结束不然计算截止天数
        if($end_time<$now_time){
            $end_date['clos_date'] = "已结束";
            $end_date['percent'] = 100;
        }else{
            $total_time = round(($end_time-$beg_time)/3600/24);
            $jiezhi = round(($end_time-$now_time)/3600/24);
            $end_date['clos_date'] = $jiezhi."天";
            $end_date['percent'] = round((1-($jiezhi/$total_time))*100) > 99?100:round((1-($jiezhi/$total_time))*100);
        }
        return $end_date;
    }
    /**
     * 根据服务标签ID查找项目ID
     * @author WuWangCheng
     * @param type $id
     * @return $pro_id
     */
    public function getProjectIdByServerTagId($label){
        $server_tag = M("ProjectCategoryServerTagList")->where("server_tag_id=".$label)->select();
        foreach ($server_tag as $val){
            $pro_id[] = $val["project_id"];
        }
        return $pro_id;
    }
    /**
     * 根据条件查询获取该条件下的项目数
     * @author WuWangCheng
     * @param type $type
     * @param type $status
     * @param type $label
     * @param type $provi
     * @return $pro_count
     */
    public function getProjectCount($type=1,$status=100,$label=0,$provi=0){
        $where['type']=$type;
        $where['status'] = is_array($status)?array("in",$status):$status;
        //类别标签默认是0，如果不是0的情况下进行条件查询
        if($label != 0){
            $where['id'] = is_array($label)?array("in",$label):$label;
        }
        //地域标签默认是0，如果不是0的情况下进行条件查询
        if($provi != 0){
           $where['provinceid'] = $provi;
        }
        $pro_count= M('Project')->where($where)->count();
        return $pro_count;
    }
    /**
     * 查找岗位个数及所需要的人数
     * @author WuWangCheng
     * @param type $p_id
     * @return $job_info
     */
    public function getJobNum($p_id){
        $job = M("ProjectRecruitJob")->where("project_id=".$p_id)->select();
        $job_info['job_num'] = count($job);
        if(!empty($job)){
            foreach ($job as $key=>$val){
                $job_info['need_count']+=$job[$key]['need_count'];
            }
        }else{
            $job_info['need_count'] = 0;
        }
        return $job_info;
    }
    
     /**
     * 仍然链接到原来的地址
     * 待项目视图页移动到thinkphp上后 切换
     *用U();
     */
    private function ProjectViewUrl($projectid,$type){
        if($type==self::TYPE_ROISE_GOODS){
            return YI_JUAN.'/raisegoods/view/id/'.$projectid.'.html';
        }
        return YI_JUAN.'/project/view/id/'.$projectid.'.html';
    }
    //获取用户参加、发起的||组织发起的相关项目的详细信息
    public function getPro($uid,$p,$pageSize,$type=self::TYPE_RECRUIT_VOLUNTEER,$status=ProjectJoinModel::STATUS_NORMAL){
    	$start = ($p-1)*$pageSize;
    	$prefix =  C('DB_PREFIX');
    	$tb1=$prefix.'project';
    	$tb2=$prefix.'project_join';
    	if(User::isUser($uid)){
    		$where="left join $tb2 on $tb1.id=$tb2.project_id where $tb2.type = $type && $tb2.status = $status && $tb2.uid = $uid && $tb1.status in(".self::STATUS_NORMAL.",".self::STATUS_ENDED.") or $tb1.creator=$uid and $tb1.type=".self::TYPE_EVENT_SHOW;
    	}else if(User::isOrg($uid)){
    		$h=$uid == UserSession::getUser('uid')?" ":" && $tb1.status in(".self::STATUS_NORMAL.",".self::STATUS_ENDED.")";
    		$where="where $tb1.creator = $uid && $tb1.type in(".self::TYPE_RECRUIT_VOLUNTEER.",".self::TYPE_EVENT_SHOW.") $h ";
    	}
    	echo $sql="select $tb1.id,$tb1.creator,$tb1.name,$tb1.summary,$tb1.end_time,$tb1.create_date,$tb1.show_image,$tb1.status,$tb1.type,$tb1.event "
    		."from $tb1 $where order by $tb1.create_date desc limit $start,$pageSize";exit;
    	$pro=$this->query($sql);
    	if($pro){
    		foreach ($pro as $k=>$v){
    			$now_time = time();
    			if($v['type'] == self::TYPE_EVENT_SHOW){
    				if($v['status'] == self::STATUS_NORMAL || $v['status']== self::STATUS_ENDED){
    					$event=M("event")->where('id=%d',$v['event'])->find();
    					$end_time=$event['vote_end_time'];
    					if($now_time < $end_time){
    						//评选进行中
    						$statu=-2;
    					}else if($now_time >= $end_time){
    						//评选已结束
    						$statu=-3;
    					}
    					$pro[$k]['status']=$statu;
    				}
    			}
    		}
    	}	
    	return $pro;
    }
    //用户参加的||发起的相关项目个数
    public function getProCount($uid,$type=self::TYPE_RECRUIT_VOLUNTEER,$status=ProjectJoinModel::STATUS_NORMAL){
    	$w="pj.type=$type and pj.status=$status and pj.uid=$uid and tb_project.status in (".self::STATUS_NORMAL.",".self::STATUS_ENDED.") or tb_project.creator=$uid and tb_project.type=".self::TYPE_EVENT_SHOW;
    	return $this->join('left join tb_project_join as pj on tb_project.id=pj.project_id')->where($w)->count();
    }
    //组织发起的相关项目个数
    public function getOrgProCount($uid){
    	$w=array(
    			'creator'=>$uid,
    			'type'=>array('in',''.self::TYPE_RECRUIT_VOLUNTEER.','.self::TYPE_EVENT_SHOW)
    	);
    	if($uid != UserSession::getUser('uid')){
    		$w['status']=array('in',''.self::STATUS_NORMAL.','.self::STATUS_ENDED);
    	}
    	return $this->where($w)->count();
    }
    //用户参加的||组织发起或参加的相关求助信息
    public function getDon($uid,$p,$pageSize,$type=self::TYPE_ROISE_GOODS,$status=ProjectJoinModel::STATUS_NORMAL){
    	$start = ($p-1)*$pageSize;
    	$prefix =  C('DB_PREFIX');
    	$tb1=$prefix.'project';
    	$tb2=$prefix.'project_join';
    	$h=$uid == UserSession::getUser('uid')?" ":" && $tb1.status in(".self::STATUS_NORMAL.",".self::STATUS_ENDED.",".self::STATUS_CLOSED.")";
    	$where="left join $tb2 on $tb1.id=$tb2.project_id where $tb1.creator = $uid && $tb1.type=$type $h OR $tb2.uid=$uid and $tb2.type=$type and $tb2.status=$status group by $tb1.id order by $tb1.create_date desc ";
    	$sql="select $tb1.id,$tb1.creator,$tb1.name,$tb1.description,$tb1.create_date,$tb1.status,$tb1.type "
    		."from $tb1 $where limit $start,$pageSize";
    	$don=$this->query($sql);
    	return $don;
    	} 
    //用户发起或参加||组织发起或参加的相关求助的个数
	public function getDonCount($uid,$type=self::TYPE_ROISE_GOODS,$status=ProjectJoinModel::STATUS_NORMAL){
		$prefix =  C('DB_PREFIX');
		$tb1=$prefix.'project';
		$tb2=$prefix.'project_join';
		$h=$uid == UserSession::getUser('uid')?" ":" && $tb1.status in(".self::STATUS_NORMAL.",".self::STATUS_ENDED.",".self::STATUS_CLOSED.")";
		$w="$tb1.creator=$uid and $tb1.type=$type $h or $tb2.uid=$uid and $tb2.type=$type and $tb2.status=$status";
		$c=$this->field('0')->join('left join '.$tb2.' on '.$tb1.'.id='.$tb2.'.project_id')
			->where($w)->group($tb1.'.id')->select();
		return count($c);
	}
	//获取用户参加过的所有项目信息
    public function getAllPro($uid,$p,$pageSize,$count=0){
    	$start = ($p-1)*$pageSize;
        $tb1 = '__PREFIX__project_join';
        $tb2 = '__PREFIX__project';
        $tb3 = '__PREFIX__evaluate';
        $limit=$count?" ":" limit $start,$pageSize";
        $sql ="select $tb1.uid,$tb1.project_id, "
                . "$tb2.creator,$tb2.name,$tb2.summary,$tb2.description, $tb2.begin_time, $tb2.create_date,$tb2.show_image,"
                . "$tb3.level,$tb3.content,$tb3.create_time from $tb1 left join $tb2 on $tb1.project_id = $tb2.id  "
                . "left join $tb3 on ($tb3.userID=$tb1.uid and $tb3.proID=$tb1.project_id) "
                . "where $tb1.uid=$uid and $tb2.status=".self::STATUS_ENDED." order by $tb3.level ".$limit;
        return $this->query($sql);
    }
    //获取待评价的项目数量
    public function finishProNum($uid){
    	$tb1 = '__PREFIX__project_join';
    	$tb2 = '__PREFIX__project';
    	$tb3 = '__PREFIX__evaluate';
    	$sql = "select count(*) as num, $tb2.id from $tb2 left join $tb1 on $tb2.id=$tb1.project_id "
    		  . "where $tb1.uid=$uid and $tb2.status=".self::STATUS_ENDED;
    	$result = $this->query($sql);
    	//评价过的数量项目数
    	$sql = "select count(*) as num from $tb3 where userID=$uid";
    	$r = $this->query($sql);
    	return $result[0]['num'] - $r[0]['num'];
    
    }
	
}
