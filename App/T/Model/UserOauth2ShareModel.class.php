<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;
class UserOauth2ShareModel extends Model{
    protected $connection =  'USER_CONTER'; //数据库连接
    /**
     * 分享类型
     * 0 无标识 无效
     * 1 分享项目
     * 2 分享反馈/评论
     * 5分享小组话题
     * 6分享求助或资源
     */
    const SHARE_TYPE_PROJECT=1;
    const SHARE_TYPE_FEEDBACK=2;
    const SHARE_TYPE_SUBJECT=5;
    const SHARE_TYPE_DONATE=6;
    
    public function shareCount($type){
    	$w=array(
    			'relation_type'=>$type,
    			'uid'=>UserSession::getUser('uid')
    	);
    	return $this->where($w)->count();
    }
    
}
