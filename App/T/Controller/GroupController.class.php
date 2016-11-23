<?php
namespace T\Controller;
use Think\Controller;
use Lib\UserSession;
use T\Model\Group;
use Lib\Image;
use Lib\City;
use Lib\VisitGroup;
use Lib\User;
use Org\Util\CMail;
/**
 * 用户小组的各种信息
 */
class GroupController extends Controller{
    protected $user;
    protected $isLogin;
    public function _initialize(){
        $this->user = UserSession::getUser();  //获取当前用户UID
        if(empty($this->user)){ 
            $this->isLogin = 0; //没有登录账户
        }else{
            $this->isLogin = 1; //已经登录账户
        }
    }
    
    /**
     * 加载我的公益小组页面
     */
    public function userGroup($p=1,$pageNum=5){
	$this->assign('title',"我的公益小组");
        $uid =I('get.id');
        //不传递uid的情况下默认访问自己的页面
        if(empty($uid)){
            $userType = 1;
            if($this->user){
                $uid = $this->user['uid'];
            }else{
                $this->redirect('t/Group/FindGroup');
            }
        }else{
            $userType = $this->userType($uid);
        }
        if(!is_numeric($uid)){
            $this->error("禁止操作");
        }
        $this->assign('userType',$userType);
        $result = api("/Group/getUserGroup",array('uid'=>$uid,'p'=>$p,'pagesize'=>$pageNum));   	
	$data['groupInfo'] = $this->getUserGroup(json_decode($result));                  //我的公益小组信息
        $this->assign('isRead',$this->myVisiterGroup());
        $data['groupInfo']?$data['groupNum'] =   api("/Group/getUserGroupNum",array('uid'=>$uid)):$data['groupNum'] =0; //所在小组的数量
        $data['groupInfo']?$data['initiatedSubNum'] = D('Subject')->subNum($uid):$data['initiatedSubNum'] =0;     //发起话题的数量
        $data['groupInfo']?$data['partakeSubNum'] = D('Comment')->subNum($uid):$data['partakeSubNum']=0;            //参与话题的数量
         
        $data['visitGroup'] =     session('group');                  //访问过的小组,记录在session中
        $data['newGroup'] = $this->newGroup();                       //新创建的小组  
	$this->assign('uid',$uid);
        $this->assign('info',$data);
        $this->assign("page",page_new($data['groupNum'],$pageNum));
        $this->display('userGroup');
    }
    
    /**
     * 我访问过的小组
     */
    private function myVisiterGroup(){
        $isReadGroup = VisitGroup::getVisitGroup();
        foreach ($isReadGroup as $val) {
            $addTime[] = $val['seltime'];
        }
        array_multisort($addTime, SORT_DESC, $isReadGroup);
        $isReadArr = array();
        for($i=0;$i<10;$i++){
            if($isReadGroup[$i]){
                $isReadArr[$i] = $isReadGroup[$i];
            }else{
                break;
            }
        }
       return $isReadArr;
    }

    /*
     * 所在的公益小组
     * I(get.found) 存在则为查看自己创建的公益小组
     */
    public function liveGroup($p=1,$pageNum = 12){
        $this->assign("title",'所在公益小组');
        $start = ($p-1)*$pageNum;
        $limit = "limit $start,$pageNum";
        $uid = I('get.id');
        $this->checkUserSafe($uid);
        $found = I('get.found');     
        $found?$type = 2:$type = 1; 
        $found?$tag = 1:$tag = 0;
        $num = json_decode(api("/Group/getUserGroupNum",array('uid'=>$uid,'type'=>$type)));
        $allGroup = json_decode(api("/Group/getUserGroup",array('uid'=>$uid,'p'=>$p,'pagesize'=>$pageNum,'type'=>$tag+1)));
        $this->assign('tag',$tag);
        $groupInfo = array();
        foreach($allGroup as $k=>$v){
            $groupInfo[$k]['id'] = $v->id;
            $groupInfo[$k]['name'] = $v->name;
            $groupInfo[$k]['updatetime'] = date('Y-m-d H:i:s',$v->updatetime);
            $groupInfo[$k]['image'] = Image::getUrl($v->image, array(200),'user');        
        }
        if(UserSession::getUser('uid') == $uid){
            $ismine = 1;
        }else{
            $ismine = 0;
        }
        $this->assign('ismine',$ismine);
        $this->assign('uid',$uid);
        $this->assign('myuid',  UserSession::getUser('uid')); 
        $this->assign("page",page_new($num,$pageNum));
        $this->assign('groupInfo',$groupInfo);
        $this->assign('isLogin',$this->isLogin);
        $this->display('liveGroup');
    }
    
    //检测用户是否存在
    private function checkUserSafe($uid){
	if(!is_numeric($uid)){
            $this->error("禁止操作");
        }
        if(!D('User')->checkUser($uid)){
            $this->error("查看的用户不存在");
        }
    }
    
    
    
    /**
     * 发起的公益话题
     */
    public function startSub($p=1,$pagesize=12){
        $this->assign('title','发起的公益话题');
        $uid = intval(I('get.uid'));
        $this->checkUserSafe($uid);
        $num = D('Subject')->subNum($uid);
        $result = D('Subject')->subInfo($uid,$p,$pagesize);
        foreach($result as $k=>$v){
            $result[$k]['image'] = Image::getUrl($v['image'], array(200),'user');
            $result[$k]['updatetime'] = date('Y-m-d H:i:s',$v['updatetime']);
        }
        $this->assign('uid',$uid);
        $this->assign('subInfo',$result);
        $this->assign("page",page_new($num,$pagesize));
        $this->assign('myuid',  UserSession::getUser('uid'));
        $this->assign('isLogin',$this->isLogin);
        $this->display('startSub');
    }
    
    /**
     * 参与的公益话题
     */
    public function partakeSub($p=1,$pagesize = 12){
        $this->assign('title','参与的公益话题');
        $uid = intval(I('get.uid'));
        $this->checkUserSafe($uid);
        $result = D('Comment')->partakeSub($uid,$p,$pagesize);
        $num = D('Comment')->subNum($uid);
        $this->assign("page",page_new($num,$pagesize));
        foreach($result as $k=>$v){
            $result[$k]['image'] = Image::getUrl($v['image'], array(200),'user');
            $result[$k]['updatetime'] = date('Y-m-d H:i:s',$v['updatetime']);
        }
        $this->assign('uid',$uid);
        $this->assign('subInfo',$result);
        $this->assign('myuid',  UserSession::getUser('uid'));
        $this->assign('isLogin',$this->isLogin);
        $this->display('partakeSub');
    }

    /**
     * 用户管理
     */
    public function userSupervise($p=1,$pagesize=12){
        $this->assign('title','小组成员');
        $gid =I('get.id');  //组ID
        if((!is_numeric($gid)) || !D('Group')->getGroupInfo($gid)){
            $this->error("禁止操作");
        }
        //获取小组的成员
        $result = json_decode(api("/Group/getNewUserinfo",array('gid'=>$gid,'p'=>$p,'pagesize'=>$pagesize,'order'=>'time'))); 
        //获取小组成员的数目
        $userNum = api("/Group/getUserNum",array('gid'=>$gid));
        $userInfo = array();
        foreach($result as $k=>$v){
            $userInfo[$k]['uid'] = $v->uid;
            $userInfo[$k]['nickname'] = $v->nickname;
            $userInfo[$k]['photo'] = Image::getUrl($v->photo, array(60,60),$v->gender==2?'user_girl':'user');
            $userInfo[$k]['address'] = City::getName($v->provinceid).'&nbsp;'.City::getName($v->cityid);
            $userInfo[$k]['type'] = $v->type;
        }  
        $this->assign('page',page_new($userNum,$pagesize));
        $this->assign('userInfo',$userInfo);
        //小组及小组组长信息
        $groupInfo = $this->getGroupById($gid);
        $this->assign('groupInfo',$groupInfo);        
        UserSession::getUser()?$this->assign('islogin',1):$this->assign('islogin',0);  
        //成员还加入了
        $this->assign('otherGroup',$this->getUserOtherGroup($gid));
        $this->assign('uid',$this->user['uid']);
        $this->assign('gid',$gid);
        $this->display('userSupervise');
    }
    
    /**
     * 移除小组成员
     */
    public function delUser(){
        $gid = intval(I('gid'));
        $uid = intval(I('uid'));
        $this->checkGroupAdmin($gid);   //查看小组是否为当前用户创建 
        D('GroupUser')->delUser($gid,$uid)?$tag=1:$tag=0;
        if($tag){
            //发通知告知成员
            $r = D('Group')->find($gid);
            $arr = array(
                'groupid'=>$gid,
                'groupname'=>$r['name']
            );
            D('Notification')->sendMsg($uid,'deluser_from_group',$arr);
            
        }
        echo $tag;
    }
    
    /**
     * 检测小组是否为当前用户
     */
    private function checkGroupAdmin($gid){
        if(!D('Group')->checkGroupAdmin($gid)){
            $this->success("禁止操作");
            die();
        }
    }



    /**
     * 发现公益小组
     */
    public function findGroup(){ 
        $this->assign('title','发现公益小组');
        $this->assign('attList',$this->getAttList());    //获取标签
        //判断是否为认证的组织用户
        $isOrgVip=User::isVip(UserSession::getUser('uid'));
        //判断用户是否登录
        if(UserSession::getUser()){
            $this->assign('islogin',1);
        }else{
            $this->assign('islogin',0);
            $reurl =  urlSafeBase64_encode(SERVER_VISIT_URL.U('t/group/findgroup'));
            $this->assign('login',UCENTER.'/user/login.html?returnurl='.$reurl);
        }
        
        $this->assign('isOrgVip',$isOrgVip);
        if($this->user){
            $this->assign('uid',$this->user['uid']);
        }else{
            $this->assign('uid',0);
        }
        $this->assign('nowSubject',$this->nowSubject());
        $this->display('findGroup');
    }
    
    /*
     * 通过ajax获取小组的信息,用于发现小组页面
     */
    public function ajaxGetGroup($p=1,$pageSize=8){
        $p = I('get.p')?I('get.p'):1;
        $lid = I("lid")?I("lid"):0;
        $tag = 'findgroup'.$p.'-'.$lid;
        if(S($tag)){
            $result = S($tag);
        }else{
            //获取小组信息
            $result = api("/Group/getGroupInfoAndNum",array('p'=>$p,'pagesize'=>$pageSize,'lid'=>$lid));
            $result = json_decode($result);
            S($tag,$result,120);
        }
        $num = $result->num;
        $data = objToArray($result->data);
        //如果用户登录，则获取用户所属的小组
        if($this->user){
            $uid = $this->user['uid'];
            $gidList = json_decode(api("/Group/getUserGroupIsList",array('uid'=>$uid)));
        }
        foreach($data as $k=>$v){
            $data[$k]['image'] = Image::getUrl($v['image'], array(60),'user');//UploadedFile::getFileUrl($v['image'], array(60, 60),'group');
            $data[$k]['subNum'] = D('Subject')->getSubNum($v['id']);     //话题数
            $data[$k]['userNum'] = api("/Group/getUserNum",array("gid"=>$v['id']));  //成员数
            $userInfo = json_decode(api("/User/getById",array('uid'=>$v['creator'])));   //组长
            $data[$k]['creator'] = $userInfo->nickname;
            $data[$k]['uid'] = $userInfo->uid;
            //判断用户是否是小组的成员      
            $this->user?(in_array($v['id'],$gidList)?$data[$k]['isGroup']=1:$data[$k]['isGroup']=0):$data[$k]['isGroup'] = 0;
        }
        $this->assign('isVip',User::isVip(UserSession::getUser('uid')));
        //判断用户是否登录
        if(UserSession::getUser()){
            $this->assign('islogin',1);
        }else{
            $this->assign('islogin',0);
            $reurl =  urlSafeBase64_encode(SERVER_VISIT_URL.U('t/group/findgroup'));
            $this->assign('login',UCENTER.'/user/login.html?returnurl='.$reurl);
        }
        
        $this->assign('data',$data);
        //找出当前页的数据
        $this->assign("page",ajax_page($num,$pageSize));
        $this->display('ajaxGetGroup');
    }

    
    
    //整理小组的信息
    private function getUserGroup($result){ 
	$result = objToArray($result);
	foreach($result as $k=>$v){           	    
	    $result[$k]['image'] = Image::getUrl($v['image'], array(60,60),'user');//UploadedFile::getFileUrl($v['image'], array(60, 60),'group');
	    $result[$k]['newReply'] = $this->newReply($v['id']);         //获取小组的最新回复的话题及最后的回复时间和回复数等	
	    $result[$k]['newJoinUser'] = $this->newJoinUser($v['id']);   //获取小组最新加入的成员
        }
	
	

        return $result;
    }
    
    
    //新创建的小组
    public function newGroup(){
        $result = api("/Group/newGroupInfo");
	$result = objToArray(json_decode($result));
        foreach($result as $k=>$v){
            $result[$k]['image'] =Image::getUrl($v['image'], array(60),'user');// UploadedFile::getFileUrl($v['image'], array(60, 60),'group');			
        }
        return $result;
    }
   
    //小组最新加入的人员
    public function newJoinUser($gid){
        $order = "time desc";
        $result = api("/Group/getNewUserinfo",array('gid'=>$gid,'p'=>1,'pagesize'=>'10','order'=>$order));
        $result = objToArray(json_decode($result));
    	foreach($result as $k=>$v){
            $default = $v['gender']==2 ? 'user_girl':'user';
            $result[$k]['photo'] = Image::getUrl($v['photo'], array(60),$default);
        }
	return $result;
    }
   
    
    /*
     * 小组最新的回复
     * 思路：查询话题表，连接回复表，按话题分组 求最小时间和记录数 按最小时间正序排列取前三
     */
    
    private function newReply($gid){
      	
        $result = D('Subject')->getNewReplyInfo($gid);
        foreach($result as $k=>$v){
                $result[$k]['mintime'] = time_day($v['minTime']);        
        }
	return $result;
    }

    
    //创建小组
    public function addGroup(){
        $this->assign('title','创建公益小组');
        tag('db_begin');
        $this->checkIsOrg();//判断是否填写资料的组织用户，不是则不能登录
        $this->assign('attList',$this->getAttList());
        $this->display('addGroup');
    }
    //添加用户进入小组
    public function addUserInGroup(){
        tag('db_begin');
        $gid = intval(I('gid'));
        if(User::isVip(UserSession::getUser('uid'))){
            $uid = $this->user['uid'];
            $id = api("/Group/addUserInGroup",array('uid'=>$uid,'gid'=>$gid));
            if($id == -1){
                echo "用户或组不存在";
            }else{
                echo $id;
            }
        }else{
            echo "只有通过认证用户才能加入小组";
        }
    }
    
    
    
    //添加小组处理程序
    public function doAddGroup(){
        tag('db_begin');
	$this->checkIsOrg();    //判断是否填写资料的组织用户，不是则不能创建
        $_POST['label'] = implode(',',$_POST['label']);
        $group = D('Group');
        if(!$group->create()){
            $this->error($group->getError());
            die();
        }
        $gid = I('id');
        if($gid){    //修改小组
            //判断修改的是否为自己创建的小组
            $result = json_decode(api("/Group/getGroupInfo",array('gid'=>$gid)));
            if(!$result || $result->creator != $this->user['uid']){
                $this->error("禁止操作");
            }else{
                if($group->save()){
                    D('Feed')->addFeed(UserSession::getUser('uid'),$gid,'GroupEdit');
                    $this->redirect("T/Group/selGroup/id/$gid");
                }
            }      
        }else{ //创建小组
            $gid = $group->add();
            if($gid){
                //创建成功，给组织下的成员发布消息
                $map['status'] = 1;
                $map['org_id'] = UserSession::getUser('uid');
                $userList = D('OrganizationUser')->where($map)->select();
                $uid = array();
                foreach($userList as $v){
                    $uid[] = $v['uid'];
                }
                $arr = array(
                    'groupname'=>I('name'),
                    'groupid'=>$gid
                );
                D('Notification')->sendMsg($uid,'invite_users',$arr,1);
                
                
                D('GroupUser')->addCreator($gid,UserSession::getUser('uid'));
                D('Feed')->addFeed(UserSession::getUser('uid'),$gid,'GroupCreate');
                $this->redirect("T/Group/selGroup/id/$gid");             
            }else{
                $this->error("创建失败");
            }
        }
    }
    
    
    //获取所有关注列表
    private function getAttList(){
        $m = D('CategoryServer'); 
        return $m->field('id,name')->order('id desc')->select();      
    }
    
    //判断是否为认证组织用户
    private function checkIsOrg(){ 
        if(!UserSession::getUser('uid')){
            $this->error("只有通过认证的组织可以创建小组");
            die();
        }else{
            $r = D('Organization')->getOrgInfo(UserSession::getUser('uid'));
            if($r['status'] != 1){
                $this->error("只有通过认证的组织可以创建小组");
                die();
            } 
        }
    }

    /*
     * 正在讨论,即最新有回复的话题
     */
    public function nowSubject($limit='0,10'){
        if(S('nowSubject')){
            return S('nowSubject');
        }
        $result = D('Comment')->actComment($limit);
        $uidArr = array();
        $gidArr = array();
        foreach($result as $k=>$v){
            $uidArr[] = $v['calluid'];
            $gidArr[] = $v['gid'];
        }
        $uidArr = array_unique($uidArr);
        $gidArr = array_unique($gidArr);
        $uidList = objToArray(json_decode(api("/User/getUserList",array('uidArr'=>$uidArr))));       	
	$gidList = objToArray(json_decode(api("/Group/getGroupList",array('gidArr'=>$gidArr))));			
        foreach($result as $k=>$v){
            $result[$k]['calluser'] = $uidList[$v['calluid']];
            $result[$k]['group'] = $gidList[$v['gid']];
            $result[$k]['image'] = Image::getUrl($v['image'],array(60,60),'user');
            $result[$k]['mtime'] = time_day($v['mtime']);
            //根据最后回复的时间和话题ID,查询用户ID，并获取该用户的头像
            $m['sid'] =$v['sid'];
            $m['time'] = $v['mtime'];
			$m['type'] = 0;
			$m['status'] = 1;
            $uInfo = M('comment')->where($m)->find();
            $result[$k]['ruid'] = $uInfo['uid'];
            $rimg = json_decode(api("/User/getById",array('uid'=>$uInfo['uid'])));
            $result[$k]['rimg'] = Image::getUrl($rimg->photo,array(60,60),'user');
            $result[$k]['runame'] = $rimg->nickname;
        }
        S('nowSubject',$result,10);
        return $result;      
    }
    
    /**
     * 查看小组信息页面
     */
    public function selGroup($p=1,$pageSize=8){   
        $gid = I('get.id');
        if(empty($gid)){
            $this->error("禁止访问");
        }
        $group = D('Group')->getGroupInfo($gid);    //查看小组是否存在
        if(!$group){
            $this->error('小组不存在或已被删除');
        }
        $this->assign('title',$group['name']);
        $this->assign('userLogo',  Image::getUrl(UserSession::getUser('photo'),array(60,60),'user'));     
        $this->assign('userType',$this->checkUserIsGroup($gid));  //获取访问小组的用户的类型，1为访问自己的小组，0为访问非自己的小组
        $this->assign('gid',$gid);
        //获取小组的信息,并判断是否为自己创建的小组
        $result = $this->getGroupById($gid);
        $this->assign('isfirst',$result['ismine']);  
        $limit = ($p-1)*$pageSize." , ".$pageSize;
        $this->assign("page",page_new(D('Subject')->getSubNum($gid),$pageSize));
        $this->assign('sub',$this->getSubject($gid,$limit));  
        
        $this->assign('userOtherGroup',$this->getUserOtherGroup($gid));     
        $this->assign('isvip',User::isVip(UserSession::getUser('uid'))); 
        $this->assign('userNum',api("/Group/getUserNum",array('gid'=>$gid)));    //获取小组一个有多少成员
        $newUser = json_decode(api("/Group/getGroupNewUser",array('gid'=>$gid)));     //获取小组最新加入的成员信息
        $newUserArr = array();
        foreach($newUser as $k=>$v){
           $newUserArr[$k]['uid'] = $v->uid;
           $newUserArr[$k]['photo'] = Image::getUrl($v->photo, array(60),$v->gender==2?'user_girl':'user'); 
           $newUserArr[$k]['nickname']=$v->nickname;  
        }
        $this->assign('newUser',$newUserArr);
        VisitGroup::recordGroup($result);   //将访问过的小组添加进session中
        $this->assign('groupInfo',$result);         
        //判断用户有没有登录，如果没有登录则显示返回地址
        if(UserSession::getUser()){
            $this->assign('isLogin',1);
        }else{
           $this->assign('isLogin',0);
           $reurl =  urlSafeBase64_encode(SERVER_VISIT_URL.U('t/group/selGroup',array('id'=>$gid)));
           $this->assign('login',UCENTER.'/user/login.html?returnurl='.$reurl);
        }
        $this->assign('user',$this->user);
        $this->assign('gid',$gid);
        $this->display('selGroup');
    }
    
    private function getSubject($gid,$limit){
        //获取小组话题信息
        $sub = D('Subject')->getNewReplyInfo($gid,$limit);
        $userList = array();
        foreach($sub as $k=>$v){
            $userList[] = $v['uid'];
        }
        $userList = array_unique($userList);
        $user = json_decode(api("/User/getUserList",array('uidArr'=>$userList)));        
	$user = objToArray($user);
        foreach($sub as $key=>$val){
            $sub[$key]['user'] = $user[$val['uid']];
            $sub[$key]['time'] = time_day($val['mintime']); 
            $sub[$key]['user']['photo'] = Image::getUrl($sub[$key]['user']['photo'],array('60','60'),'user');
        }
        return $sub;
    }
    
    
    
    
    //获取小组成员还加入了哪些小组
    public function getUserOtherGroup($gid){
        $userOtherGroupInfo = json_decode(api("/Group/userOtherGroup",array('gid'=>$gid)));
	$userOtherGroupArr = array();
        foreach($userOtherGroupInfo as $k=>$v){
            if($v->gid==$gid){
                continue;   //忽略当前小组
            }
            $userOtherGroupArr[$k]['gid'] = $v->gid;
            $userOtherGroupArr[$k]['name'] = $v->name;
            $userOtherGroupArr[$k]['introduce'] = $v->introduce;
            $userOtherGroupArr[$k]['image'] = Image::getUrl($v->image, array(60),'user');
        }
        return $userOtherGroupArr;
    }
    
    
    //获取小组的信息,并判断是否为自己创建的小组
    private function getGroupById($gid){
        $result = json_decode(api("/Group/getGroupInfo",array('gid'=>$gid)));
        $result = objToArray($result);    
        $result['image'] = Image::getUrl($result['image'], array(60),'user');
        $result['user']['photo'] = Image::getUrl($result['user']['photo'], array(60),'user'); 
        $result['user']['address'] = City::getName($result['user']['provinceid']).'&nbsp;'. City::getName($result['user']['cityid']);
        if($result['creator'] == $this->user['uid']){
            $result['ismine'] = 1;
        }else{
            $result['ismine'] = 0;
        }
        return $result;
    }
    
    
    
    //判断访问用户是当前小组成员还是其他成员
    private function checkUserIsGroup($gid){
        if($this->isLogin == 1){
            $uid = $this->user['uid'];
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
    
    
    //判断访问者的用户类型
    private function userType($uid = ''){         
        if(empty($uid)){
            if(empty($this->user['uid'])){
                $this->error("禁止访问");
            }else{
                $uid = $this->user['uid'];
                return 1;    //访问的自己的账户信息
            }
        }elseif($uid == $this->user['uid']){ //判断用户访问的是自己的信息还是别人的信息
            return 1;     //访问自己的账户自己的账户信息
        }else{
            $this->error("禁止访问");
        }
    }
    
    //修改小组信息
    public function updateGroup(){
        $this->assign('title','修改公益小组信息');
        $this->checkIsOrg();//判断是否填写资料的组织用户，不是则不能登录
        $this->assign('attList',$this->getAttList());
        $gid = I('get.id');
        //判断修改的小组是否为自己的小组
        $groupInfo = json_decode(api("/Group/IsGroupCreater",array('gid'=>$gid)));
        $groupInfo = objToArray($groupInfo);
        if($groupInfo['creator'] != $this->user['uid']){
            $this->error("禁止访问");
        }
        $groupInfo['imagesrc'] = Image::getUrl($groupInfo['image'], array(81,78),'user');
    	$this->assign('groupInfo',$groupInfo);
        $this->assign('isUp',1);    //标记为修改小组信息页面
        $this->assign('gid',$gid);
        $this->display('addGroup');
    }
    
    //退出小组
    public function exitGroup(){
        $uid = UserSession::getUser('uid');
        $gid = intval(I('gid'));
        if(D('Group')->checkGroupAdmin($gid)){
            echo -1;
            die();
        }
        $r = D('GroupUser')->delUser($gid,$uid);
        echo $r;
    }

 
}