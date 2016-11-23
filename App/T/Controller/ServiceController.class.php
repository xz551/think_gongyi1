<?php
namespace T\Controller;
use Think\Controller;
use Lib\UserSession;
use T\Model\ConcurModel;
class ServiceController extends Controller{
    /**
     * 申请服务
     */
    public function applyService($id,$sid){  
        tag('user_login');
        tag('db_begin');     
        //检测是否为认证用户，非认证用户不能进行捐助或求助申请
        if(!D('User')->checkAuth()){
            $this->ajaxReturn(array('status'=>-1,'content'=>'非认证用户不能进行操作'));
        }
        $concur = D('Concur')->getConcurById($id);
        //检测权限
        $auth = $this->checkAuth($concur,$sid);
        if($auth['status'] == -1){
             $this->ajaxReturn(array('status'=>-1,'content'=>$auth['content']));
             die();
        }
        $_w = array(
           'concur_id'  => $concur['id'],
           'service_id' => $sid,
           'apply_uid'  => UserSession::getUser('uid'),
           'status'     => -1,
        );
        $r = M('ConcurServiceApply')->where($_w)->find();
       //获取申请服务相关数据
       $_d = array(
           'service_id' => $sid,
           'apply_uid'  => UserSession::getUser('uid'),
           'datetime'   => time(),
           'updatetime' => time(),
           'concur_id'  => $id,
           'status'     => 0,
       );
       //重新申请,将拒绝的状态修改为申请状态
        $status = 1;
        $content = 'success';
       if($r){
           if(!M('ConcurServiceApply')->where('id=%d',$r['id'])->save($_d)){
               $this->ajaxReturn(array('status'=>-1,'content'=>'申请失败'));
           }else{
               //$concurApply = $this->addConcurApply($id);  //更新服务申请记录
           }
       //新添加
       }elseif(M('ConcurServiceApply')->add($_d)){
            //$concurApply = $this->addConcurApply($id);  //添加服务申请记录
            //添加站内消息
            $arr = array('id'=>$id,'title'=>$concur['title'],'type'=>$concur['type']);
            D("Notification")->sendMsg($concur['creator'],'service_request',$arr,1);
       }else{
            $status = -1;
            $content = "操作失败";
       }
            $this->ajaxReturn(array('status'=>$status,'content'=>$content));
    }
    
    /**
     * 检测权限
     */
    private function checkAuth($concur,$sid){
        if(!$concur){
            return array('status'=>-1,'content'=>'禁止操作');
        }
        $_w = array(
           'concur_id'  => $concur['id'],
           'service_id' => $sid,
           'apply_uid'  => UserSession::getUser('uid'),
           'status'     => array('in','0,1'),
       );
       $r = M('ConcurServiceApply')->where($_w)->find();
       if($r){
            return array('status'=>-1,'content'=>'不能重复提交');   
       }
       if($concur['creator'] == UserSession::getUser('uid')){
           return array('status'=>-1,'content'=>'不能申请自己的服务');
       }
       if($concur['status'] != ConcurModel::STATUS_NORMAL){
          return array('status'=>-1,'content'=>'该服务不是正常的发布状态');
       }
       if($concur['end_time'] <= time()){
          return array('status'=>-1,'content'=>'服务已过期');
       }
    }
    
    /**
     * 发起人结束服务
     */
    public function applyEnd($id){
        tag('user_login');
        tag('db_begin');
        $_w = array(
            'id' =>$id,
            'creator' => UserSession::getUser('uid'),
        );
        $_d = array(
            'is_service' => -1,
            'update_date'   => time(),
        );
       if(M('Concur')->where($_w)->save($_d)){
           $isend = $this->endConcur($id);
           $this->ajaxReturn(array('status'=>1,'content'=>'success','isend'=>$isend));
       }else{
           $this->ajaxReturn(array('status'=>-1,'content'=>'操作失败'));
       }
    } 
    
    /**
     * 撤销服务申请
     */
    public function endOnMine($id,$sid){
        tag('user_login');
        tag('db_begin');
        $_w = array(
            'service_id'  => $sid,
            'concur_id'   => $id,
            'apply_uid'   => UserSession::getUser('uid'),
            'status'      => 0,
        );
        $_d = array(
            'status'        => -2,
            'updatetime'    => time(),
        );
        //没有登录的用户禁止操作
        if(!UserSession::getUser('uid')){
            $this->ajaxReturn(array('status'=>-1,'content'=>'禁止操作'));
        }
        if(M('ConcurServiceApply')->where($_w)->save($_d)){
            //$this->addConcurApply($id,0);
            $this->ajaxReturn(array('status'=>1,'content'=>'success'));
        }else{
            $this->ajaxReturn(array('status'=>-1,'content'=>'操作失败'));
        }
    }
    
    
    
     /**
     * 修改服务申请记录
     * @param number $id 互助表id
     * @param number $type 0-撤销 1-已经申请等待审核 -1审核没有通过 2审核通过
     */
    private function addConcurApply($id,$type=1){
        return;
	$_w = array(
            'concur_id' => $id,
            'userid'    => UserSession::getUser('uid'),
        );
        $concurApply = M('ConcurApply')->where($_w)->find();
        $r = false;
        if($concurApply){
            $_d['service'] = $type;
            $r = M('ConcurApply')->where("id=%d",$concurApply['id'])->save($_d);
        }else{
            $_d = array(
                'service'  => $type,
                'concur_id' => $id,
                'userid'    => UserSession::getUser('uid'),
		'datetime'=> date('Y-m-d H:i:s'),
            );
            $r = M('ConcurApply')->add($_d);
        }
        $status = $r?1:-1;
        $content = $r?'':"添加服务申请记录失败";
        return array('status'=>$status,'content'=>$content);
    }
    
    
    /**
     * 在结束某个服务时，判断是否已经全部结束
     * @param $id concur表id
     */
    public function endConcur($id){
        //拒绝所有还没有通过请求的用户，拒绝理由：该互助项目已经结束
        $_wService = array(
            'concur_id' => $id,
            'status'    => 0,
        );
        $_dService['status'] = -1;
        $_dService['reason'] = '该项目已经结束';
        M('concurServiceApply')->where($_wService)->save($_dService);
        //获取互助项目信息
        $_w = array(
            'creator'=> UserSession::getUser('uid'),
            'id'     => $id,
        );
        $result = M('Concur')->where($_w)->find();
        //判断concur是否已经全部结束
        if($result && $result['money']<=0 && $result['is_supplies']<=0 && $result['is_service']<=0 ){
            //给互助项目的状态修改成结束
            $_d['status'] = 888;
            M('Concur')->where("id=%d",$id)->save($_d);
            return 1;
        }else{
            return 0;
        }
    }
    
    /**
     * 获取项目服务未处理的申请数目
     * @param type $id concur表id
     * @param type $sid service表id
     */
    public function getApplicationNumber($id,$sid){
        //判断查询的是否为自己发布的项目
        $_w = array(
            'id'=>$id,
            'creator'=>  UserSession::getUser('uid'),
        );
        if(!M('Concur')->where($_w)->count()){
            $this->ajaxReturn(array('status'=>1,'message'=>'操作失败'));
        }
        //还没有通过审核的请求数目
        $_applyW = array(
            'service_id'=> $sid,
            'concur_id' => $id,
            'status'    => 0,
        );
        $sum = M('ConcurServiceApply')->where($_applyW)->count();
        $this->ajaxReturn(array('status'=>1,'sum'=>$sum));
    }
    
}
