<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/28
 * Time: 13:09
 */

namespace T\Controller;


use Think\Controller;

class TplController extends Controller {

    public function dispatch_jump($error=""){
        if($error!=''){
            $this->error=urlSafeBase64_decode($error);
        }
        $this->waitSecond=3;
        $this->display();
    }
} 