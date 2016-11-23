<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/9/18
 * Time: 13:51
 */

namespace Uc\Business;
use Org\Util\String;
use Lib\UserSession;

/**与微信相关的操作
 * 微信登陆 微信绑定
 * Class WxBusiness
 * @package Uc\Business
 */
class WxBusiness
{

    const wx_bind_code_session='wx_bind_code_session';

    /**获取绑定的code
     * @param int $uid 没有传值表示 获取的是登录操作。有表示 获取的为绑定操作
     * @return bool|string
     */
    public static function bind_code($uid=0){
        if(session(self::wx_bind_code_session)){
            //return session(self::wx_bind_code_session);
        }
        $code=String::randString(6,1);
        //检查code是否已经存在
        $c=D('WxUser')->where(array('login_code'=>$code,'status'=>1))->count();
        if($c>0){
            return self::bind_code();
        }else{
            $data=array(
                'login_code'=>$code,
                'uid'=>$uid,
                'status'=>1,
                'update_date'=>time()
            );
            if(!empty($uid)){
                //查找是否用没有使用的code
                $login_code=D('WxLogin')->where('uid=%d and `status`=1 and ( openid is null or openid="")',$uid)->find();
            }else{
                $login_code=false;
            }
            if($login_code){
                //更新 code
                $data['id']=$login_code['id'];
                $result=D('WxLogin')->save($data);
            }else{
                $data['create_date']=time();
                $result=D('WxLogin')->add($data);
            }

            return $result && ( session(self::wx_bind_code_session,$code) || !0)?$code:false;
        }
    }
    public static function clear_code(){
        session(self::wx_bind_code_session,null);
    }
}