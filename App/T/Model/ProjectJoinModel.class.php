<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;

use Think\Model;

/**
 * Description of ProjectJoinModel
 *
 * @author Administrator
 */
class ProjectJoinModel extends Model {

    //put your code here
    const STATUS_NORMAL = 1;
    const STATUS_CANCLED = 2;

    public function joinCount($projectid){
        $c= M('ProjectJoin')->where('project_id='.$projectid.' and status='.self::STATUS_NORMAL)->count(); 
        return $c;
    }
    //查询用户是否参与过项目
    public function checkPro($pid,$uid){
    	$map['uid'] = $uid;
    	$map['project_id'] = $pid;
    	return $this->where($map)->count();
    }
}
