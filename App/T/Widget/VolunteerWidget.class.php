<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Widget;
use Think\Page;
/**
 * 认证用户
 *
 * @author Administrator
 */
class VolunteerWidget extends WController{
    /**
     * 认证用户
     */
    public function vlist(){
    layout(false);
        //认证用户
        $volunteer=D('UCenter')->volunteer();
        //dump($volunteer);exit;
        $this->assign('voliumteer', $volunteer['item']); 
        
        $totalCount=$volunteer['page']['totalCount'];        
        $currentPage=$volunteer['page']['currentPage'];
        $countPage=$volunteer['page']['countPage'];
        $sizePage=$volunteer['page']['sizePage']; 
        $this->assign('pagestring',$this->page($totalCount, $sizePage));
        //dump($this->page($totalCount, $sizePage));
        $this->display("Widget/Volunteer:vlist");
    }
    private function page($totalCount,$sizePage){
    layout(false);
        /**
         *  'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
        'prev'   => '<<',
        'next'   => '>>',
        'first'  => '1...',
        'last'   => '...%TOTAL_PAGE%',
        'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
         */
        $Page       = new Page($totalCount,$sizePage);// 实例化分页类 传入总记录数和每页显示的记录
        $Page->setConfig('theme','<li>%FIRST%</li> <li>%UP_PAGE%</li> <li> %LINK_PAGE% </li> <li>DOWN_PAGE%</li> <li> %END%</li>');
        return $Page->show();
    }
}
