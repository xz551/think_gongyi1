<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/26
 * Time: 11:55
 */

namespace M\Controller;


use Lib\Idcard;
use M\Business\UserBusiness;
use M\Session\MUserSession;
use T\Model\ProjectModel;
use Think\Controller;
use Think\Log;

class ProjectController extends Controller
{

    /**取得项目列表
     * @param string $last_id
     * @param int $pagesize
     * @param int $area
     * @param int $label
     * @param string $status
     * @param string $actname
     */
    public function all($last_id='',$pagesize=20, $area = 0, $label = 0,$status='0',$actname=''){
        layout(!IS_AJAX);
        $this->title='招募项目';
        $data=D('Project')->getAll($last_id,$pagesize, $area, $label,$status,$actname);
        $this->data=$data;
        $this->display(IS_AJAX?'ajax_all':'all');
    }

    /**取得项目详情
     * @param int $id
     */
    public function index($id=0){
        $info=D('Project')->getInfo($id,array(500,312));
        if(!$info || $info['id']==null){
            $this->error('项目不存在');
        }else if($info['status']==ProjectModel::STATUS_WAITFORCHECK){
            $this->error('项目正在审核');
        }else if($info['status']==ProjectModel::STATUS_EDITING){
            $this->error('项目不存在或未发布');
        }else if($info['status']==ProjectModel::STATUS_VERIFY_DENY){
            $this->error('项目审核失败');
        }
        $this->title=$info['name'];
        $this->info=$info;
        $this->display();
    }

    /**
     * 取得用户认证资料信息
     * 真实姓名
     * 手机号
     * 身份证
     */
    public function get_user_vol(){
        tag('m_user_login');
        layout(false);
        //取得登录用户认证信息
        $username='';
        $usercard='';
        $user_vol_lock=false;
        $vol=D('T/Volunteer')->find(MUserSession::getUserId());
        if($vol){
            //填写过 检查是否
            //status
            $user_vol_lock=$vol['status']==1;
            $username=$vol['real_name'];
            $usercard=$vol['idcard_code'];
        }
        $user=D('User')->getUser(MUserSession::getUserId());
        $userphone=$user['phone'];
        $user_phone_lock=$user['phone_status']==1;
        if($user_vol_lock || $user_phone_lock){
            //都已经认证了 不需要再弹出了
            echo '';
            exit;
        }
        $this->username=$username;
        $this->userphone=$userphone;
        $this->usercard=$usercard;
        $this->user_vol_lock=$user_vol_lock?1:0;
        $this->user_phone_lock=$user_phone_lock?1:0;
        $this->display();
    }

    /**参与项目
     * @param int $id 项目编号
     * @param string $uname 用户真实姓名
     * @param string $phone 手机号
     * @param string $idcard 用户身份证号
     */
    public function join($id=0,$uname='',$idcard='',$jobid=0){
        tag('m_user_login');
        $info=M('Project')->find($id);
        if(!$info){
            $this->return_error('项目不存在');
        }else if($info['status']!=ProjectModel::STATUS_NORMAL){
            $this->return_error('项目状态不能参与');
        }
        //检查岗位编号是否正确
        $job=M('project_recruit_job')->where("project_id=%d and id=%d",$id,$jobid)->find();
        if(!$job){
            $this->return_error('岗位错误');
        }
        if($job['status']==-1){
            $this->return_error('岗位报名已经结束');
        }
        if(!MUserSession::is_vol()){
            $this->return_error('组织用户不能参与');
        }
        //检查用户是否报名了该岗位
        $user_join_detail=M('project_recruit_detail')->where("project_id=%d and job_id=%d and user_id=%d",$id,$jobid,MUserSession::getUserId())->find();
        if($user_join_detail && $user_join_detail['status']!=101){
            //用户没有取消报名 不可以再次提交
            $this->return_error('您已经报名过该职位');
        }
        //检查用户是否参与过别的报名
        $user_join=M("project_join")->where("uid=%d and project_id=%d",MUserSession::getUserId(),$id)->find();
        if($user_join && $user_join['status']==1){
            $this->return_error('您已经参与过该项目');
        }
        /**
         * 参与项目时 更新用户认证信息
         */
        $result=UserBusiness::save_vol($uname,$idcard);
        if(is_string($result)){
            $this->return_error($result);
        }
        /**
         * 参与项目 更新用户基本信息
         */
        $result=UserBusiness::save_user();
        if(is_string($result)){
            $this->return_error($result);
        }
        //用户参与项目

        $detail_data=array(
            'status'=>100,
            'update_date'=>now_time()
        );
        if($user_join_detail){
            //更新
            $result=M('project_recruit_detail')->where('id=%d',$user_join_detail['id'])->save($detail_data);
        }else{
            //新增
            $detail_data['project_id']=$id;
            $detail_data['job_id']=$jobid;
            $detail_data['user_id']=MUserSession::getUserId();
            $detail_data['create_date']=now_time();
            $result=M('project_recruit_detail')->add($detail_data);
        }
        if(!$result){
            //参与岗位失败
            $this->return_error("岗位报名失败");
        }
        $join_data=array(
            'status'=>1,
            'update_time'=>now_time()
        );
        if($user_join){
            //更新
            $result=M("project_join")->where('id=%d',$user_join['id'])->save($join_data);
        }else{
            //新增
            $join_data['uid']=MUserSession::getUserId();
            $join_data['project_id']=$id;
            $join_data['type']=$info['type'];
            $join_data['create_date']=now_time();
            $result=M("project_join")->add($join_data);
        }
        if(!$result){
            //参与岗位失败
            $this->return_error("报名失败");
        }
        $this->return_success('报名成功');
    }
    private function return_error($msg){
        $this->ajaxReturn(array('status'=>-1,'msg'=>$msg));
    }
    private function return_success($msg){
        $this->ajaxReturn(array('status'=>1,'msg'=>$msg));
    }

    /**
     * 取消参与项目
     */
    public function cancel($id=0,$jobid=0){
        tag('m_user_login');
        $info=M('Project')->find($id);
        if(!$info){
            $this->return_error('项目不存在');
        }else if($info['status']!=ProjectModel::STATUS_NORMAL){
            $this->return_error('项目状态不能参与');
        }
        //检查岗位编号是否正确
        $job=M('project_recruit_job')->where("project_id=%d and id=%d",$id,$jobid)->find();
        if(!$job){
            $this->return_error('岗位错误');
        }
        if($job['status']==-1){
            $this->return_error('岗位报名已经结束');
        }
        //检查用户是否报名了该岗位
        $user_join_detail=M('project_recruit_detail')->where("project_id=%d and job_id=%d and user_id=%d",$id,$jobid,MUserSession::getUserId())->find();
        if(!$user_join_detail || $user_join_detail['status']==101){
            //用户没有取消报名 不可以再次提交
            $this->return_error('您没有报名该岗位');
        }
        //取消参与项目
        M('project_recruit_detail')->where("id=%d",$user_join_detail['id'])->save(array(
            'status'=>101,
            'update_date'=>now_time()
        ));
        /**
        //检查用户是否参与过别的报名
        $user_join=M("project_join")->where("uid=%d and project_id=%d",MUserSession::getUserId(),$id)->find();
        if(!$user_join || $user_join['status']!=1){
            $this->return_error('您没有报名该项目');
        }**/
        //取消参与
        M("project_join")
            ->where("uid=%d and project_id=%d",MUserSession::getUserId(),$id)
            ->save(array(
            'status'=>2,
            'update_time'=>now_time()
        ));
        $this->return_success('取消成功');
    }
}