<?php
namespace M\Widget; 
use Think\Controller;
class ConditionWidget extends Controller{
    public function index($t=0){
        layout(false);
        //获取类别标签,如果缓存中存在则从缓存中获取
        if(S(C('interimCache.label'))){
            $label = S(C('interimCache.label'));
        }else{
            $r = D('T/CategoryServer')->order('`order`')->select();
            $arr = array();
            foreach($r as $k=>$v){
                $arr[$v['id']] = $v['name'];
            }
            S(C('interimCache.label'),$arr,C('interimCache.labelTime'));	
            $label = $arr;
        }

        //获取地域标签
        if(S(C('interimCache.area'))){
            $area = S(C('interimCache.area'));
        }else{
            $area = M('ProvinceCity')->where("class_type=1")->select();
            S(C('interimCache.area'),$area,C('interimCache.areaTime'));
        }
        $this->assign('tag',$t);
        $this->assign('area',$area);
        $this->assign('label',$label);
        $this->display("Widget/Condition:index");
    }
}
