<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Widget;

use Think\Controller;

/**
 * Description of MenuWidget
 *
 * @author zhangzhigang
 */
class MenuWidget extends Controller {

    //put your code here
    public function init() {
        layout(false);
        $items = array(
            array('label' => '用户管理', 'url' => 'javascript:;', 'items' => array(
                    array('label' => '用户列表', 'url' =>'/user/userlist'),
                    array('label' => '认证用户审核', 'url' =>'/user/volunteerverifylist'),
                )),
            array('label' => '组织管理', 'url' => 'javascript:;', 'items' => array(
                    array('label' => '组织列表', 'url' =>'/user/orglist'),
                    array('label' => '组织审核', 'url' => '/user/orgverifylist'),
                )),
            
            array('label' => '项目管理', 'url' => 'javascript:;', 'items' => array(
                    array('label' => '项目列表', 'url' => '/project/admin'),
                    array('label' => '项目审核', 'url' => '/project/verify'),
                    array('label' => '热点项目', 'url' => '/project/hotadmin'),
                )),
            array('label' => '证书管理', 'url' => 'javascript:;', 'items' => array(
                    array('label' => '证书列表', 'url' => '/Admin/Certificate/carlist'),
                    array('label' => '证书颁发', 'url' => '/Admin/Certificate/Issued')
                )),
            array('label' => '征集项目管理', 'url' => 'javascript:;', 'items' => array(
                    array('label' => '征集项目列表', 'url' => '/event/index'),
                    array('label' => '征集项目添加', 'url' => '/event/create'),
                    array('label' => '征集项目赞助商', 'url' => '/event/supportlist'),
                )),
            array('label' => '活动管理', 'url' => 'javascript:;', 'items' => array(
                    array('label' => '活动列表', 'url' => '/active/admin'),
                    array('label' => '活动审核', 'url' => '/active/verify'),
                )),
            array('label' => '专题管理', 'url' => 'javascript:;', 'items' => array(
                    array('label' => '专题列表', 'url' => '/collection/index'),
                    array('label' => '专题创建', 'url' => '/collection/create'),
                )),
            array('label' => '反馈管理', 'url' => 'javascript:;', 'items' => array(
                    array('label' => '反馈列表', 'url' => '/proposal/admin'),
                )),
            array('label' => 'Banner管理', 'url' => 'javascript:;', 'items' => array(
                    array('label' => 'Banner列表', 'url' => '/banner/admin'),
                    array('label' => 'Banner添加', 'url' => '/banner/create'),
                )),
            array('label' => '培训服务', 'url' => 'javascript:;', 'items' => array(
                array('label' => '培训视频', 'url' => '/Admin/course')
            )),
            array('label' => '客户端管理', 'url' => 'javascript:;', 'items' => array(
                    array('label' => 'Banner列表', 'url' => '/mobile/bannerlist'),
                    array('label' => 'Banner创建', 'url' => '/mobile/bannercreate'),
                    array('label' => '版本列表', 'url' => '/mobile/versionlist'),
                    array('label' => '发布新版本', 'url' => '/mobile/versioncreate'),
                )),
            array('label' => '权限设置', 'url' => 'javascript:;', 'items' => array(
                    array('label' => '前台权限设置', 'url' => '/right/index'),
                    array('label' => '后台权限设置', 'url' => '/adminright/index'),
                )),
            array('label' => '用户/用户组', 'url' => 'javascript:;', 'items' => array(
                    array('label' => '管理员管理', 'url' => '/administrator/administratorlist'),
                    array('label' => '管理员日志', 'url' => '/adminlog/admin'),
                    array('label' => '资料修改', 'url' => '/administrator/modify'),
                )),
        );
        $this->assign('items', $items);
        $this->display('Widget/Menu:init');
    }

}
