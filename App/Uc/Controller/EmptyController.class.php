<?php

namespace Uc\Controller;
use Think\Controller;
use Lib\UserSession;
class EmptyController extends Controller{
	public function index(){
		if(UserSession::getUser('uid')){ 
			redirect(U('user/index','uid='.UserSession::getUser('uid')));
		}else{ 
			redirect(UCENTER.'/user/login?returnurl='.urlSafeBase64_encode(SERVER_VISIT_URL.'/uc'));
		}
	}
}

?>