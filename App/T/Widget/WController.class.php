<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace T\Widget;
use Think\Controller;
/**
 * Description of WController
 *
 * @author Administrator
 */
class WController extends Controller {
    public function __construct() {
        layout(false);
        parent::__construct();
    }
    //put your code here
    protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
       if(empty($templateFile)){
           return;
       }
        parent::display($templateFile,$charset,$contentType,$content,$prefix);
    }
}
