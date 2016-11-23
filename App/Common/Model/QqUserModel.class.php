<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/9/11
 * Time: 13:17
 */

namespace Common\Model;


use Lib\User;
use Lib\UserSession;
use Think\Model;

/**qq用户
 * Class QqUser
 * @package Common\Model
 */
class QqUserModel extends Model
{
    protected $connection =  'USER_CONTER'; //数据库连接

    /**编辑 更新 新增 qq用户基本信息
     * @param $user_info
     */
    public function edit($openid,$user_info){
        $data=is_object($user_info)?get_object_vars($user_info):$user_info;
        $data['openid']=$openid;
        $data['update_date']=time();
        //检查是否存在openid
        $qq_user=$this->where(array('openid'=>$openid,'status'=>1))->find();
        if($qq_user){
            //更新
            $data['id']=$qq_user['id'];
            $result=$this->save($data);
            return ( $result && $this->update_user($qq_user['uid'],$data) )?$qq_user['uid']:false;
        }else{
            //新增
            $data['uid']=$this->create_user($data);
            $data['create_date']=time();
            return $this->add($data)!==false ?$data['uid']:false ;
        }
    }

    /**
     * 绑定qq账号
     */
    public function bind($openid,$user_info){
        $data=is_object($user_info)?get_object_vars($user_info):$user_info;
        $qq_user=$this->where(array('openid'=>$openid))->find();
        if(empty($qq_user) || $qq_user['status']==-1 || $qq_user['uid']==UserSession::getUserId() ){
            //执行绑定
            $data['openid']=$openid;
            $data['update_date']=time();
            $data['status']=1;
            $data['uid']=UserSession::getUserId();
            if($qq_user) {
                //更新
                $data['id']=$qq_user['id'];
                $result=$this->save($data);
            }else{
                //新增
                $data['create_date']=time();
                $result=$this->add($data);
            }
            return $result;
        }
        return '该三方帐号已经被其他中青公益帐号绑定';
    }

    /**
     * 登录账号 解除qq绑定
     */
    public function unbind(){
        return $this->where('uid=%d',UserSession::getUserId())->save(array(
            'update_date'=>time(),
            'status'=>-1
        ));
    }
    /**
     * 创建一个新的用户
     */
    private function create_user($data){
        $user_data=array(
            'nickname'=>$data['nickname'],
            'photo'=>$data['figureurl_qq_2'],
            'gender'=>$data['gender']=='男'?1:2,
            'create_date'=>time(),
            'update_date'=>time(),
            'source'=>'qq'
        );
        return D('User')->add($user_data);
    }

    /**更新用户基本信息
     * @param $data
     */
    private function update_user($uid,$data){
        if(!User::exists_login($uid)){
            //没有绑定用户 更新
            $user_data=array(
                'nickname'=>$data['nickname'],
                'photo'=>$data['figureurl_2'],
                'gender'=>$data['gender']=='男'?1:2,
                'update_date'=>time(),
                'uid'=>$uid
            );
            return D('User')->save($user_data);
        }
        return true;
    }
}