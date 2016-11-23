<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace CmsApi\Controller;
use Think\Controller;
/**
 * Description of HotnewController
 *
 * @author Administrator
 */
class HotnewController extends Controller {
    
    public function hot($limit=5){
        $this->ajaxReturn(D('HotNews')->getHotList());
    }
}
