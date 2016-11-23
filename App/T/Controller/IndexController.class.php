<?php
namespace T\Controller;
use Think\Controller;
use T\Model\UCenterModel;
use T\Model\BannerModel;
use Lib\Image\UploadedFile;
use Lib\UserSession;

class IndexController extends Controller {

    public function index($vs='new'){
        $this->display($vs);
    }
    public function ajax_usercount(){ 
        echo D('UCenter')->UserCount();
    }
    public function ajax_orgcount(){
        echo api('/organization/count/status/1');
    }
    public function ajax_event(){
        layout(false);
        //征集
        $this->event=D('Event')->getHotEvent();
        $this->display('index_event');
    }
    public function islogin(){
        $uinfo=UCenterModel::getUserInfo();
        if($uinfo){
            echo '<a href="'.UCENTER.'/user/login.html">'.$uinfo['nickname'].'</a>';
        }else{
            echo '<a href="'.UCENTER.'/user/login.html" >登录</a>&nbsp;<strong>|</strong>&nbsp;<a href="'.UCENTER.'/user/register.html" >注册</a>';
        }
    }
    public function ajax_slider(){
        layout(false);
        $now = time();
        $data=M('banner')->
        where('start_time <=' . $now . ' and (end_time >= ' . $now . ' or end_time is null or end_time = "") and status='.BannerModel::BANNER_STATUS_SHOW)
            ->order('sort desc,create_date desc,id desc')
            ->select();
	    
        $this->assign('data', $this->imagePath($data));
        $this->display("index_slider");
    }
    private function imagePath($data){
        foreach ($data as $key => $value) {
            $image=$value['image'];
            $data[$key]['image']= UploadedFile::getFileUrl($image,array(760,400),'banner');
        }
        return $data;
    }
    public function ajax_project(){
        layout(false);
        $project=D('ProjectHot')->getHotList();
        $this->project=$project;
        $this->display('index_project');
    }
    public function ajax_active(){
        layout(false);
        //热点活动 :活动图片 活动名称 发布人 地址 活动时间 活动标签 报名人数
        $active=D('Active')->getHotList();
        $this->active=$active;
        $this->display('index_active');
    }
    public function ajax_good(){
        layout(false);
        //求助 物资求助
        $raisegoods=D('Project')->getInfoByType(\T\Model\ProjectModel::TYPE_ROISE_GOODS,12);
        $this->goods=$raisegoods;
        $this->display('index_goods');

    }
    public function ajax_org(){
        layout(false);
        //全部公益组织数
        $org=D('UCenter')->org();
	
        $this->org=$org['item'];
        $this->display('index_org');
    }
    
    public function ajax_group(){
        layout(false);
	   $this->assign('nowSubject',$this->getGroupInfo());
        $this->display('index_group');
    }
    
     /*
     * 正在讨论,即最新有回复的话题
     */
    public function nowSubject(){
        if(S('nowSubject')){
            return S('nowSubject');
        }
        $limit='0,4';	
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
            $result[$k]['image'] = \Lib\Image::getUrl($v['image'],array(60,60),'user');
            $result[$k]['mtime'] = time_day($v['mtime']);
            //根据最后回复的时间和话题ID,查询用户ID，并获取该用户的头像
            $m['sid'] =$v['sid'];
            $m['time'] = $v['mtime'];
			$m['type'] = 0;
			$m['status'] = 1;
            $uInfo = M('comment')->where($m)->find();
            $result[$k]['ruid'] = $uInfo['uid'];
            $rimg = json_decode(api("/User/getById",array('uid'=>$uInfo['uid'])));
            $result[$k]['rimg'] = \Lib\Image::getUrl($rimg->photo,array(60,60),'user');
            $result[$k]['runame'] = $rimg->nickname;
        }
        S('nowSubject',$result,10);
        return $result;
    }
    public function getGroupInfo(){
        $group=D('Group')->newGroupInfo(4);
        foreach($group as $k=>$v){
            $group[$k]['image'] = \Lib\Image::getUrl($v['image'],array(60,60));
            $user= json_decode(api("/User/getById",array('uid'=>$v['creator'])));
            $group[$k]['username'] =$user->nickname;
            $group[$k]['mtime'] = time_day($v['addtime']);
        }
        
        return $group;
    }
}