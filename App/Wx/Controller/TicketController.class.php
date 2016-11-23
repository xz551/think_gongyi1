<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/5/6
 * Time: 18:02
 */

namespace Home\Controller;


use Think\Controller;

class TicketController extends Controller {
    public function index($url){
        $at=new \Wx\Service\WxService();
        $result =$at->getJsSdkSignature($url);
        echo $result;
    }
}