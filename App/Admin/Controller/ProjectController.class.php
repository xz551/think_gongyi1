<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/5/20
 * Time: 13:55
 */

namespace Admin\Controller;


use Admin\Model\ProjectModel;
use Think\Controller;

class ProjectController extends Controller {

    /**
     *待审核项目数量
     */
    public function verify_count(){
        echo M('Project')->where(array('status'=>ProjectModel::STATUS_WAITFORCHECK))->count();
    }
    /**
     * 处理强制关闭项目提交过来的数据
     * @param unknown $id
     * @param unknown $reject
     */
    public function close($id,$reject){
    	$map['status']=ProjectModel::STATUS_VERIFY_DENY;
    	$map['reject_info']=$reject;
    	$r=M("Project")->where("id=%d",$id)->save($map);
    	if($r){
    		$this->redirect("/project/admin");
    	}else{
    		$this->error("强制关闭项目出错了");
    	}
    }

}