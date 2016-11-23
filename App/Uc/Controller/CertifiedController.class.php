<?php
namespace Uc\Controller;
use Think\Controller;
use Lib\UserSession;
use Lib\Image;
use M\Controller\CodeController;
class CertifiedController extends Controller{
    /**
     * 用户认证
     */
    public function index(){
        tag('user_login');
        layout(false);
        //检测登录用户类型
        $check = $this->checkUserType();
        if($check['status'] == -1){
            $this->error($check['msg']);
        }
        //获取关注领域列表
        $field = D('CategoryServer')->getAllLabel();
        $this->assign('field',$field);
        //获取用户的关注领域                       
        $interest = json_decode(api("/User/getUserInterest",array('uid'=>  UserSession::getUser('uid'))));
        $interestlist = ',';
        foreach($interest as $k=>$v){
            $interestlist .= $k.','; 
        }
        $interestlist = trim($interestlist,',');
        $this->assign('interestlist',$interestlist);
        //获取用户的技能标签
        $list = D('UserAbilityTag')->getField();
        $this->assign('list',$list);
        //获取用户表基本信息
        $user = D('User')->find(UserSession::getUser('uid'));
        //获取用户认证信息
        $vol = D('Volunteer')->find(UserSession::getUser('uid'));
        $vol['imgurl'] = \Lib\Image::getUrl($vol['idcard_file_name']);
        $this->assign('user',$user);
        $this->assign('vol',$vol);
        $this->display();
    } 
    
    public function handle(){
         tag('user_login');
        //手机验证码验证
        $result=CodeController::check_verify($ip_yzcode);
        if(!$result){
                $this->error("手机验证码错误");
        } 
        //检测登录用户类型
        $check = $this->checkUserType();
        if($check['status'] == -1){
            $this->error($check['msg']);
        } 
        //创建认证表数据
      	$data = D('Volunteer')->create();
        if(!$data){
		$this->error(D('Volunteer')->getError());
	}
        //更新或添加认证数据
        if(D('Volunteer')->find(UserSession::getUser('uid'))){
	    if(!D('Volunteer')->where("uid=%d",UserSession::getUser('uid'))->save($data)){
	    	$this->error("更新失败");
	    }
        }else{
            if(!D('Volunteer')->add($data)){
	    	$this->error("认证提交失败");
	    }
        }
        //更新用户表信息（性别，地址）
        $userData['gender'] = intval(I('ipt_gender'));
        $userData['provinceid'] = intval(I('s_province'));
        $userData['cityid'] = intval(I('s_city'));
        $userData['countyid'] = intval(I('s_area'));
        $userData['address'] = I('smrz_addr');
        D('User')->where("uid=%d",  UserSession::getUser('uid'))->save($userData);
        
        //设置用户的关注领域和技能标签
        $r = $this->setUserLabel();      
        if($r['status'] == -1){
		$this->error($r['info']);
        }else{
            $this->success("认证提交成功");
        }
    }
    
    //检测登录用户类型
    private function checkUserType(){
    	return array('status'=>1);
        $usertype = UserSession::getUser('type');
        if($usertype != 10){
            if($usertype == 11){
                return array('status'=>-1,'msg'=>'通过认证的用户不可再次提交认证申请');
            }else{
                return array('status'=>-1,'msg'=>'这里不可进行组织认证');
            }
        }
        return array('status'=>1);
    }
    
     /**
     * 设置用户的关注领域和技能标签
     */
    private function setUserLabel(){
        $ly_label  = rtrim(I('ly_label'),',');  //获取领域标签
        //关注领域验证
        $lyArr = array(1,2,3,4,5,6,7,8,9,10,11);
        $ly = explode(',',$ly_label);
        foreach($ly as $val){
            if(!in_array($val,$lyArr))  return $this->return_error("关注领域含有非法字符");
        }
        //技能标签验证
        $jn_label  = trim(trim(I('jn_label')),',');  //获取技能标签
        $jn =  explode(',',$jn_label);
        foreach($jn as $v){
            if(!preg_match("/^[^~!@#\<\>\$\%\^&\*\(\)\+]{1,}$/",$v))    return $this->return_error("技能标签含有非法字符");
        }
        $lable = D('UserAbilityTag');
        $lable->startTrans();
        //删除用户已存在的关注领域 
        if(!D('UserCategoryServerTagList')->delLabel()){
            $lable->rollback();
            return $this->return_error("初始化用户关注领域失败");
        }
        //重新添加用户的关注领域
        if(!D('UserCategoryServerTagList')->addLabel($ly)){
            $lable->rollback();
            return $this->return_error("添加用户关注领域失败");
        }        
        //检测技能标签是否存在,存在则返回技能标签id，不存在则添加并返回id
        $jnList = D('CategoryTag')->checkLabel($jn);
        if(!$jnList){
            $lable->rollback();
            return $this->return_error("技能标签处理失败");
        }
        //删除用户已有的技能标签
        if(!D('UserAbilityTag')->delLabel()){
            $lable->rollback();
            return $this->return_error("用户技能标签初始化失败");
        }
        //重新添加用户的技能标签
        if(!D('UserAbilityTag')->addLabel($jnList)){
            $lable->rollback();
            return $this->return_error("用户技能标签添加失败");
        }
	
        $lable->commit();
        return array('status'=>1);
    }
    
    private function return_error($msg = ''){
        return array('status' => -1, 'msg' => $msg);
    }
    
}
