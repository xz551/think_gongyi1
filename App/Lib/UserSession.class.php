<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/12/16
 * Time: 14:20
 */

namespace Lib;


class UserSession {
       /**验证数据的有效性
     * @param $data
     * @param null $key
     * @return bool
     */
    private static function validateData($data,$key=null)
    {

        $len=mb_strlen(self::computeHMAC('test'),'8bit');
        if( mb_strlen($data,'8bit')>=$len )
        {
            $hmac=mb_substr($data,0,$len,'8bit');

            $data2=mb_substr($data,$len,mb_strlen($data,'8bit'),'8bit');

            return $hmac===self::computeHMAC($data2,$key)?$data2:false;
        }
        else
            return false;
    }

    /**保存用户登录
     * @param $uid
     */
    public static function login($uid,$time=0){
        $data=D('User')->find($uid);
	if($data['type'] == 21){
		$org = D('Organization')->find($uid);
		$data['organization_status'] = $org['status']; 
	}
        $cookie_data=array(
            $data['uid'],
            $data['nickname'],
            30*24*60*60,
            $data,
        );
        $user_cookie=self::hashData(serialize($cookie_data));
        cookie(UCENTERKEY,$user_cookie,$time);
    }
    /**保存cookie数据安全
     * @param $data
     * @param null $key
     * @return string
     */
    private static function hashData($data,$key=null)
    {
        return self::computeHMAC($data,$key).$data;
    }

    /**保存cookie数据安全
     * @param $data
     * @param null $key
     * @param null $hashAlgorithm
     * @return string
     */
    private static  function computeHMAC($data,$key=null,$hashAlgorithm=null)
    {
        if($key===null)
            $key=validationkey;
        if($hashAlgorithm===null)
            $hashAlgorithm="sha1";

        if(function_exists('hash_hmac'))
            return hash_hmac($hashAlgorithm,$data,$key);

        if(0===strcasecmp($hashAlgorithm,'sha1'))
        {
            $pack='H40';
            $func='sha1';
        }
        else
        {
            $pack='H32';
            $func='md5';
        }
        if(mb_strlen($key,'8bit')>64)
            $key=pack($pack,$func($key));
        if(mb_strlen($key,'8bit')<64)
            $key=str_pad($key,64,chr(0));
        $key=mb_substr($key,0,64,'8bit');

        return $func((str_repeat(chr(0x5C), 64) ^ $key) . pack($pack, $func((str_repeat(chr(0x36), 64) ^ $key) . $data)));
    }
    private static  $_User;

    /**取得当前登录用户
     * @param string $key 传值代表取得对应的属性值
     * @return mixed|null
     */
    public static  function getUser($key=''){
        if(self::$_User ==null) {
            $c = cookie(UCENTERKEY);
            $data =self:: validateData($c);
            if ($data) {
                $user=@unserialize($data);
                $uid=$user[0];
                if(empty($uid)){
                    return false;
                }
                self::$_User=D('User')->where('uid='.$uid)->find();
                self::$_User['userid']=self::$_User['uid'];
            }
        }
        return self::$_User==null?null:( $key? self::$_User[$key]: self::$_User );
    }

    /**
     * 取得当前登录用户的头像
     */
    public static function getPhoto(){
        return self::getUser('photo');
    }
    public static function getMail(){
        return self::getUser('email');
    }
    public static function getUserName(){
        return self::getUser('nickname');
    }
    /**
     * 判断是否登录
     */
    public static function islogin(){
        return self::getUser()!=null;
    }

    /**
     * 取得登录用户编号
     */
    public static function getUserId(){
        return self::getUser('uid');
    }
 
    const phone_login_verify_status_key="phone_login_verify_status_key";
    
    public function set_phone_login_verify_status($phone=1){
    	session(self::phone_login_verify_status_key,$phone);
    }
    public function get_phone_login_verify_status(){
    	return session(self::phone_login_verify_status_key);
    }
}