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
    /**
     * 自动定位模板文件
     * @access protected
     * @param string $template 模板文件规则
     * @return string
     */
    private function parseTemplate($template='') {
        if(is_file($template)) {
            return $template;
        }
        $depr       =   C('TMPL_FILE_DEPR');
        $template   =   str_replace(':', $depr, $template);

        // 获取当前模块
        $module   =  MODULE_NAME;
        if(strpos($template,'@')){ // 跨模块调用模版文件
            list($module,$template)  =   explode('@',$template);
        }
        $this->_theme_path=$this->getThemePath($module);

 
        // 分析模板文件规则
        if('' == $template) {
            // 如果模板文件名为空 按照默认规则定位
            $template = CONTROLLER_NAME . $depr . ACTION_NAME;
        }elseif(false === strpos($template, $depr)){
            $template = CONTROLLER_NAME . $depr . $template;
        }

        $file   =   $this->_theme_path.$template.C('TMPL_TEMPLATE_SUFFIX');
        if(C('TMPL_LOAD_DEFAULTTHEME') && $this->_theme_path != C('DEFAULT_THEME') && !is_file($file)){
            // 找不到当前主题模板的时候定位默认主题中的模板
            $file   =   dirname($this->_theme_path).'/'.C('DEFAULT_THEME').'/'.$template.C('TMPL_TEMPLATE_SUFFIX');
        }
        return $file;
    }
    /**
     * 获取当前的模板路径
     * @access protected
     * @param  string $module 模块名
     * @return string
     */
    private function getThemePath($module=MODULE_NAME){
        // 获取当前主题名称
        // 获取当前主题的模版路径
        $tmplPath   =   C('VIEW_PATH'); // 模块设置独立的视图目录
        if(!$tmplPath){
            // 定义TMPL_PATH 则改变全局的视图目录到模块之外
            $tmplPath   =   defined('TMPL_PATH')? TMPL_PATH.$module.'/' : APP_PATH.$module.'/'.C('DEFAULT_V_LAYER').'/';
        }
        return $tmplPath.$this->_theme;
    }

}