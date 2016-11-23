<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/24
 * Time: 14:02
 */

namespace M\Controller;


use Org\Util\String;
use Think\Controller;

class TplController extends Controller
{
    public function dispatch_jump($error=""){
        if($error!=''){
            $this->error=urlSafeBase64_decode($error);
        }
        $this->waitSecond=3;
        $page=array('dispatch_jump','dispatch_jump_2');
        $this->display($page[String::randNumber(0,1)]);
    }
}