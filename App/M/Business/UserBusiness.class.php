<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/19
 * Time: 16:47
 */

namespace M\Business;

use Lib\Idcard;
use M\Session\MUserSession;
use T\Model\UserModel;

/**
 * 用户信息操作
 * Class UserBusiness
 * @package M\Business
 */
class UserBusiness
{

    /**
     * 根据openid查询用户编号
     * @param $openid
     */
    public static function getUidByOpenId($openid)
    {
        $wxuser = D('WxUser')->where(array('openid' => $openid))->find();
        if ($wxuser != false) {
            //不存在
            $uid = self::setUserByWxUser($wxuser);

        } else { 
            $uid = null;
        }
        return $uid;
    }

    /**
     * 向user表中新增一条记录
     */
    private static function setUserByWxUser($wxuser)
    {
        //昵称 性别 头像 保存到用户表 创建uid
        $data = array(
            'nickname' => $wxuser['nickname'],
            'gender' => $wxuser['sex'],
            'photo' => $wxuser['headimgurl'],
            'type' => 10,//普通用户
            'status' => 0,//0 邮箱未验证 登录状态正常 1 邮箱已验证 状态正常  -1 用户被屏蔽
            'create_date' => time(),
            'update_date' => time(),
            'web_id' => 0

        );
        $uid=$wxuser['uid'];
        if(!empty($uid) && empty($wxuser['olduid'])){
            //更新
            unset($data['create_date']);
            D('User')->where('uid=%d',$wxuser['uid'])->save($data);
        }else{
            $uid = D('User')->add($data);
            //修改wx_user 绑定userid
            D('WxUser')->where(array('openid' => $wxuser['openid']))->setField('uid', $uid);
        }
        return $uid;
    }

    /**
     * 执行帐号绑定
     * @param int $uid 要绑定的用户编号
     * @param int $type 登陆类型
     */
    public static function bind($uid=0,$type=''){

        $m=$type==MUserSession::LOGIN_TYPE_WEIXIN?'WxUser':(MUserSession::LOGIN_TYPE_OAUTH?'T/UserOauth2':'');
        if(empty($m)){
            return '登陆状态不允许执行绑定';
        }
        //查找目前登陆的用户编号对应的帐号信息
        $oath=D($m)->where("uid=%d and status=0",MUserSession::getUserId())->find();
        if(!$oath){
            return '授权信息不存在';
        }
        M()->startTrans();
        //更改绑定的id
        $result=D($m)->where('id=%d',$oath['id'])->save(array('uid'=>$uid,'olduid'=>MUserSession::getUserId()));
        if(!$result){
            M()->rollback();
            return '绑定出错';
        }
        M()->commit();
        return true;
    }

    /**参与项目时 更新用户认证信息
     * @param $uname
     * @param $idcard
     * @return string
     */
    public static function save_vol($uname,$idcard){
        $vol=D('T/Volunteer')->find(MUserSession::getUserId());
        $user_vol_lock=false;
        if($vol){
            //填写过 检查是否审核通过
            $user_vol_lock=$vol['status']==1;
        }
        if(!$user_vol_lock){
            //用户信息还没有审核通过  检查用户传递过来的信息
            if (!verify_name($uname)) {
                return ('昵称必须2到10位合法字符');
            }
            //验证身份证
            if(!Idcard::idcard_checksum($idcard)){
                return ('身份证号错误');
            }
            //验证身份证是否已经被使用
            $c=D('T/Volunteer')->where("idcard_code='%s' and status <> -1 and uid <> %d",$idcard,MUserSession::getUserId())->count();
            if($c>0){
                return ('身份证已经被使用');
            }
            //更新用户验证信息
            $data=array(
                'real_name'=>$uname,
                'idcard_code'=>$idcard,
                'status'=>10,
                'update_date'=>time()
            );
            if($vol){
                //更新
                $result=D('T/Volunteer')->where('uid=%d',$vol['uid'])->save($data);
            }else{
                //新增
                $data['uid']=MUserSession::getUserId();
                $data['apply_date']=time();
                $result=D('T/Volunteer')->add($data);
            }
            if(!$result){
                return ('保存用户信息失败');
            }
        }
        return true;
    }

    /**
     * 参与项目时 更新用户基本信息
     */
    public static function save_user(){
        $user=D('user')->getUser(MUserSession::getUserId());
        $user_phone_lock=$user['phone_status']==1;
        if(!$user_phone_lock){
            //手机号未被验证
            $phone=$user['phone'];
            if ($phone==null || !verify_phone($phone)) {
                return ("手机号错误");
            }
            //更新用户手机号
            $result=D('User')->where("uid=%d and phone <> '%s' ",MUserSession::getUserId(),MUserSession::getRegPhone())->save(array(
                'phone'=>MUserSession::getRegPhone(),
                'phone_status'=>1,
                'update_date'=>time()
            ));
            if(!$result){
                return '保存用户手机时出错';
            }
        }
        return true;
    }
}