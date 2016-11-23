<?php
namespace Uc\Widget;

use Lib\UserSession;
use Think\Controller;
use Lib\User;
use Lib\City;
use Lib\Image;

class LayoutWidget extends Controller
{
    public function top()
    {
        layout(false);
        $uid = I('uid') ? I('uid') : UserSession::getUser('uid');
        //判断用户是否登陆
        if (UserSession::getUser()) {
            //判断是访问的自己的账户还是别人的账户
            if (UserSession::getUser('uid') == $uid) {
                $isMine = 1;    //访问的自己的账户
            } else {
                $isMine = 0;    //访问的别人的账户
            }
        } else {
            $isMine = 2;
        }
        $this->assign('isMine', $isMine);
        //获取用户信息
        $userObj = json_decode(api("/User/getById", array('uid' => $uid)));
        $userInfo['name'] = $userObj->nickname;
        $userInfo['sex'] = ($userObj->gender == 1) ? '男' : (($userObj->gender == 2) ? '女' : '未知');
        $userInfo['area'] = City::getName($userObj->provinceid) . '&nbsp;' . City::getName($userObj->cityid) . '&nbsp;' . $userObj->address;
        $default = ($userObj->gender == 2) ? 'user_girl' : 'user';
        $userInfo['photo'] = Image::getUrlThumbCenter($userObj->photo, array(200), $default);
        $this->assign('userInfo', $userInfo);
        $this->assign('privateLetter', UCENTER . '/inbox/messagebox/mainbox.html');   //私信地址
        //获取关注领域
        $interest = json_decode(api("/User/getUserInterest", array('uid' => $uid)));
        $interestArea = array();
        foreach ($interest as $k => $v) {
            $interestArea[$k] = $v;
        }
        $this->assign('interest', $interestArea);

        //获取绑定的第三方信息
        $aouth = json_decode(api("/User/getOauth", array('uid' => $uid)));
        $userAouth = array();
        foreach ($aouth as $k => $v) {
            $userAouth[$k]['type'] = $v->type;
            $userAouth[$k]['homepage'] = $v->homepage;
        }
        $this->assign('aouth', $userAouth);
        $this->assign('isVip', User::isVip($uid));
        $this->assign('uid', $uid);
        //判断是组织还是个人账户，并获取对应数据及加载对应的页面
        if (User::isUser($uid)) {
            //获取个人技能
            $userSkill = json_decode(api("/User/getSkill", array('uid' => $uid)));
            $userSkillArr = array();
            foreach ($userSkill as $k => $v) {
                $userSkillArr[$k] = $v;
            }
            //获取个人的服务时长
            $serverTime = $this->getServerTime($uid, 2);
            if (!$serverTime) {
                $serverTime = 0;
            }
            $this->assign('serverTime', $serverTime);
            $this->assign('userSkill', $userSkillArr);
            $this->display('Widget/top_user');
		}else if(User::isOrg($uid)){
            //获取组织的服务时长
            $serverTime = $this->getServerTime($uid, 1);
            if (!$serverTime) {
                $serverTime = 0;
            }
            $this->assign('serverTime', $serverTime);
            //获取组织的材料
            $orgInfo = json_decode(api("/Organization/getInfo", array('orgid' => $uid, 'type' => 1)));
            if ($orgInfo->website) {
                if (substr($orgInfo->website, 0, 7) != 'http://') {
                    $orgInfo->website = 'http://' . $orgInfo->website;
                }
                $this->assign('isshow', 1);
            }

            $this->assign('orgInfo', $orgInfo);
            $this->display('Widget/top_org');
        }
    }

    public function left_account(){
        $cname = strtolower(CONTROLLER_NAME);
        if($cname=='accountinfo'){
            $this->display('Widget/Accountinfo:left'); 
        }
    }
    public function left()
    {
        //CONTROLLER_NAME
        layout(false);
        $uid = I('uid') ? I('uid') : UserSession::getUser('uid');
        $info = strtolower(CONTROLLER_NAME);
        if ($info == "volgroup") {
            $info = "user";
        } elseif ($info == "volres") {
            $info = "resumes";
        } elseif ($info == "uc") {
            $info = "user";
        }
        $this->assign('info', $info);
        if (User::getUser($uid)->uid == UserSession::getUser('uid')) {
            $this->assign('showPic', 1);
        }
        if (User::isUser($uid)) {
            $this->display('Widget/left_user');
        } else if (User::isOrg()) {
            $this->display('Widget/left_org');
        }
    }

    public function header()
    {
        layout(false);
        //判断用户是否登录
        if (UserSession::getUser()) {
            $isLogin = 1;   //登录的用户
            //获取用户名称
            $this->assign('name', UserSession::getUser('nickname'));
            //获取url地址
            $this -> assign('privateLetter', UCENTER . '/inbox/messagebox/mainbox.html');
            $this->assign('edit', UCENTER . '/accountinfo/base.html');
                   $url = urlSafeBase64_encode(WEB_SITE.$_SERVER['REQUEST_URI']);                    
            $this->assign('logout', UCENTER . '/user/logout.html?returnurl=' . $url);
            $this->assign('notice', UCENTER . '/inbox/notification/mylist.html');
            //登陆的用户获取通知的数目
            $noticeNum = api("/User/getUnreadNoticeNum", array('uid' => UserSession::getUser('uid')));
            $messageboxNum = api("/User/getMessageboxNum", array('uid' => UserSession::getUser('uid'))); //私信数
            $user = UserSession::getUser();
            if ($user['type'] == 20 || $user['type'] == 21) {
                $orginfo = D("T/Organization")->getOrgInfo($user['uid']);
                if ($orginfo['status'] == 1) {
                    $jiaUrl = JIA_SERVER_URL . '/index';
                } else {
                    $jiaUrl = JIA_SERVER_URL;
                }
            } else {
                $jiaUrl = JIA_SERVER_URL;
            }
            $this->assign('uid', UserSession::getUser('uid'));
            $this->assign('noticeNum', $noticeNum);
            $this->assign('messageboxNum', $messageboxNum);
        } else {
            $jiaUrl = JIA_SERVER_URL;
                    $url = urlSafeBase64_encode(SERVER_VISIT_URL.$_SERVER['REQUEST_URI']);
            $this->assign('login', UCENTER . '/user/login.html?returnurl=' . $url);
            $isLogin = 0;   //没有登录的用户
        }
        $this->assign('gongyi', SERVER_VISIT_URL);
        $this->assign('jiaUrl', $jiaUrl);
        $this->assign('isLogin', $isLogin);
        $this->display('Widget/header');
    }

    public function Tipswindown()
    {
        layout(false);
        $uid = I('uid');
        if (UserSession::getUser('uid') == $uid) {
            $jia_jieshao = 'jia_jieshao' . $uid;
            //先取 如果取到了 不加载  如果没有取到 存 并 显示
            if (!cookie($jia_jieshao)) {
                cookie($jia_jieshao, 'value', 99999999999);
                $this->display('Widget/tips');
            }
        }
    }
        //私信弹窗
        public function privateLetter(){
        	$this->display('Widget/privateLetter');
        }
    /**
     * @param $type 类型 1为获取组织的所有志愿者在该组织的服务时长  2为志愿者的服务时长
     * @param $gid
     * @param string $uid
     */
    private function getServerTime($id, $type)
    {
        if ($type == 1) {
            $map['assigner'] = $id;
        } else {
            $map['user_id'] = $id;
        }
        return M('project_recruit_detail')->where($map)->sum('server_time');
    }
	/**
	 * 
	 */
    public function findPasswordTop($tag){
    	$this->assign('tag',$tag);
    	$this->display("Widget/find_pwd_top");
    }
    public function footer()
    {
        $time = date("Y", time());
        $this->assign('time', $time);
        $this->display('Widget/footer');
    }
    
     /**
     * 获取地点标签模块
     */
    public function area($provinceid = '', $cityid = '', $countyid) {
        //如果为修改信息则默认选中地址信息 
        if (S(C('areaList'))) {
            $area = S(C('areaList'));
        } else {
            $area = M('ProvinceCity')->where("class_type=1")->select();
            S(C('areaList'), $area, C('areaTime'));
        }
        $this->assign('areaList', $area);
        $this->assign('provinceid', $provinceid);
        $this->assign('cityid', $cityid);
        $this->assign('countyid', $countyid);
        $this->display("Widget/area");
    }
    
    
}

?>