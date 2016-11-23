<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/24
 * Time: 10:06
 */

namespace M\Business;


class RegBusiness
{
    public static function verify_phone($phone){
        /**
         * 判断前面的操作是否已经验证通过
         */
        session('verify_success',null);
        $result=D('User')->where("phone='%s' ",$phone)->find();
        if($result){
           return array('status'=>-1,'msg'=>'手机号已经存在');
        }else{
            return array('status'=>1,'msg'=>'手机号不存在');
        }
    }
}