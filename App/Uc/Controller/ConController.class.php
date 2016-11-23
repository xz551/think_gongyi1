<?php
namespace Uc\Controller;
use Think\Controller;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
class ConController extends Controller{
    public function index(){
        $this->display();
    }
}
