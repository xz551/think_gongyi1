<?php
namespace Lib;
use Lib\Image;
class User
{
    private static $user_session_key = 'user_session_key';
    private static $user_volunteer=null;
    private static $get_user=null;
    /**取得用户信息
     * @param string $id
     * @return mixed
     */
    public static function getUser($id = '')
    {
        if(self::$get_user!=null){
            return self::$get_user;
        }
        $uid = $id ? $id : I('uid');
        $key = self::$user_session_key . $uid;
        $user = S($key);
        if (true || !$user) {
            $user = api('/user/getById/uid/' . $uid);
            S($key, $user);
        }
        return self::$get_user=json_decode($user);
    }

    /**
     * 取得志愿者认证信息
     */
    public static function getVolunteer($id){
        if(!self::$user_volunteer){
            //查询志愿者信息
            self::$user_volunteer=D('T/Volunteer')->find($id);
        }
        return self::$user_volunteer;
    }
    public static function isUser($id = '')
    {
        return self::getUser($id)->type == 10 || self::getUser($id)->type == 11;
    }

    public static function isOrg($id = '')
    {
        return self::getUser($id)->type == 20 || self::getUser($id)->type == 21;
    }

    //判断是否认证的组织
    public static function isVipOrg($id=''){
    //    return self::getUser($id)->type == 21;
        if(self::getUser($id)->type == 21) {
            $user = api('/organization/getInfo', array('orgid' => $id));
            $user = json_decode($user);
            return $user->status == 1;
        }
        return false;
    }

    /**
     * 判断是否为认证用户
     * @param string $id
     * @return bool
     */
    public static function isVipUser($id=''){
        return self::getUser($id)->type == 11;
    }
    
    
     //判断是否为认证用户||组织
    public static function isVip($id)
    {
        return self::isVipOrg($id) || self::isVipUser($id);
    }
    //获取用户的nickname;
    public static function getNickname($id=''){
    	return self::getUser($id)->nickname;
    }
    //获取用户的type
    public static function getType($id=''){
    	return self::getUser($id)->type;
    }
    public static function getSex()
    {
        return '';
    }

    public static function getName()
    {

    }
    
    //获取用户头像
    public static function getPhoto($id=''){
        return Image::getUrl(self::getUser($id)->photo,array('60','60'),'user');
    }
    //保存用户信息
    public  function loginIn($username,$password,$login_type=0){
    	$api=json_decode(api(UCENTER.'/api/user/login',array('LoginForm[username]'=>$username,'LoginForm[password]'=>$password,'login_type'=>$login_type)));
    	if($api->status){
    		$key=$api->data->cookie_key;
    		$v=$api->data->cookie;
    		cookie($key,$v);
    		return true;
    	}else{
    		return false;
    	}	
    }

    /**检查某用户是否存在 邮箱、用户名登陆信息
     * @param $uid
     */
    public static function exists_login($uid){
        $user=self::getUser($uid);
        $user=is_object($user)?get_object_vars($user):$user;
        return
            !empty($user) &&
            (
                !empty($user['password'] ) && ( !empty($user['email']) || !empty($user['phone'])  )
            );
    }
    
}
