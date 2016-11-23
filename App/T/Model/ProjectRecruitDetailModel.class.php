<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;

class ProjectRecruitDetailModel extends Model{
    /**
     * 分页获取用户的服务时长
     */
    public function getUserTimeInfo($uid,$p,$pageSize){
    	$start = ($p-1)*$pageSize;
        $prefix =  C('DB_PREFIX');
        $tb = $prefix.'project_recruit_detail';
        $proTb = $prefix.'project';
        $jobTb = $prefix.'project_recruit_job';
        $sql = "select $tb.project_id,$tb.server_time,$tb.allot_time,$tb.assigner,$proTb.name as proname,$proTb.creator as creator,$jobTb.name as jobname from $tb "
                . " left join $proTb on $tb.project_id = $proTb.id "
                . " left join $jobTb on $tb.job_id = $jobTb.id "
                . " where $tb.user_id = $uid && $tb.server_time>0"
                ." limit $start,$pageSize";
        return $this->query($sql);
    }
    //获取用户总的服务时长
    public function getUserTimeNum($uid){
    	$w=array(
    		'user_id'=>$uid,
    		'server_time'=>array('gt',0)
    	);
    	$userTimeInfo=$this->where($w)->select();
    	$count=0;
    	if($userTimeInfo){
    		foreach ($userTimeInfo as $k=>$v){
    			$count +=$v['server_time'];	
    		}
    	}
		return $count;    	
    }
    //获取用户参加项目且分配时长的个数
    public function getUserTimeCount($uid){
    	$w=array(
    		'user_id'=>$uid,
    		'server_time'=>array('gt',0)	
    	);
    	return $this->where($w)->count();
    }
    
}
