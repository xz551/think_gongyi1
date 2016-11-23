<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wx\Service;
use Lib\Session\UserSession;
use Common\SysCrypt;

/**
 *微信用户服务
 *
 * @author Administrator
 */
class WxUserService extends \Think\Model{
    var $accessToken; 
    function __construct($mp) {
        if(!$this->accessToken){
            $this->accessToken=D('WxAccessToken','Service')->AccessToken($mp);
        }
    }
    /**
     * 获取用户基本信息
     * @param type $openId
     * @return type
     */
    public function userInfo($openId){ 
        $url="https://api.weixin.qq.com/cgi-bin/user/info";
        $param=  sprintf("access_token=%s&openid=%s&lang=zh_CN",$this->accessToken,$openId); 
        $res=get($url,$param); 
        //test($res);
        return json_decode($res);
    }
    public function login($postObject){
        if(!UserSession::getOpenId()){
            $w['openid']=$postObject->FromUserName;
            $w['ToUserName']=$postObject->ToUserName;
            $wx_user=M('wx_user')->where($w)->find();
            if(!$wx_user){
                $newid=D('WxUser')->addUser($postObject->FromUserName,$postObject->ToUserName);
                $wx_user=M('wx_user')->find($newid);
            }else{
                $nickname=$wx_user['nickname'];
                if(!$nickname){
                    //更新
                    $wxUserObject=$this->userInfo($postObject->FromUserName);
                    $uData=D('WxUser')->wxUserData($wxUserObject);
                    M('wx_user')->where($w)->save($uData);
                }
            } 
	    UserSession::UserLogin($wx_user);
        }
    }
    /**
     * 取得所有关注者
     * @param type $next_openid
     * @return type
     */
    public function SubscribeUsers($next_openid=''){
        //https://api.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN&next_openid=NEXT_OPENID
        $url="https://api.weixin.qq.com/cgi-bin/user/get";
        $param=  sprintf("access_token=%s&next_openid=%s",$this->accessToken,$next_openid); 
        $res=get($url,$param);         
        return json_decode($res);
    }
}
