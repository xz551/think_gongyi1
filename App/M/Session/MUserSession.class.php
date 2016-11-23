<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/18
 * Time: 14:27
 */

namespace M\Session;


use Lib\UserSession;

class MUserSession
{
    /**
     * 微信授权登录
     */
    const LOGIN_TYPE_WEIXIN='1';
    /**
     * 微博 人人 授权登录
     */
    const LOGIN_TYPE_OAUTH='2';
    /**
     * 公益帐号登陆
     */
    const LOGIN_TYPE_GONGYI='0';
    private static $_user=null;
    public static function getUser(){
        if(self::$_user==null) {
            $uid = self::getUserId();
            if (!$uid) {
                return null;
            }
            $user_json = api('/User/getById/uid/' . $uid);
            self::$_user= json_decode($user_json);
        }
        return self::$_user;
    }

    public static function getUserId(){
        $userid_cookie=cookie(self::user_cookie);
        if(!$userid_cookie){

            return null;
        }
        $userid=unserialize(urlSafeBase64_decode( $userid_cookie)); 
        return $userid['uid'];
    }

    public static function islogin(){
        return self::getUserId()!=null;
    }

    const user_cookie='muser_login';
    public static function login($uid){
        $v=urlSafeBase64_encode( serialize(['uid'=>$uid]));
        cookie(self::user_cookie,$v,array('expire'=>60*60*24*30,'COOKIE_DOMAIN'=>DOMAIN));
    }
    public static function loginout(){
        self::setLoginType(null);
        self::login(null);
        self::setRegPhone(null);
    }

    const login_type_cookie_type='muser_login_type';
    /**
     * 设置登录类型
     * @param $type
     */
    public static function setLoginType($type){
        $type=urlSafeBase64_encode($type);
        cookie(self::login_type_cookie_type,$type,array('expire'=>60*60*24*30,'COOKIE_DOMAIN'=>DOMAIN));
    }

    /**
     * 取得登陆方式
     * @return string
     */
    public static function getLoginType(){
        return urlSafeBase64_decode(cookie(self::login_type_cookie_type));
    }

    const RETURNURL='RETURNURL';
    /**
     *
     * @param $returnurl
     */
    public static function setReturnUrl($returnurl){
        session(self::RETURNURL,$returnurl);
    }
    public static function getReturnUrl(){
        $url= session(self::RETURNURL);
        $url=empty($url)?'':( stripos($url.'/')===false?urlSafeBase64_decode($url):$url);
        if(empty($url) || ( stripos($url,WEB_SITE.'/m/')===false && stripos($url,'/m/')===false ) ){
            $url='/m/user';
        }
        return $url;
    }

    const reg_phone_key='reg_phone_key';
    /**要注册的手机号
     * @param $phone 手机号
     */
    public static function setRegPhone($phone){
        session(self::reg_phone_key,$phone);
    }
    public static function getRegPhone(){
        return session(self::reg_phone_key);
    }

    /**
     * 是否是志愿者
     */
    public static function is_vol(){
        $user=D('User')->find(MUserSession::getUserId());
        return $user['type']==10 ||  $user['type']==11;
    }
}
