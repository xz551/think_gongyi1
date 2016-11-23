<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2016/5/16
 * Time: 07:41
 */

namespace Admin\Controller;


use Think\Controller;

/**
 * 征集
 * Class EventController
 * @package Admin\Controller
 */
class EventController extends Controller
{
    /**
     * 查看项目
     */
    public function project($p=1,$pageSize=25,$event=0){
        $count=M('project')->where("event=".$event)->count();
        $pageCount=ceil($count/$pageSize);
        $data=M('project')->where("event=".$event)->order(" sort desc ")->limit($p.','.$pageSize)->select();
        $this->data=$data;
        $this->pageHtml=pageHtml($p,$pageCount);
        $this->page=array('count'=>$count,'p'=>$p,'pageCount'=>$pageCount);
        $this->display();
    }
}