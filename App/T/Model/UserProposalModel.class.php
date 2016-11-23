<?php

namespace T\Model;
use Think\Model;
use Lib\UserSession;

class UserProposalModel extends Model{
    //自动验证
    protected $_validate = array(
        array('content','require','反馈内容不能为空'),
        array('contact','require','联系方式不能为空')
    );
    
   
    //自动完成
    protected $_auto = array (
        array('uid','getCreator',1,'callback') , // 新增时候，设置创建时间
        array('create_date','time',1,'function'), // 新增或者修改时，都更改 更新时间
    );
    //自动完成时获取用户id
    protected function getCreator(){
        return UserSession::getUser('uid');
    }
}

