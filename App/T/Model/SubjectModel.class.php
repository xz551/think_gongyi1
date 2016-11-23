<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;
class SubjectModel extends Model{
    
       //自动验证
    protected $_validate = array(     
                               //array('title',"/^[^!@#\<\>\$\%\^&\*\(\)\+]{1,}$/",'标题含非法字符'),
                               //array('content',"/^[^~!@#\<\>\$\%\^&\*\(\)\+]{0,}$/",'介绍含非法字符'),
                               array('title','5 ,35','标题请输入5-30个字符',0,'length'),
                               array('content','10,50565','介绍请输入10个以上字符',0,'length'),
                               array('gid','require','组ID不能为空'),
                            );

    //自动完成
    protected $_auto = array (
        array('uid','getCreator',1,'callback'), //新增数据时，设置创建者ID
        array('addtime','time',1,'function') , // 新增时候，设置创建时间
        array('updatetime','time',3,'function'), // 新增或者修改时，都更改 更新时间
    );

    //自动完成时获取用户id
    protected function getCreator(){
        return UserSession::getUser('uid');
    }



    
    //获取小组有最新回复的话题，及回复的数量
    public function getNewReplyInfo($gid,$limit='0,3',$order='minTime'){
        $prefix = C('DB_PREFIX');
        $subTab = $prefix.'subject';
        $commentTab = $prefix.'comment';
        if($order == 'addtime'){
            $order = $subTab.'.addtime';
        }
        /**if(tb_comment.time,count(*),0)  注释：如果查询出的第一条tb_comment.time 为真，则执行统计函数count(*)， 否则为0 
         *max(if(tb_comment.time,time,addtime)) 注释: 如果time 存在则结果为time,不存在则为addtime 最后保留所有中的最大数值
         *预测数据库中会跟上条数据比较，然后两者留其小
         */
        $sql = "select max(if($commentTab.time,$commentTab.time,$subTab.addtime)) as minTime, if($commentTab.time,count(*),0) as num, $subTab.id,$subTab.uid,$subTab.title,$subTab.content from $subTab  "
                . " left join $commentTab on (tb_subject.id = $commentTab.sid) && ($commentTab.status=1) && ($commentTab.type=0)  "
                . " where  $subTab.gid=$gid && $subTab.isdel=0  group by $subTab.id order by $order desc limit $limit";
        return $this->query($sql);
    }
    
    
    //获取用户发起的话题数量
    public function subNum($uid){
        return $this->where("uid=%d && isdel=0",$uid)->count();
    }
    
    //获取用户发起的话题信息
    public function subInfo($uid,$p=1,$pageNum=15){
        $start = ($p-1)*$pageNum;
        return $this->where("uid=%d && isdel=0",$uid)->limit($start,$pageNum)->select();
    }
    
    
    //获取小组的话题数目
    public function getSubNum($gid){
        return $this->where("gid=%d && isdel=0",$gid)->count();
    }
    
    //删除话题
    public function delSub($id){
        $map['uid'] = UserSession::getUser('uid');
        $map['id'] = $id;
        $map['isdel'] = 0;
        $data['isdel'] = 1;
        return $this->where($map)->save($data);
    }
    
    
}