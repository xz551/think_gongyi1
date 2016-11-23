<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;
use Lib\Image;
use Think\Model;
use Lib\Image\UploadedFile;
use Lib\City;
use Lib\Helper;
/**
 * Description of ProjectHotModel
 *
 * @author Administrator
 */
class ProjectHotModel  extends Model{
    //热点招募 项目
    private $_PROJECT_HOT_LIST_KEY='PROJECT_HOT_LIST_KEY';
    public function getHotList(){
        $project=S($this->_PROJECT_HOT_LIST_KEY);
        if($project){
            return $project;
        }
        $project=M('ProjectHot as ph')->join("left join tb_project as p on ph.project_id=p.id")->field("p.*")->order('ph.sort asc ,ph.id asc')->limit(4)->select();
        foreach ($project as $key => $value) {
            $projectid=$value['id'];
            //图片
            $image =Image::getUrlThumbCenter($value['show_image'], array(291, 220));// UploadedFile::getFileUrl($value['show_image'], array(291, 220), 'project');
            //发布者
            $projectCreatorData = getApiContent(ucUrl('/api/user/safeinfo',array('uid'=>$value['creator'])),false,true); 
             
             //服务区域
            $server_area = sprintf('%s %s', City::getName($value['provinceid']), City::getName($value['cityid']));
            //已报名
            $count=D('ProjectJoin')->joinCount($projectid);
            //招募人数
            $needcount=D('ProjectRecruitJob')->getRecruitNeedCount($projectid);
            
            $project[$key]['show_image']=$image;
            $project[$key]['creator']=Helper::Utf8Substr($projectCreatorData['nickname'],0,13);
            $project[$key]['area']=$server_area;
            $project[$key]['usercount']=$count;
            $project[$key]['needcount']=$needcount;
            $project[$key]['url']=$this->ProjectViewUrl($projectid);
            //name
            $project[$key]['name']=Helper::Utf8Substr($value['name'],0,22);
        }
        S($this->_PROJECT_HOT_LIST_KEY,$project,C('CACHE_TIME'));
        return $project;
    }
    /**
     * 仍然链接到原来的地址
     * 待项目视图页移动到thinkphp上后 切换
     *用U();
     */
    private function ProjectViewUrl($projectid){
        return YI_JUAN.'/project/view/id/'.$projectid.'.html';
    }
}
