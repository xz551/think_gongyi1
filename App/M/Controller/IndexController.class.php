<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/3/26
 * Time: 10:35
 */

namespace M\Controller;


use Think\Controller;

class IndexController extends Controller {

    public function index(){
        //项目
        $project=D('project')->getProjectHot();
        $this->project=$project;
        //活动
        $active=D('Active')->getActiveList();
        $this->active=$active;
        //报道
        $article=D('T/Article')->getHotBaoDaoArticle(5);
        $this->article=$article;
        $this->display('index');
    }
}