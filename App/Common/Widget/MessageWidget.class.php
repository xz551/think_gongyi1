<?php
/**
 * 迷你登录框
 */
namespace Common\Widget; 
use Think\Controller;
class MessageWidget extends Controller{
	public function privateLetter(){
		layout(false);
		$this->display(T("Common@Widget/Message:privateLetter"));
	}
}