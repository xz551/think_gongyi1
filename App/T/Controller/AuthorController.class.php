<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/9/2
 * Time: 14:45
 */

namespace T\Controller;


use Lib\QC;
use Lib\Recorder;
use Lib\URL;
use Lib\ErrorCase;
use Lib\User;
use Org\Util\String;
use Lib\UserSession;
use Think\Controller;

/**QQ登录授权 操作类
 * Class AuthorController
 * @package T\Controller
 */
class AuthorController extends Controller
{
    const VERSION = "2.0";
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";
    const LOGIN_RETURN_URL='LOGIN_RETURN_URL';
    const AUTHOR_TYPE_KEY='AUTHOR_TYPE_KEY';

    protected $recorder;
    public $urlUtils;
    protected $qq_error;


    /**
     * 构造
     */
    function _initialize(){
        $this->recorder = new Recorder();
        $this->urlUtils = new URL();
        $this->qq_error = new ErrorCase();
    }

    /**
     * 登录触发
     */
    public function qq_login($returnurl= '',$t=0){
        $returnurl=empty($returnurl)?urlSafeBase64_encode('/'):$returnurl;
        $appid = $this->recorder->readInc("appid");
        $callback = $this->recorder->readInc("callback");
        $scope = $this->recorder->readInc("scope");

        //-------生成唯一随机串防CSRF攻击
        $state = md5(uniqid(rand(), TRUE));
        $this->recorder->write('state',$state);

        //-------构造请求参数列表
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $appid,
            "redirect_uri" => $callback,
            "state" => $state,
            "scope" => $scope
        );

        $login_url =  $this->urlUtils->combineURL(self::GET_AUTH_CODE_URL, $keysArr);
        session(self::LOGIN_RETURN_URL,$returnurl);
        session(self::AUTHOR_TYPE_KEY,$t);
        header("Location:$login_url");
    }

    /**登录回掉
     * @return mixed
     */
    public function qq_callback(){
        $state = $this->recorder->read("state");

        //--------验证state防止CSRF攻击
        if($_GET['state'] != $state){
            $this->error($this->qq_error->getError("30001"));
        }

        //-------请求参数列表
        $keysArr = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->recorder->readInc("appid"),
            "redirect_uri" => urlencode($this->recorder->readInc("callback")),
            "client_secret" => $this->recorder->readInc("appkey"),
            "code" => $_GET['code']
        );

        //------构造请求access_token的url
        $token_url = $this->urlUtils->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response = $this->urlUtils->get_contents($token_url);

        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);

            if(isset($msg->error)){
                $error_msg=$this->qq_error->getError($msg->error);
                if(empty($error_msg) && isset($msg->error_description)){
                    $this->error($msg->error_description);
                }else{
                    $this->error('获取错误');
                }
            }
        }

        $params = array();
        parse_str($response, $params);

        $this->recorder->write("access_token", $params["access_token"]);
        //return $params["access_token"];
        //获取用户基本信息
        $access_token=$params["access_token"];
        $openid=$this->get_openid();
        $q=new QC($access_token,$openid);
        $user_info=$q->get_user_info();
        $user_obj=is_string($user_info) ? json_decode($user_info) : $user_info;
        $reg=is_object($user_obj) ? $user_obj->reg:$user_obj['reg'];
        $msg=is_object($user_obj) ? $user_obj->msg:$user_obj['msg']; 
        if(!empty($reg)){
            $this->error($msg);
        }else{
            $t=session(self::AUTHOR_TYPE_KEY);
            if($t==0){
                $result_uid=D('QqUser')->edit($openid,$user_obj);
                if(!$result_uid){
                    $this->error('更新用户信息失败');
                }else{
                    //保存用户登录状态
                    \Lib\UserSession::login($result_uid);
                    //跳转
                    $this->redirect(urlSafeBase64_decode(session(self::LOGIN_RETURN_URL)));
                }
            }else{
                //绑定
                $result=D('QqUser')->bind($openid,$user_obj);
                if(is_string($result)){
                    $this->error($result,WEB_SITE.'/uc/accountinfo/bind.html');
                }else if(!$result){
                    $this->error('绑定失败',WEB_SITE.'/uc/accountinfo/bind.html');
                }
                $this->redirect(urlSafeBase64_decode(session(self::LOGIN_RETURN_URL)));
            }

        }
    }

    /**取得登录用户openid
     * @return mixed
     */
    private function get_openid(){

        //-------请求参数列表
        $keysArr = array(
            "access_token" => $this->recorder->read("access_token")
        );

        $graph_url = $this->urlUtils->combineURL(self::GET_OPENID_URL, $keysArr);
        $response = $this->urlUtils->get_contents($graph_url);

        //--------检测错误是否发生
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($response);
        if(isset($user->error)){
            $this->error($this->qq_error->getError($user->error));
        }

        //------记录openid
        $this->recorder->write("openid", $user->openid);
        return $user->openid;
    }


    /**
     * 解除绑定
     */
    public function unbind($returnurl=''){
        if(!User::exists_login(UserSession::getUserId())){
            $this->error('请设置登录账号');
        }else{
            //解除绑定
            D('QqUser')->unbind();
            $this->redirect(urlSafeBase64_decode($returnurl));
        }
    }
}