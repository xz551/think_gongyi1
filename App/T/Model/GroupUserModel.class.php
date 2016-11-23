<?php
namespace T\Model;
use Think\Model;
class GroupUserModel extends Model{
	protected $connection =  'USER_CONTER'; //数据库连接
     //获取小组所有用户的信息
     public function allUserInfo($gid){
        $prefix =  C('DB_PREFIX');
        $joinTab = $prefix.'group_user';
        $userTab = $prefix.'user';
        $sql = "select $joinTab.uid,$userTab.photo from $joinTab "
                . " left join $userTab on $joinTab.uid=$userTab.uid "
                . " where $joinTab.gid=$gid order by $joinTab.time desc limit 0,10";
       return $this->query($sql); 
     }
     
    //获取用户所在小组的数量 
    public function groupNum($uid){
            return $this->where("uid=%d",$uid)->count();
    }
    
       //删除小组成员
    public function delUser($gid,$uid){
        $map['uid'] = $uid;
        $map['gid'] = $gid;
        $map['rank'] = 0;
        if($this->where($map)->delete()){    //如果删除成功，小组权重减1
            $prefix = C('DB_PREFIX');
            $sql = "update " . $prefix . "group set weight = weight-1 where id=$gid";
            $this->execute($sql);
            return 1;
        }
    }
    
    //将创建者添加进小组和用户的关系表
    public function addCreator($gid,$uid){
        $data['uid'] = $uid;
        $data['gid'] = $gid;
        $data['time'] = time();
        $data['rank'] = 1;
        $this->add($data);
    }
    
}