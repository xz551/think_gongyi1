<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/10/17
 * Time: 15:07
 */
namespace Common\Behaviors;

use Think\Behavior;
use Think\View;

class MobileBehavior extends Behavior {

    private $_theme="";
    private $_theme_path="";
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @param mixed $params 行为参数
     * @return void
     */
    public function run(&$params)
    {
        C('DEFAULT_THEME','Default');
        import('Vendor.MobileDetect');
        $m=new \MobileDetect();
        if($m->isMobile()){
	    C('DEFAULT_THEME','Mobile');
            $this->_theme="Mobile/";
            $file=$this->parseTemplate($params);

            if(!file_exists($file)){ 
                C('DEFAULT_THEME','Default');
            }
        }
    }

}