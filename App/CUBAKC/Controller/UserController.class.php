<?php
namespace Uc\Controller;
use Lib\Image;
use Think\Controller;
use Lib\User;
use T\Common\Api\CityApi as CityApi;
use Lib\UserSession;
use Uc\Bll\userDynamic;
class UserController extends Controller{
    //加载用户首页
    public function index($uid){
    	$this->title = User::getUser($uid)->nickname.' 的主页 | 用户中心';
        (UserSession::getUser())?((UserSession::getUser('uid') == $uid)?$isMine = 1:$isMine = 0):$isMine=2;
        $this->assign('isMine',$isMine);        
        //获取所在的公益小组
        $groupInfo = json_decode(api("/Group/getUserGroup",array('uid'=>$uid,'p'=>'1','pagesize'=>'2','order'=>'1')));
        $group = array();
        foreach($groupInfo as $k=>$v){
            $group[$k]['id'] = $v->id;
            $group[$k]['name'] = $v->name;
            $group[$k]['introduce'] = $v->introduce;
            $group[$k]['image'] = Image::getUrlThumbCenter( $v->image, array(84));
        }
        $this->assign('groupNum',api("/Group/getUserGroupNum",array('uid'=>$uid,)));
        $this->assign('group',$group);
        $this->assign("uid",$uid); 
        if(User::isUser()){
                //所在的公益组织
                $org=$this->orgInfo($p=1,$pagenum=2,$uid);
                $this->assign("org",$org);
    		$this->display("index");
    	}else if(User::isOrg()){   
                //获取组织部分的数据，组织的简介和组织的自愿者
                $orgObj = json_decode(api("/Organization/getInfo",array('orgid'=>$uid)));
                $this->assign('introduce',$orgObj->summary);
                $orgUser = json_decode(api("/Organization/getUsers",array('orgid'=>$uid,'p'=>1,'pagesize'=>32)));
                $orgUserInfo = $orgUser->data;
                $orgUserArr = array();
                foreach($orgUserInfo as $k=>$v){
                    $gender=$v->gender;
                    $orgUserArr[$k]['uid'] = $v->uid;
                    $orgUserArr[$k]['nickname'] = $v->nickname;
                    $orgUserArr[$k]['photo'] = Image::getUrlThumbCenter($v->photo, array(50),$gender==2?'user_girl':'user');
                }
                $this->assign('userInfo',$orgUserArr);
                $this->assign('userNum',$orgUser->page->count);
    		$this->display("orgInfo");
    	}else{
    		//跳转到提示页面(查看的用户不存在)
    		$this->title = ' 系统消息 | 用户中心';
    		$this->error('查看的用户不存在');
    	}	    
    }
    //用户中心动态分页
    public function ajaxGetDynamic($uid,$p=1,$pageSize=10){
        $result = D('T/OrganizationUser')->getDynamic($uid); 
        $userDynamic = userDynamic::handleInfo($result,$uid,$p,$pageSize); 
    	$this->assign("userDynamic",$userDynamic['data']);
    	$this->assign("page",ajax_page($userDynamic['num'],$pageSize));
    	$this->display("ajaxGetDynamic");
    }
    /**
     * 当前用户所在最新的两个组织的信息
     */
    public function orgInfo($p=1,$pagenum=2,$uid){
		$result = D('T/OrganizationUser')->getOrgInfo($p,$pagenum,$uid);   //获取用户所在组织的信息
        $num = D('T/OrganizationUser')->getOrgNum($uid);                  //获取用户参加了多少个组织
        $result[0]['num']=$num;
        $city = new CityApi();
        foreach($result as $k=>$v){
            $result[$k]['photo'] =Image::getUrlThumbCenter($v['photo'], array(84),'user');//  UploadedFile::getFileUrl($v['photo'], array(100, 75),'user');
            $result[$k]['provinceid'] =$city->getName($v['provinceid']);
            $result[$k]['cityid'] = $city->getName($v['cityid']);
           
        }
	return $result;
    }

    
    
    /**
     * 当前用户所在小组信息
     */
    public function groupInfo($p=1,$pagenum=2,$uid){

        $result = D('T/Group')->getUserGroupInfo($p,$pagenum,$type=1);
        $num=D('T/GroupUser')->groupNum($uid);
        $result[0]['num']=$num;
        foreach($result as $k=>$v){
            $result[$k]['image'] =Image::getUrlThumbCenter($v['image'], array(84));//  UploadedFile::getFileUrl($v['image'], array(100, 75),'user');
        }
        return $result;    
    }

}