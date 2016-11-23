<?php
namespace Lib;
use Lib\Image;
use Lib\UserSession;
/**
 * 用户的动态
 */
class Dynamic {
    //添加一个动态
    public static function addDynamic($type,$pid,$idlist=''){
        $uid = UserSession::getUser('uid');
        
        if($type == 'Primaries' || $type='SuccessRaise'){
            
        }else{
            
            
        }
        
    }
    //获取用户动态
    public function getDynamic(){
       
        //获取项目，活动，小组，话题等的信息 
        if(array_key_exists($v['type'],$this->getProArray())){  //项目信息
            $info = M('project')->where('id=%d',$v['pid'])->find();
            $arr[$k]['image'] = substr($info['show_image'],0,5) == 'http:'?$info['show_image']:Image::getUrl($info['show_image'], array(100, 75));// UploadedFile::getFileUrl($info['show_image'], array(100, 75),'project');
            $arr[$k]['title'] = $info['name'];
            $arr[$k]['introduce'] = $info['summary'];
            $arr[$k]['url'] = $this->getProUrl($v['pid']);
        }elseif(array_key_exists($v['type'],$this->getActArray())){ //活动信息
            $info = M('active')->where('id=%d',$v['pid'])->find();     
            $arr[$k]['image'] = substr($info['image'],0,5) == 'http:'?$info['image']:Image::getUrl($info['image'], array(100, 75));// UploadedFile::getFileUrl($info['image'], array(100, 75),'active');
            $arr[$k]['title'] = $info['name'];
            $arr[$k]['introduce'] = $info['description'];
            $arr[$k]['url'] = $this->getActUrl($v['pid']);
        }elseif(array_key_exists($v['type'],$this->getGroupArray())){//小组信息
            $info = D('group')->getGroupInfo($v['pid']);
            $arr[$k]['image'] = substr($info['image'],0,5) == 'http:'?$info['image']:Image::getUrl($info['image'], array(100, 75));// UploadedFile::getFileUrl($info['image'], array(100, 75),'group');
            $arr[$k]['title'] = $info['name'];
            $arr[$k]['introduce'] = $info['introduce'];
            $arr[$k]['url'] = $this->getGroupUrl($v['pid']);
        }elseif(array_key_exists($v['type'],$this->getSubjectArray())){//话题信息
            $arr[$k]['url'] = $this->getSubjectUrl($v['pid']);                              
            $arr[$k]['title'] = $info['title'];
            $arr[$k]['introduce'] = $info['content'];                             
            $arr[$k]['info'] = M('subject')->where('id=%d',$v['pid'])->find();
        }
        if($v['type'] == 'Primaries'){
            $user = D('User')->getUserInfo($v['data']->primaries_uid);
            $arr[$k]['user'] = $this->getUserImageInfo($user);
        }elseif($v['type'] == 'SuccessRaise'){
            $user = D('User')->getUserInfo($v['data']->join_id);
            $arr[$k]['user'] = $this->getUserImageInfo($user);
        }   

    }
    
    
    
    //一对一的关系
    private function getTypeArray(){
        return array(
            'ProjectCreate' =>  '创建了项目',
            'ProjectEdit'   =>  '修改了项目',
            'ProjectBack'   =>  '在项目中发布了反馈',
            'EnjoyRecruit'  =>  '报名参加招募',
            'ActiveJoin'    =>  '报名参加活动',
            'ActiveCreate'  =>  '发起了活动',
            'ActiveEdit'    =>  '修改活动信息',
            'ApplyRaise'    =>  '希望认捐救助',
            'SuccessRaise'  =>  '接受认捐',
            'GroupEdit'     =>  '创建了小组',
            'GroupEdit'     =>  '修改了小组信息',
            'SubjectCreate' =>  '发布了话题',
            'SubjectEdit'   =>  '修改了话题',
            'SubjectBack'   =>  '回复了话题', 
        );
    }
    //一对多的关系
    private function getOneToMayArray(){
        return array(
            'Primaries'     =>  '发布了入选名单',
            'SuccessRaise'  =>  '接受了捐助',
        );
    }
    
    //获取活动的信息的类型
    private function getActArray(){
        return array(
            'ActiveJoin'    =>  '报名参加活动',
            'ActiveCreate'  =>  '发起了活动',
            'ActiveEdit'    =>  '修改活动信息'
        );   
    }
    
    //用户获取项目信息的类型
    private function getProArray(){
        return array(
            'ProjectCreate' =>  '创建了项目',
            'ProjectEdit'   =>  '修改了项目',
            'EnjoyRecruit'  =>  '报名参加招募',
            'ApplyRaise'    =>  '希望认捐求助',
            'ProjectBack'   =>  '在项目中发布了反馈',
            'Primaries'     =>  '发布了入选名单',
            'SuccessRaise'  =>  '接受了捐助'
        );
    }
    
    //用于获取小组信息的类型
    private function getGroupArray(){
        return array(
            'GroupEdit'     =>  '创建了小组',
            'GroupEdit'     =>  '修改了小组信息',    
        );
    }
    //用户获取话题信息的类型
    private function getSubjectArray(){
        return array(
            'SubjectCreate' =>  '发布了话题',
            'SubjectEdit'   =>  '修改了话题',
            'SubjectBack'   =>  '回复了话题',
        );
        
    }
    
    
    //获取项目的URL地址
    private function getProUrl($id){
        return "http://www.719kj.com/project/view/id/".$id.".html";
    }
    //获取活动的URL地址
    private function getActUrl($id){
        return "http://www.719kj.com/active/view/id/".$id.".html";
    }
    
    
    
   
    
    
}
