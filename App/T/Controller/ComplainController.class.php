<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/23 0023
 * Time: 下午 8:24
 */

namespace T\Controller;


use Lib\UserSession;
use Think\Controller;

/**
 * 投诉处理
 * Class ComplainController
 * @package T\Controller
 */
class ComplainController extends Controller
{

    /**
     * 局部，显示一个投诉的框
     */
    public function index($pid=0,$cuserid=0,$cusername="",$type=2){
        layout(false);
        if(empty(UserSession::getUserId())){
            //没有登录
            echo 0;
            exit;
        }
        $this->uid=UserSession::getUserId();
        $this->uname=UserSession::getUserName();
        $this->pid=$pid;
        $this->cuserid=$cuserid;
        $this->cusername=$cusername;
        $this->type=$type;
        $this->display();
    }

    /**
     * 提交投诉信息
     */
    public function sub(){ 
        $complain=D('Complain');  
        $data=$complain->create();
        if($data){
            $data['create_time']=time();
            $data['status']=0;
            $result=$complain->add($data);
            if($result){
                echo 1;
            }else{
                echo $complain->getError();
            }
        }else{
            echo $complain->getError();
        }
    }
}