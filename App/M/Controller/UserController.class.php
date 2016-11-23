<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/14
 * Time: 16:00
 */

namespace M\Controller;


use M\Session\MUserSession;
use Think\Controller;


/**
 * Class UserController
 * 手机版个人中心操作
 * @package M\Controller
 */
class UserController extends Controller
{
    /**
     * 个人中心首页
     */
    public function index(){ 
        tag("m_user_login");
        $this->title='我的主页';
        $this->display('index');
    }

    /**
     * 我的项目
     */
    public function project($p=1,$pagesize=20){
        layout(!IS_AJAX);
        $this->title='我的项目';
        $uid=MUserSession::getUserId();
        $data=D('Project')->getProjectJoin($uid,$p,$pagesize);
        $this->data=$data;
        $this->display(IS_AJAX?"project_ajax":"project");
    }
    public function active($p=1,$pagesize=20){
        layout(!IS_AJAX);
        $this->title='我的活动';
        $uid=MUserSession::getUserId();
        $data=D('Active')->getActiveJoin($uid,$p,$pagesize);
        $this->data=$data;
        $this->display(IS_AJAX?"active_ajax":"active");
    }
    public function login($login=0,$returnurl='/m/user'){
        if($login==1){
            //重新登录
            MUserSession::loginout();
        }
        $back_url=WEB_SITE. '/m/auth/index';
        MUserSession::setReturnUrl($returnurl);
        $this->returnurl=$returnurl;
        $returnurl=urlSafeBase64_encode($back_url);
        $this->weibo=UCENTER.'/oauth/auth/provider/weibo.html?returnurl='.$returnurl;
        $this->tweibo=UCENTER.'/oauth/auth/provider/qq.html?returnurl='.$returnurl;
        $this->renren=UCENTER.'/oauth/auth/provider/renren.html?returnurl='.$returnurl;
        $this->weixin=WX_API_URL.'/Auth/index?redirect='.urlencode($back_url);
        $this->title='登录';
        $this->display('login');
    }
    public function logindo($type=''){
        $login_url=UCENTER.'/api/user/login';
        $result=getApiContent($login_url,I('post.')); 
        if($result['status']==true && type!=='bind'){
            //登录成功 
            MUserSession::login($result['data']['uid']);
	     
            MUserSession::setLoginType(MUserSession::LOGIN_TYPE_GONGYI);
        }
        echo json_encode($result);
    }
    public function loginout(){
        MUserSession::loginout();
        $this->redirect('Index/index');
    }
}