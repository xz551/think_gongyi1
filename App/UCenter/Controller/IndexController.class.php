<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        //热点招募信息: 项目图片 项目标题 发布人 地址 职位 已报名人数 招募人数
        $project=D('ProjectHot')->getHotList();
        //热点活动 :活动图片 活动名称 发布人 地址 活动时间 活动标签 报名人数
        
        $active=D('Active')->getHotList();
        $this->project=$project;
        $this->active=$active;
        $this->display();
    }
}