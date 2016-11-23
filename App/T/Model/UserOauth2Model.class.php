<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;
class UserOauth2Model extends Model{
    protected $connection =  'USER_CONTER'; //数据库连接
    
    /**
     * 获取当前登录用户已经绑定的三方类型
     */
    public function getBindingType(){
        $map['uid'] = UserSession::getUser('uid');
        $map['status'] = 0;               
        $r = $this->where($map)->select();
        $list = array();
        foreach($r as $v){
            if($v['type'] == 1){
                $list['qq'] = 1;
            }elseif($v['type'] == 2){
                $list['weibo'] = 1;
            }elseif($v['type'] == 3){
                $list['renren'] = 1;
            }
        }
        return $list;
    }
    
    
    
    
    
}
