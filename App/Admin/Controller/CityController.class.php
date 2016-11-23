<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/23 0023
 * Time: 上午 10:09
 */

namespace Admin\Controller;


use Think\Controller;

class CityController extends Controller
{
    public function getChildrenCity($id){
        $data=D('ProvinceCity')->getChildrenCity($id);
        foreach($data as $k=>$v){
            $result[$v['id']] = $v['class_name'];
        }
        $this->ajaxReturn($result);
    }
}