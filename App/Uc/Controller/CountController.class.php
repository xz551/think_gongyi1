<?php
namespace Uc\Controller;
use Think\Controller;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
class CountController extends Controller{
    public function index(){
        $this->display();
    }
    
    
    public function getcount(){
    	layout(false);
	$this->assign('uid',UserSession::getUser('uid'));
    	$this->display();
    }


}
