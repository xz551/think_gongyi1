<?php
namespace T\Controller;
use Think\Controller;
use Lib\UserSession;
use Lib\Image;
use Lib\User;
use T\Model\UserOauth2ShareModel;
class SubjectController extends Controller{
    //添加话题
    public function addSubject(){
        $this->assign('title','发布话题');
        tag('db_begin');
        //判断当前账户是否为认证的账户
        $this->checkUserIsOrg();
        //判断用户是否为小组成员
        if(!$this->checkUserIsInOrg(UserSession::getUser('uid'),I('gid'))){
            $this->error("只有小组成员才可添加话题");
        }
	//处理提交的话题
        $sub = D('Subject');        
        if(!$sub->create()){
           $this->error($sub->getError());
        }      
        $id = $sub->add();
        //给发布话题的小组权重加1，权重值配置一下
        $gid = intval(I('gid'));
        D('Group')->changeWeight($gid);
        if($id){
            D('Feed')->addFeed(UserSession::getUser('uid'),$id,'SubjectCreate');
            $this->redirect("t/subject/subinfo/id/$id");    
        }
    }
    //更新话题
    public function updateSubject(){
        $this->assign('title','更新话题');
        tag('db_begin');
        $sid = I('id');
        //判断话题是否为当前用户创建
        $subject = M('subject')->find($sid);
        if($subject['uid'] != UserSession::getUser('uid')){
            $this->error("禁止操作");
        }
        //处理提交的话题   
        $sub = D('Subject');
        if(!$sub->create()){
            $this->error($sub->getError());
        }
        if(!$sub->save()){
            $this->error('操作失败');
        }
        D('Feed')->addFeed(UserSession::getUser('uid'),$sid,'SubjectEdit');
        $this->redirect("t/subject/subinfo/id/$sid");
        
    }
    
    
    //判断用户是否为认证的组织账户
    public function checkUserIsOrg(){
        $status = User::isVip(UserSession::getUser('uid'));
        if(!$status){
            $this->error("只有认证用户才能发布话题");
        }
        
    }
    
    //话题详情页面
    public function subInfo(){
        $sid = I('id'); //获取话题ID
        $subject = M('Subject')->find($sid);    //获取话题信息
        if(!$subject){
            $this->error("话题不存在");
        }
        $this->assign('title',$subject['title']);
        if($subject['isdel'] == 1){
            $this->error("话题已删除");
        }
        /**
         * 本话题分享的个数
         */
        //$count=D('UserOauth2Share')->shareCount(UserOauth2ShareModel::SHARE_TYPE_SUBJECT);

        $count=D('UserOauth2Share')->where(" relation_type=5 and relation_id=".$sid)->count();
        $this->assign('sharenum',$count);

        //判断用户有没有登录，如果没有登录则显示返回地址
        if(UserSession::getUser()){
            $this->assign('islogin',1);
        }else{
           $this->assign('islogin',0);
           $reurl =  urlSafeBase64_encode(SERVER_VISIT_URL.U('t/subject/subinfo',array('id'=>$sid)).'#discuss');
           $reurl2 =  urlSafeBase64_encode(SERVER_VISIT_URL.U('t/subject/subinfo',array('id'=>$sid)));
           $this->assign('login',UCENTER.'/user/login.html?returnurl='.$reurl);
           $this->assign('login2',UCENTER.'/user/login.html?returnurl='.$reurl2);
        }    
        //判断是否为用户自己发布的话题
        ($subject['uid'] == UserSession::getUser('uid'))?$isMine = 1:$isMine = 0;
        $subject['image'] =$subject['image']?\Lib\Image::getUrl($subject['image'],array(600,0)):'';
        //获取当前话题所属小组
        $gid = $subject['gid'];
        //判断访问者是否为小组成员
        UserSession::getUser('uid')?(($this->checkUserIsInOrg(UserSession::getUser('uid'),$gid))?$ismGroup = 1:$ismGroup = 0): $ismGroup = 0;
        $default = (UserSession::getUser('gender')==2)?'user_girl':'user';
        $userPhoto = \Lib\Image::getUrl(UserSession::getUser('photo'),array(60,60),$default);
        $this->assign('userPhoto',$userPhoto);
        $this->assign('ismgroup',$ismGroup);
        //获取小组及组长的信息
        $groupInfo = $this->getGroupById($gid);
        $this->assign('groupInfo',$groupInfo);
        $s = ($subject['updatetime'] == $subject['addtime'])?'创建于&nbsp;&nbsp;':'更新于&nbsp;&nbsp;';
        $subject['time'] = $s.time_day($subject['updatetime']);
        
        //获取发布话题的人的信息
        $subUserObj = json_decode(api("/User/getById",array('uid'=>$subject['uid'])));
        $subUser['nickname'] = $subUserObj->nickname;
        $default = ($subUserObj->gender==2)?'user_girl':'user';
        $subUser['photo'] = Image::getUrl($subUserObj->photo,array(60,60),$default);
        $subUser['uid'] = $subUserObj->uid;
        $subUser['type'] = $subUserObj->type;
        $this->assign('subuser',$subUser);
      
	//获取组内话题
        $this->assign('sub',$this-> getSub($gid,'0,6',$sid));
        (User::isVip(UserSession::getUser('uid')))? $isvip = 1:$isvip = 0;
        $this->assign('gid',$gid);
        $this->assign('sid',$sid);
        $this->assign('isvip',$isvip);
        $this->assign('uid',UserSession::getUser('uid'));
        $this->assign('ismine',$isMine);
        $this->assign('subject',$subject);      
        $this->display('subInfo');
    }
    /**
     * 获取话题
     */
    private function getSub($gid,$limit,$sid){
        $sub = D('Subject')->getNewReplyInfo($gid,$limit);   
        $userList = array();
        foreach($sub as $k=>$v){
            if($v['id'] == $sid){
                unset($sub[$k]);
                continue;
            }
            $userList[] = $v['uid'];
        }
        $userList = array_unique($userList);
        $user = json_decode(api("/User/getUserList",array('uidArr'=>$userList)));        
	$user = $this->objToArray($user);
        foreach($sub as $key=>$val){
            $sub[$key]['user'] = $user[$val['uid']];
            $sub[$key]['time'] = time_day($val['mintime']);
            $default = ( $sub[$key]['user']['gender']==2)?'user_girl':'user';
            $sub[$key]['user']['photo'] = Image::getUrl($sub[$key]['user']['photo'],array('60','60'),$default);
        }   
        return $sub;
    }
    //获取话题的评论
    public function getComment($sid,$p=1,$pagesize=8){
        $limit = ($p-1)*$pagesize.' , '.$pagesize;
        //获取所有直接评论的话题信息
        $map['sid'] = $sid;
        $map['cid'] = 0;
		$map['type'] = 0;
		$map['status'] = 1;
        $result = M('comment')->where($map)->order('time desc')->limit($limit)->select();
        if(!$result){
		return 0;
	}
        $arr = array();
        $idList  = array();  //获取ID序列,用于获取评论的回复
        $uidList = array();   //获取UID序列，用户获取评论用户的资料
        foreach($result as $v){
            $arr[$v['id']] = $v;
            $idList[] = $v['id'];
            $uidList[] = $v['uid'];
        }
        $uidList = array_unique($uidList);
        $callback = $this->callBackInfo($idList);
        $user = $this->getUserInfo($uidList);
        foreach($result as $k=>$v){
            $result[$k]['userInfo'] = $user[$v['uid']];
            $result[$k]['callback'] = $callback[$v['id']];
            $result[$k]['time'] = date('Y-m-d H:i:s',$v['time']);
        } 
        return $result;
    }

    //获取所有的回复，并返回
    public function callBackInfo($cidList){
        $map['cid'] = array('in',$cidList);
		$map['type'] = 0;
		$map['status'] = 1;
        $result = M('comment')->where($map)->order('time desc')->select();
        $uArr = array();
        $arr = array();
        foreach($result as $v){
            $uArr[] = $v['uid'];
            $uArr[] = $v['calluid'];
            $arr[$v['cid']] = $v;
        }
        $uArr = array_unique($uArr);
        $user = $this->getUserInfo($uArr);
        $arr = array();
        $i=0;
        foreach($result as $k=>$v){
            $arr[$v['cid']][$i]['userInfo'] = $user[$v['uid']];
            $arr[$v['cid']][$i]['calluserInfo'] = $user[$v['calluid']];
            $arr[$v['cid']][$i]['subinfo'] = $v;
            $arr[$v['cid']][$i]['subinfo']['time'] = date('Y-m-d H:i:s',$v['time']);
            $i++;	
        }
        return $arr;
    }
    //获取所有的料，并以uid（用户ID）为键的数组格式返回
    public function getUserInfo($uidList){
        
        $user = json_decode(api("/User/getUserList",array('uidArr'=>$uidList)));        
        $user = $this->objToArray($user);
        foreach($user as $k=>$v){
            $default = ( $user[$k]['gender']==2)?'user_girl':'user';
            $user[$k]['photo'] = Image::getUrl($v['photo'],array('60','60'),$default);
        }
	return $user;
    }


    //ajax获取话题评论回复，并局部刷新页面
    public function ajaxSubInfo($p=1,$pagesize=8){
        //添加话题的信息
        $sid = I('id'); //获取话题ID
        $subject = M('Subject')->find($sid);    //获取话题信息
        //判断是否为用户自己发布的话题
        if($subject['uid'] == UserSession::getUser('uid')){
            $isMine = 1;
        }else{
            $isMine = 0;
        }
        $subject['image'] =$subject['image']?\Lib\Image::getUrl($subject['image'],array(600,0)):'';
        $start = ($p-1)*$pagesize;
        $limit = "$start,$pagesize";
        //获取话题的评论
        $comment = $this->getComment($sid,$p,$pagesize);
        $this->assign('sid',$sid);  //当前话题id
        $this->assign('uid',UserSession::getUser('uid'));   //当前用户id
        $this->assign('comment',$comment);
        $this->assign('ismine',$isMine);
        $this->assign('subject',$subject);
        
        //获取所有直接评论的话题信息
        $num =  M('comment')->where("sid=%d && cid=0 && type=0 && status=1",$sid)->count();
        $this->assign('num',$num);
        $this->assign("page",ajax_page($num,$pagesize));
        $this->display('ajaxSubInfo');
    }

    //添加话题评论
    public function addComment(){
        tag('db_begin');
        //获取话题的小组
        $sid = I('sid');
        $uid = UserSession::getUser('uid');
       	if(empty($uid)){
		echo '必须登录';
		die();
	}
        $subject = M('Subject')->find($sid);
        $gid = $subject['gid'];
        if(!$this->checkUserIsInOrg($uid,$gid)){
            echo "必须为小组成员才可参与讨论";
            die();
        }  
        $comment = D("Comment"); 
        if ($comment->create()){
            if($comment->add()){
                 //回复成功发通知    
                $arr = array(
                    'subjectid'=>$sid,
                    'subjectname'=>$subject['title'],
                    'content'=>I('content')
                );      
                if(I('calluid')){
                     if(I('calluid') != UserSession::getUser('uid')){
                        D('Notification')->sendMsg(I('calluid'),'user_reply_user',$arr);
                        D('Feed')->addFeed(UserSession::getUser('uid'),$sid,'CommentBack',array('id'=>I('calluid'),'content'=>I('content')));
                     }
                    
                }else{
                    if($subject['uid'] != UserSession::getUser('uid')){
                        D('Notification')->sendMsg($subject['uid'],'user_reply_sub',$arr);
                        D('Feed')->addFeed(UserSession::getUser('uid'),$sid,'SubjectBack',array('content'=>I('content')));
                    }
                }
                D('Group')->changeWeight($gid);
                echo 1;
            }else{
                echo '添加失败';
            }
        }else{
            echo $comment->getError();
        } 
    }
    
    //判断用户是否为小组的成员
    public function checkUserIsInOrg($uid,$gid){
        return api("/Group/checkUserIsGroup",array('uid'=>$uid,'gid'=>$gid));
    }

    //把对象转换成数组
    private function objToArray($result){
        $arr = array();
        foreach($result as $k=>$v){
            if(is_object($v)){
                foreach($v as $key=>$val){
                    $arr[$k][$key] = $val;
                }
            }else{
                $arr[$k] = $v;
            }
        }
        return $arr;
    }
    
    //ajax获取最新话题
    public function newSub(){
        $this->newSubInfo('addtime');
    }
    
    //ajax获取最近话题
    public function recentlySub(){
        $this->newSubInfo();
    }
    
    private function newSubInfo($order='',$p=1,$pageSize = 8){
        $gid = I('gid');
        $num = D('Subject')->getSubNum($gid);   //获取小组话题的总数目
        $userType = $this->checkUserIsGroup($gid);
        $this->assign('userType',$userType);
        $p = I('get.p')?I('get.p'):1;
        $start = ($p-1)*$pageSize;
        $limit = "$start,$pageSize";
        $this->assign("page",ajax_page($num,$pageSize));       
        $order = $order?$order:'minTime';        
        $sub = D('Subject')->getNewReplyInfo($gid,$limit,$order);
        $userList = array();
        foreach($sub as $k=>$v){
            $userList[] = $v['uid'];
        }
        $userList = array_unique($userList);
        $user = json_decode(api("/User/getUserList",array('uidArr'=>$userList)));        
	$user = $this->objToArray($user);
        foreach($sub as $key=>$val){
            $sub[$key]['user'] = $user[$val['uid']];
            $sub[$key]['time'] = time_day($val['minTime']);
            $default = ( $sub[$key]['user']['gender']==2)?'user_girl':'user';
            $sub[$key]['user']['photo'] = Image::getUrl($sub[$key]['user']['photo'],array('60','60'),$default);
        } 
        $this->assign('sub',$sub);
        $this->display('newSub');
    }
      
    //获取小组及组长的信息
    private function getGroupById($gid){
        $result = json_decode(api("/Group/getGroupInfo",array('gid'=>$gid)));
        $result = $this->objToArray($result);
        $result['image'] = Image::getUrl($result['image'], array(60),'user');
        $default = ( $result['user']['gender']==2)?'user_girl':'user';
        $result['user']['photo'] = Image::getUrl($result['user']['photo'], array(60),$default); 
        ($result && $result['creator'] == $this->user['uid'])?$result['ismine'] = 1:$result['ismine'] = 0;
        return $result;
    }
    
    //删除话题
    public function delSub(){
        $sid = intval(I('sid'));
        $r = D('Subject')->delSub($sid);
        if($r){
            //查看话题所属组
            $s = M('Subject')->find($sid);      
            D('Group')->changeWeight($s['gid'],1,-1);
            echo $r;
        }
    }
    
        //判断访问用户是当前小组成员还是其他成员
    private function checkUserIsGroup($gid){
        if(UserSession::getUser()){
            $uid = UserSession::getUser('uid');
            $userTag = api("/Group/checkUserIsGroup",array('uid'=>$uid,'gid'=>$gid));   //判断访问的是否为自己的小组
            if($userTag){   //访问的是自己的小组
                return 1;
            }else{  //访问的非自己的小组
                return 0;
            }
        }else{
            return 0;   //没有登录的用户为访问非自己的小组
        }
    }
    
    /**
     * 分享话题
     */
    public function shareSub(){
    	layout(false);
        //查看用户是否绑定 1.qq  2.weibo  3.renren
        $list = D('UserOauth2')->getBindingType();
        $this->assign('list',$list);
        //mb_strlen($str,'UTF8');  
        //当前地址
        $sid = I('sid');
        $url = SERVER_VISIT_URL.U('t/subject/subinfo',array('id'=>$sid));
        $content = "“益”起来，更精彩！我在中青公益中发现这个话题 “".I('name')."” 很不错哦，你也来参加吧！@中青公益 聚合青年公益力量。".$url;
	$this->assign('num',140-mb_strlen($content,'UTF8'));
	$this->assign('content',$content);
	$this->assign('relation_type',UserOauth2ShareModel::SHARE_TYPE_SUBJECT);
	$this->assign('sid',$sid);
        $this->display("shareSub");
    }
    
    
    /**
     * 获取API接口返回的内容
     */
    public function share(){
	$data = $_POST;
	$url = UCENTER."/oauth/pushstatuses";
        $r = getApiContent($url, $data);     
	$this->ajaxReturn($r);
    }

    
}
