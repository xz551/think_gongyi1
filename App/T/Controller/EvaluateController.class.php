<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/24
 * Time: 16:30
 */

namespace T\Controller;


use Lib\CJSON;
use Lib\Helper;
use Think\Controller;

class EvaluateController extends Controller{

    /**
     * @param $project_id
     */
    public function count($project_id){
        $c= M('evaluate')->where(array('proID'=>$project_id))->count();
        $callback=I('get.callback');
        echo $c;exit;
         echo $callback.'({"c":"'.$c.'"})';
        exit;
    }

    /**项目评价内容
     * @param $project_id
     */
    public function content($project_id){
        layout(false);
        //header('Content-type: application/x-javascript');
        $c=M('evaluate')->where(array('proID'=>$project_id))->select();
        $content=array();
        foreach($c as $key=>$value){
            $user = getApiContent(ucUrl('/api/user/safeinfo',array('uid'=>$value['userID'])),false,true);
			$time =date("Y-m-d H:i:s",$value['create_time']);
            array_push($content,array(
                "nickname"=>$user['nickname'],
                "photo"=>$user['photo_middle'],
                'content'=>$value['content'],
                'level'=>$value['level'],
                'time'=>$time
            ));
        }
        $this->data=$content;
        $this->display();
    }

    private function map($value){

        //发布者
        $user = getApiContent(ucUrl('/api/user/safeinfo',array('uid'=>$value['userID'])),false,true);
        $value['user']=$user;//['photo_middle'];
        return $value;
    }

} 