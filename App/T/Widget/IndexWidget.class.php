<?php
namespace T\Widget;

use T\Model\BannerModel;
use T\Model\ProjectHotModel;
use T\Model\VolunteerModel;
use Lib\UserSession;

/**
 * Description of IndexWidget
 *
 * @author WuWangCheng
 */
class IndexWidget extends WController
{
    //焦点
    function new_slider()
    {
        $key='t_think_gongyi_index_new_slider';
        $now = time();
        $data=S($key);
        if(!$data){
        $data = M('banner')->
        where('start_time <=' . $now . ' and (end_time >= ' . $now . ' or end_time is null or end_time = "") and status=' . BannerModel::BANNER_STATUS_SHOW)
            ->order('sort desc,create_date desc,id desc')
            ->select();
            S($key,$data,60*60*2);
        }
        foreach ($data as $key => $value) {
            $url = $value['url'];
            $url = str_replace("http://www.gy.com/news/", 'http://cms.' . DOMAIN . '/news/', $url);
            $data[$key]['url'] =$url;
	    $image = $value['image']; 
            $data[$key]['image'] = \Lib\Image::getUrl($image, array(760, 400));
	     
        }
        $this->assign('data', $data);
        $this->display("Widget/Index:new_slider");
    }

    //报道
    function new_information()
    {
        $key = "t_think_gongyi_index_new_information";
        $baodao = S($key);
        if (!$baodao) {
            $baodao = D('Article')->getHotArticle(0, 6);
            foreach ($baodao as $k => $v) {
                $baodao[$k]['content'] = trim(strip_tags($v['content'], 'img'), "&nbsp;");
            }
            S($key, $baodao, 60*60*2);
        }
        $this->assign('baodao', $baodao);
        $this->display("Widget/Index:new_information");
    }

    //招募
    function new_recruit()
    {
        $key = "t_think_gongyi_index_project";
        $project=S($key);
        if(!$project){
            $project = D("ProjectHot")->getHotList(4);
            S($key,$project,60*60*2);
        }
        $this->project = $project;
        $this->display("Widget/Index:new_recruit");
    }

    //活动
    function new_active()
    {
        $key = "t_think_gongyi_index_active";
        //热点活动 :活动图片 活动名称 发布人 地址 活动时间 活动标签 报名人数
        $active=S($key);
        if(!$active){
            $active = D('Active')->getHotList();
            S($key,$active,60*60*2);
        }
        $this->active = $active;
        $this->display("Widget/Index:new_active");
    }

    //小组
    function new_group()
    {
        $key = "t_think_gongyi_index_group";
        $group = S($key);
        if (!$group){
            $group = $this->getGroupInfo();
            S($key,$group,60*60*2);
        }
        $this->assign('group', $group);
        $this->display("Widget/Index:new_group");
    }

    //互助
    function new_help()
    {
        $help_key = "t_think_gongyi_index_help";
        $help=S($help_key);
        if(!$help){
            //求助
            $help = D('Concur')->getConcurInfoByType(0);
            S($help_key,$help,60*60*2);
        }
        $dona_key = "t_think_gongyi_index_dona";
        $dona=S($dona_key);
        if(!$dona){
            //捐助
            $dona = D('Concur')->getConcurInfoByType(1);
            S($dona_key,$dona,60*60*2);
        }
        $this->assign("help", $help);
        $this->assign("dona", $dona);
        $this->display("Widget/Index:new_help");
    }

    //推荐
    function new_recommend()
    {
        $key='t_think_gongyi_index_recommend';
        $org=S($key);
        if(!$org){
            $org = D('UCenter')->org(10);
            S($key,$org,60*60*2);
        }
        $this->org = $org['item'];
        $this->display("Widget/Index:new_recommend");
    }

    //最新加入
    function new_join()
    {
        $key='t_think_gongyi_index_new_join';
        $new_join=S($key);
        if(!$new_join){
            $new_join = D("Organization")->getNewJoinOrg();
            S($key,$new_join,60*60*2);
        }
        $this->assign("newjoin", $new_join);
        $this->display("Widget/Index:new_join");
    }

    //用户信息
    function new_userinfo()
    {
        //	return;
        $u = UserSession::getUser();
        if ($u) {
            $islogin = 1;
            $uinfo['type'] = $u['type'];
            $uinfo['name'] = $u['nickname'];
            $uinfo['photo'] = $u['photo'] ? \Lib\Image::getUrl($u['photo'], array(70, 70)) : STATIC_SERVER_URL . "/gongyi/images/gy_lg_false.png";
            //个人用户
            if (in_array($u['type'], array(10, 11))) {
                $uinfo['status'] = D("Volunteer")->getVolInfoById($u['uid'])['status'];
                $uinfo['server_time'] = D("Volunteer")->getVolDetailInfoByVolId($u['uid']);
            }
            //组织用户
            if (in_array($u['type'], array(20, 21))) {
                $org_info = D("Organization")->getOrgInfo($u['uid']);
                $vol_info = D("Volunteer")->getVolDetailInfoByOrgId($u['uid']);
                $uinfo['status'] = $org_info['status'];
                $uinfo['count'] = $vol_info['count'];
                $uinfo['server_time'] = $vol_info['server_time'];
            }
        } else {
            $islogin = 0;
            $uinfo['photo'] = STATIC_SERVER_URL . '/gongyi/images/gy_lg_false.png';
        }
        $this->assign('isLogin', $islogin);
        $this->assign("uInfo", $uinfo);
        //根据用户的登陆状态以及用户的类型来选择志愿家下方的页面的显示模块
        $this->assign('userPageByUserType', $this->getUserPageByUserType($islogin, $uinfo['type'], $uinfo['status']));
        $this->display("Widget/Index:new_userinfo");
    }


    private function getUserPageByUserType($islogin, $type, $status)
    {
        if (!$islogin) {
            return 'Widget/Index/userinfo_nologin';
        }
        if ($type == 10) {    //个人
            switch ($status) {
                case 0:
                    return 'Widget/Index/userinfo_userlogin_not_apply';      //未申请
                case 10:
                    return 'Widget/Index/userinfo_userlogin_wait_examine';  //待审核
                case -1:
                    return 'Widget/Index/userinfo_userlogin_not_pass';      //未通过
            }
        }
        if ($type == 11) {
            $this->assign('user_logo_title', '认证个人');
            return 'Widget/Index/userinfo_userlogin_already_examine';    //认证个人
        }
        if ($type == 20) {
            return 'Widget/Index/userinfo_orglogin_not_apply';   //未提交认证组织
        }
        if ($type == 21) {//申请认证组织
            switch ($status) {
                case 0:
                    return 'Widget/Index/userinfo_orglogin_wait_examine';    //待审核
                case 1:
                    $this->assign('user_logo_title', '认证组织');
                    return 'Widget/Index/userinfo_orglogin_already_examine'; //审核通过
                case -1:
                    return 'Widget/Index/userinfo_orglogin_not_pass';    //审核未通过
            }
        }
    }


    //侧边栏
    function new_rightbar()
    {

        $this->display("Widget/Index:new_rightbar");
    }

    //图片路径
    private function imagePath($data)
    {
        foreach ($data as $key => $value) {
            $image = $value['image'];
            $data[$key]['image'] = \Lib\Image::getUrl($image, array(760, 400));// UploadedFile::getFileUrl($image,array(760,400),'banner');
        }
        return $data;
    }

    //获取小组信息
    public function getGroupInfo()
    {
        //$group = S('index_group_info');
        //if ($group) {
        //    return $group;
        //}
        $group = D('Subject')->getNewSubjectAndOtherInfo();
        //S('index_group_info', $group, 300);
        return $group;

    }

}
