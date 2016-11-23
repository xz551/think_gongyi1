<?php
namespace UC\Model;
use Think\Model;
use Lib\UserSession;


class VolunteerResumeModel extends Model {

    protected $connection = 'USER_CONTER'; //数据库连接    
    //自动验证
    protected $_validate = array(
        array('start_time ', 'number', '开始时间必须为整数'),
        array('end_time ', 'number', '结束时间必须为整数'),
        array('provinceid', 'number', '省ID必须为整数'),
        array('cityid', 'number', ' 市ID必须为整数'),
        array('address ', 'require', '地址不能为空'),
        array('org_name ', 'require', '发起组织不能为空'),
        array('title ', 'require', '活动名称不能为空'),
        array('responsibility ', 'require', '主要职责不能为空'),
    );
    //自动完成
    protected $_auto = array(
        array('uid', 'getCreator', 1, 'callback'), //新增数据时，设置创建者ID
        array('create_date', 'time', 1, 'function'), //新增数据时，设置创建者ID
        array('update_date', 'time', 3, 'function'), // 新增或者修改时，都更改更新时间
        array('status', '0'), // 新增时设置状态为0-未认证
    );

    //自动完成时获取用户id
    protected function getCreator() {
        return UserSession::getUser('uid');
    }
   
    //获取履历
    public function resume($uid,$limit='0,5',$order='update_date desc'){
        return $this->where("uid=%d", $uid)->order($order)->limit($limit)->select();
    }
    
    //获取用户的履历数
    public function getNum($uid){
        return $this->where("uid=%d",$uid)->count();
    }
    
    //根据ID获取履历
    public function getResume($id){
        $map['uid']= UserSession::getUser('uid');
        $map['id'] = $id;
        return $this->where($map)->find();
    }
    
    
}