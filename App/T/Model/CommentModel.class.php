<?php
namespace T\Model;
use Think\Model;
use Lib\Image;
use Lib\UserSession;
/**
 *  话题评论表 
 */
class CommentModel extends Model{
    
    //自动验证
    protected $_validate = array(     
                                array('uid','number','用户必须登录'),
                                array('sid','number','必须指定话题ID'),
                                array('calluid','number','必须指定回复给哪位用户'),
                                array('cid','number','必须为数字'),
                                //array('content','/^[^~!@#\<\>\$\%\^&\*\(\)\+]{1,}$/','评论内容有非法字符'),
                                array('content','2,140','评论内容为2-140个字符',0,'length'),
                            );

    //自动完成
    protected $_auto = array ( 
        array('time','time',3,'function') , // 新增时候，设置创建时间     
    );


    //获取用户参与评论的话题数
    public function subNum($uid){
        $tab = C('DB_PREFIX').'comment';
        $subTab = C('DB_PREFIX').'subject';
        $sql = "select count(*) as num  from (select $tab.id from $tab inner join $subTab on $tab.sid=$subTab.id  where $tab.status=1 && $tab.type=0 && $tab.uid = $uid && $subTab.isdel=0 group by $tab.sid) as tb";
	$result = $this->query($sql);
	return $result[0]['num'];
    }
    
    //获取用户参与的话题的信息
    public function partakeSub($uid,$p,$pagesize){
        $start = ($p-1)*$pagesize;
        $tab = C('DB_PREFIX').'comment';
        $subTab = C('DB_PREFIX').'subject';
        $sql = "select distinct $tab.sid, $subTab.* from $tab "
                . " inner join $subTab on $tab.sid=$subTab.id "
                . " where $tab.status=1 && $tab.type=0 && $tab.uid = $uid && $subTab.isdel=0 limit $start,$pagesize";
        return $this->query($sql);
    }
    
    //获取正在讨论的话题
    public function actComment($limit='0,5'){
        $prefic = C('DB_PREFIX');
        $subTab = $prefic.'subject';
        $comTab = $prefic.'comment';
        
        $sql = "select sid,max(time) as mtime,$subTab.title,$subTab.content,$subTab.uid,$subTab.image,$subTab.calluid,$subTab.gid from $comTab "
                . " inner join $subTab on $comTab.sid=$subTab.id "
                . " where $comTab.status=1 && $comTab.type=0 && $subTab.isdel=0  group by sid order by mtime desc limit $limit";
        return $this->query($sql);  
    }
    /**
     * 添加评论
     */
    public function addComment($sid,$calluid,$cid,$content,$type=1){
    	$uid=UserSession::getUser('uid');
    	$map=array(
    		'sid'=>$sid,
    		'uid'=>$uid,
    		'content'=>$content,
    		'time'=>time(),	
    		'cid'=>$cid,		
    		'calluid'=>$calluid,
    		'type'=>$type,
    	);
    	if($uid){
    		return $this->add($map);
    	}else{
    		return false;
    	}
    	
    }
    /**
     * 查询某一求助下的讨论
     */
    public function commentInfo($id,$cid,$p,$pageSize,$type=1,$status=1){
    	$start = ($p-1)*$pageSize;
    	$limit = "$start,$pageSize";
    	$w=array(
    		'sid'=>$id,
    		'type'=>$type,
    		'status'=>$status,
    		'cid'=>$cid			
    	);
    	if($cid){
    		$result=$this->where($w)->order("time asc")->select();
    	}else{
    		$result=$this->where($w)->order("time desc")->limit($limit)->select();
    	}
    	foreach ($result as $key=>$val){
    		$user=json_decode(api("/User/getById",array('uid'=>$val['uid'])));
    		$result[$key]['nickname']=$user->nickname;
    		$result[$key]['image']=Image::getUrlThumbCenter($user->photo,array(),$user->gender==2?'user_girl':'user');
    		$result[$key]['time']=date_time($val['time']);
    		$callUser=json_decode(api("/User/getById",array('uid'=>$val['calluid'])));
    		$result[$key]['callNickname']=$callUser->nickname;
    	}
    	return $result;
    }
  
}
