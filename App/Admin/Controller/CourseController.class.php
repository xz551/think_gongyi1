<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/12/19
 * Time: 11:42
 */

namespace Admin\Controller;


use Lib\VideoUrlParser;
use Org\Util\String;
use Think\Controller;
use Think\Page;

class CourseController extends Controller {

    public function  u(){
        dump($_SESSION);
    }
    public function index($pageSize=20,$p=1,$video_name=''){
        $w=array('status'=>1);
        if($video_name){
            $w['video_name']=array('like','%'.$video_name.'%');
        }
        $count=M('course_video')->where($w)->count();
        $page=new Page($count,$pageSize);
        $totalPages = ceil($page->totalRows / $page->listRows); //总页数

        $video=M('course_video')->where($w)->order(" update_time desc ")->limit($page->firstRow.','.$page->listRows)->select();
        $video = array_map(array(__CLASS__, 'filter'), $video);
        $this->video=$video;
        $this->page=pagestring($p,$totalPages);
        $param=$_GET;
        $param['pageSize']='_pageSize_';


        $this->pageurl=U(ACTION_NAME,$param);
        $this->pageSize=$pageSize;
        $this->display();
    }
    private function filter($data){
        //{$vo['video_desc']|\\Org\\Util\\String::msubstr=###,0,60}
        $data['video_all_desc']=$data['video_desc'];
        $data['video_desc']=String::msubstr($data['video_desc'],0,60);

        return $data;
    }
    public function edit($id=0){
        if($id>0){
            $video=M('course_video')->find($id);
            $this->video=$video;
        }
        $this->type=M('dict')->where('fid=1')->select();

        $this->display();

    }
    public function videoinfo($url){
//    	dump(VideoUrlParser::parse($url));exit;
	    $data=VideoUrlParser::parse($url);
        $data['title']=urldecode($data['title']);
        $data['desc']=urldecode($data['desc']);
        $this->ajaxReturn($data);
    }
    public function editdo(){
        $data=M('course_video')->create();
        if($data){
            $type=$data['video_type_id'];
            $id_type=explode('_',$type);
            $data['video_type_id']=$id_type[0];
            $data['video_type_desc']=$id_type[1];
            $data['update_time']=now_time();
            if($data['id']){
                M('course_video')->data($data)->save();
                $this->success('修改成功',U('index'));
            }else{
		        $data['create_time']=now_time();
                $data['status']=1;
                $data['view']=0;
                M('course_video')->data($data)->add();
                $this->success('新增成功',U('index'));
            }
        }
    }
    public function r($id=0){
        echo M('course_video')->where('id='.$id)->save(array('status'=>0));
    }
}