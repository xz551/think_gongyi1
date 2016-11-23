<?php
namespace T\Controller;
use Think\Controller;
use Lib\UserSession;
class SpecialpageController extends Controller {

    public function page() {
        layout(false);
        if ($this->isMobile()) {
            $pagename = "mobilepage"; //手机页面
        } else {
            $pagename = "page"; //电脑页面
        }
        $u = UserSession::getUser();
        if ($u) {
            $noticeNum = api("/User/getUnreadNoticeNum", array('uid' => UserSession::getUser('uid'))); //通知数
            $messageboxNum = api("/User/getMessageboxNum", array('uid' => UserSession::getUser('uid'))); //私信数
            $islogin = 1;
            $userType = $u['type'];
            //我的志愿项目
            $murl['project'] = U('Uc/Project/index', array('uid' => $u['uid']));
            //我的公益活动
            $murl['active'] = U('/Uc/Active/index/', array('uid' => $u['uid']));
            //我的志愿小组
            $murl['group'] = U('T/Group/liveGroup', array('id' => $u['uid']));
            //我的志愿家
            $murl['jia'] = 'http://jia.gy.com';
            $this->assign('murl', $murl);
            //获取url地址
            $this->assign('privateLetter', UCENTER . '/inbox/messagebox/mainbox.html');
            $this->assign('edit', UCENTER . '/accountinfo/base.html');
            $url = urlSafeBase64_encode(SERVER_VISIT_URL);
            $this->assign('logout', UCENTER . '/user/logout.html?returnurl=' . $url);
            $this->assign('notice', UCENTER . '/inbox/notification/mylist.html');
        } else {
            $url = urlSafeBase64_encode(YIJUAN_VISIT_URL . $_SERVER['REQUEST_URI']);
            $this->assign('login', '/Uc/login/index.html?returnurl=' . $url);
            $this->assign('register', UCENTER . '/Uc/Register/index.html?returnurl=' . $url);
            $isLogin = 0;
        }
        $this->assign('isLogin', $islogin);
        $this->assign('userType', $userType);
        $this->assign('user', $u);
        $this->assign('noticeNum', $noticeNum);
        $this->assign('messageboxNum', $messageboxNum);
        $this->display($pagename);
    }

    function isMobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

}
