<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Common\Model;
use Think\Model;
/**
 * Description of ActiveJoinModel
 *
 * @author Administrator
 */
class ActiveJoinModel extends Model{
    /**
	 * 用户参与活动的状态
	 * 1 已经参与
	 * -1 取消参与
	 * @version 2013-9-22
	 * @author wwpeng
	 * @var unknown_type
	 */
	const ACTIVE_JOIN_STATUS_NORMAL = 1;
	const ACTIVE_JOIN_STATUS_CANCEL = -1;
	/**
	 * 是否签到
	 * @var unknown_type
	 */
	const ACTIVE_JOIN_ARRIVED = 1;
	const ACTIVE_JOIN_UNARRIVED = 0;
    //put your code here
    public function joinCount($activeid,$all=false){
        $w=array(
            'active_id'=>$activeid
        );
        if(!$all){
            $w['status']=self::ACTIVE_JOIN_STATUS_NORMAL;
        }
        //array('status'=>ActiveJoin::ACTIVE_JOIN_STATUS_NORMAL,'active_id'=>$id)
        return M('ActiveJoin')->where($w)->count();
    }
}
