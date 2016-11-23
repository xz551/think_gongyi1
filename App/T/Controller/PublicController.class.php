<?php
namespace T\Controller;
use Think\Controller;
class PublicController extends Controller{
    /**
     * 根据省ID获取市列表
     */
    public function getCity($cid,$level){
        $_w['class_type'] = $level;
        $_w['class_parent_id'] = $cid;
        $r = M('ProvinceCity')->where($_w)->select();
        $city = array();
        foreach($r as $k=>$v){
            $city[$v['id']] = $v['class_name'];
        }
        echo json_encode($city);
    }
}
