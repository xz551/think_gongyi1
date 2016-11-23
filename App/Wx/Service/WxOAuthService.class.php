<?php 
namespace Wx\Service;

/**
 * 用户授权
 *
 * @author Administrator
 */
class WxOAuthService {
    private $mp;
    public function __construct(){
        if(_mp_){
            $this->mp=M('Mp')->where("MpOriginalId='%s'",_mp_)->find();
        }
    }
    /**
     * 执行授权
     * @param type $redirect 授权后跳转的地址
     */
    public function auth($redirect,$scope='snsapi_userinfo'){
        $appId=$this->mp['AppId']?$this->mp['AppId']:C('WX_APPID');
        //$scope="snsapi_userinfo";//$this->mp['AppId']?"snsapi_userinfo":"snsapi_base";
        $url=  sprintf("https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=1#wechat_redirect",$appId,$redirect,$scope);
        redirect($url);
    }
    /**
     * 根据code获取access_token
     * @param type $code code值
     */
    public function accessToken($code){
        $appId=$this->mp['AppId']?$this->mp['AppId']:C('WX_APPID');
        $appSecret=$this->mp['AppId']?$this->mp['AppSecret']:C('WX_APPSECRET');
        $url="https://api.weixin.qq.com/sns/oauth2/access_token";
        $param=sprintf("appid=%s&secret=%s&code=%s&grant_type=authorization_code",$appId,$appSecret,$code);
        $result=get($url,$param);
        $obj=json_decode($result);
        if($obj->errcode){
            return $result;
        }else{
            return $obj;
        }
    }
    /**
     * 重新获取token
     */
    public function refreshToken($refresh_token){
        $url="https://api.weixin.qq.com/sns/oauth2/refresh_token";
        $param=  sprintf("appid=%s&grant_type=refresh_token&refresh_token=%s",$this->mp['AppId'],$refresh_token);
        $result=get($url,$param);
        $obj=  json_decode($result);
        if($obj->errcode){
            return $result;
        }else{
            return $obj;
        }
    }
    /**
     * 通过授权 后 获取用户基本信息
     */
    public function userinfo($access_token,$openid){
        $url="https://api.weixin.qq.com/sns/userinfo";
        $param=  sprintf("access_token=%s&openid=%s&lang=zh_CN",$access_token,$openid);
        $result=get($url,$param);
        $obj=  json_decode($result);
        if($obj->errcode){
            return $result;
        }else{
            return $obj;
        }
    }
}
