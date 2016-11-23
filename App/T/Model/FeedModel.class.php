<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;
use Think\Model;
/**
 * Description of FeedModel
 *
 * @author Administrator
 */
class FeedModel extends Model {
    
    protected $connection ="USER_CONTER";
    
    //put your code here
    public function getFeed($pid=null,$uid=null,$limit=10){
	$d= D('Feed')->select(); 
	return $d;	
    }
    public function getDyNum($uid){
    	$w['uid']=$uid;//UserSession::getUser('uid');
    	return $this->where($w)->count();
    }
    
    
    /**
     * 添加动态
     */
  public function addFeed($uid,$pid,$type,$joinID='',$d){
        if($joinID){
            if($type == 'Primaries'){
                $data['primaries_uid'] = $joinID;
            }elseif($type == 'SuccessRaise'){
                $data['join_id'] = $joinID;
            }else{
                $data = $joinID;
            }
        }
	
  	$data = is_array($data)?array_merge($data,$d):$d;
	$map['data'] = $data;
        $map['uid'] = $uid;
        $map['pid'] = $pid;
        $map['type'] = $type;
        $map['create_date'] = time();
        $this->add($map);
    }
    
}
