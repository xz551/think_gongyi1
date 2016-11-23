<?php
/**
 * Created by PhpStorm.
 * User: zhangzhigang
 * Date: 15-1-29
 * Time: 下午5:22
 */

namespace Admin\Controller;


use Think\Controller;

class VolunteerController extends Controller{
    /**
     * 待认证用户数
     */
    public function wait_count(){
        $result=api('/volunteer/wait_review_count');
        echo $result;
    }
} 