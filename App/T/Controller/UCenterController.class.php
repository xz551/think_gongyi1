<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace T\Controller;
use Lib\UserSession;
use T\Model\OrganizationUser;
use T\Model\UCenterModel;
use Think\Controller;
/**
 * Description of UCenter
 *
 * @author Administrator
 */
class UCenterController extends Controller {    
    /**
 * 公益组织
 */
    public function vip(){
        $data = getApiContent(ucUrl('/api/organization/getListByUids',array('uids'=>$vipIds)),false,true);
        $this->assign("orgdata", $data);
        $this->display();
    }

    /**
     * 当前用户申请加入组织
     */
    public function join_org($orgid=0){
        $user=UserSession::getUser();
     
        
        $type=$user['type'];
        $resutl=array();
        if($user==null){
            //用户没有登录，提示用户登陆
            $resutl['status']="-1";
            $resutl['msg']="请登陆";

        }else if($type!=UCenterModel::USER_TYPE_NORMAL && $type !=UCenterModel::USER_TYPE_VOLUNTEER){
            //用户不是志愿者，不能操作
            $resutl['status']="0";
            $resutl['msg']="您不能执行此操作";
        }else {
            $resutl=$this->is_org($orgid);
            if(!isset($resutl['status'])){
                if($type==UCenterModel::USER_TYPE_NORMAL){
                    $resutl['status']="-2";
                    $resutl['msg']="请成为志愿者后，再执行操作";
                }else {
                    $resutl_vol = $this->is_vol($user["uid"]);
                    if (!isset($resutl_vol['status'])) {
                        $resutl = $this->join_org_do($orgid,$user["uid"]);
                    }else{
                        $resutl['status']=$resutl_vol['status'];
                        $resutl['msg']=$resutl_vol['msg'];
                    }
                }
            }
        }
         
            if (I('callback')) {
                echo I('callback') . "(" . json_encode($resutl) . ")";
            } else {
                $this->ajaxReturn($resutl);
            }
         
    }

    /**验证是否为组织账号
     * @param $org
     * @return array
     */
    private function is_org($orgid){
        $org=api('/user/getById/uid/'.$orgid);
        $resutl=array();
        if($org==null){
            $resutl['status']="0";
            $resutl['msg']="请选择组织用户加入";
        }else {
            $orgObj=json_decode($org); 
            if($orgObj->type!=UCenterModel::USER_TYPE_ORG && $orgObj->type!=UCenterModel::USER_TYPE_UNORG){
                $resutl['status']="0";
                $resutl['msg']="请选择组织用户加入";
            }else{
                $resutl['orgname']=urlSafeBase64_encode($orgObj->uid);
            }
        }
        return $resutl;
    }

    /**验证是否为认证的志愿者
     * @param $uid
     * @return array
     */
    private function is_vol($uid){

        $vol_string=api('/Volunteer/getById/uid/'.$uid);
        $vo=json_decode($vol_string);
        $resutl=array();
        if($vo==null){
            //获取信息失败
            $resutl['status']="0";
            $resutl['msg']="用户信息获取失败";
        }else if($vo->vol_status==0){
            //审核没有通过，提示用户申请成为志愿者
            $resutl['status']="-2";
            $resutl['msg']="请成为志愿者后，再执行操作";
        }
        return $resutl;
    }

    /**
     * 用户加入组织的状态
     */
    public function org_user_status($org){
        $user=UserSession::getUser();
        $type=$user['type'];
        if($type==UCenterModel::USER_TYPE_ORG || $type==UCenterModel::USER_TYPE_UNORG){
            //组织用户 不 显示
            $msg=22;
        }else{
            $status=D('OrganizationUser')->where(array('org_id'=>$org,'uid'=>UserSession::getUser('uid')))->getField('status');
            $msg=$status===null?-1:$status;
        }

        if(I('callback')){
            echo  I('callback')."({'status':'".$msg."'})";
        }else{
            $this->ajaxReturn(array('status'=>$msg));
        }
    }

    /**执行加入操作
     * @param $orgid
     * @param $user
     * @return mixed
     */
    public function join_org_do($orgid,$uid,$t=0)
    {
        $user=UserSession::getUser();
        $resutl = D('OrganizationUser')->joinOrg($orgid, $uid);
        if($t==1){
            echo json_encode($resutl);
        }else{
            return $resutl;
        }
    }
    
    /**
     * 获取通知数
     */
    public function getNotice(){
    	$notice_num=api("/User/getUnreadNoticeNum",array('uid'=>UserSession::getUser('uid')));
    	$callback=I('callback');
    	if($callback){
    		echo $callback.'({"count":"'.$notice_num.'"})';
    	}else{
    		echo $notice_num;
    	}
    }
    
    /**
     * 获取私信数
     */
    public function getMessage(){
    	$message_num=api("/User/getMessageboxNum",array('uid'=>UserSession::getUser('uid'))); //私信数
    	$callback=I('callback');
		if($callback){
			echo $callback.'({"count":"'.$message_num.'"})';
		}else{
			echo $message_num;
		}
    }
    
}
