<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;
class GroupModel extends Model
{
    protected $connection =  'USER_CONTER'; //数据库连接
    
        
    //自动验证
    protected $_validate = array(     
                               array('name','/^[^~!@#\<\>\$\%\^&\*\(\)\+]{2,}$/','标题有非法字符'),
                               array('label','require','标签不能为空'),
                               array('image','require','图片不能为空'),                          
                               array('name','2,30','标题请输入2-30个合法字符',0,'length'),
                               array('introduce','10,50565','评论内容为10个以上字符',0,'length'),
                            );

    //自动完成
    protected $_auto = array (
        array('creator','getCreator',1,'callback'), //新增数据时，设置创建者ID
        array('addtime','time',1,'function') , // 新增时候，设置创建时间
        array('updatetime','time',3,'function'), // 新增或者修改时，都更改 更新时间
    );
    
    //自动完成时获取用户id
    protected function getCreator(){
        return UserSession::getUser('uid');
    }

    /*
     * 获取用户所在的所有小组的信息
     * type 1-所有的的公益小组，2-用户创建的公益小组
     */
    public function getUserGroupInfo($p,$pageNum,$type=1,$uid){
        $start = ($p-1)*$pageNum;
        $limit = "limit $start,$pageNum";
        if($type==2){
            $where = " && $groupUserTab.rank=1";
        }
        $prefix =  C('DB_PREFIX');
        $groupUserTab = $prefix.'group_user';
        $groupTab = $prefix.'group';
        $sql = "select $groupTab.* from $groupUserTab "
                . " left join $groupTab on $groupUserTab.gid=$groupTab.id"
                . " where $groupUserTab.uid=$uid $where order by $groupUserTab.time desc $limit";
        return $this->query($sql);
    }
    
    //根据组ID获取小组的信息
    public function getGroupInfo($id){
        return $this->where("id=%d",$id)->find();
    }
    
    //获取最新创建的小组的信息
    public function newGroupInfo($limit=10){
        return $this->order('addtime desc')->limit($limit)->select();
    }
    
    //检测小组是否为当前登录用户创建
    public function checkGroupAdmin($gid){
        $map['creator'] = UserSession::getUser('uid');
        $map['id'] = $gid;
        return $this->where($map)->count();
    }
    
   /**
    * 改变小组权限
    * @author liuzm <liuzm@cyyun.com>
    * @version 2015-05-04
    * @param $gid 小组ID
    * @param $weight 改变的权限值
    * @param $type 1-增加权限， -1，降低权限
    */    
    public function changeWeight($gid,$weight=1,$type=1){
        $prefix = C('DB_PREFIX');
        $weight = ($type==1)?(-$weight):$weight;
        $sql = "update " . $prefix . "group set weight = weight-$weight where id=$gid";
        $this->execute($sql);
    }
    
}
