<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2016/8/1
 * Time: 16:04
 */

namespace T\Controller;


use Think\Controller;

class HrController extends Controller
{

    public function index(){
        $this->title="人才招聘-UI设计师";
        $this->display();
    }
}