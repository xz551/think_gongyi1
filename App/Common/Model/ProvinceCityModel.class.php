<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/6/8
 * Time: 11:10
 */

namespace Common\Model;

use Think\Model;

/***
 * 地址操作类
 * Class ProvinceCity
 * @package Common\Model
 */
class ProvinceCityModel extends Model{

    /**
     * 取得所以信息 省市区
     * 缓存一年
     */
    public function getAll(){
        $key='_province_city_getall';
        $all=S($key);
        if(!$all)
        {
            $all=M('ProvinceCity')->field("id,class_parent_id,class_name,class_type")->select();
            S($key,$all,3600*30*12);
        }
        return $all;
    }

    /**
     * 检索的父级编号
     * @var int
     */
    private $parent_id=0;
    /**
     *根据编号 取得下级 的 集合
     * 传递0 或不传  取得第一级
     */
    public function getChildrenCity($pid=0){
        $all=$this->getAll();
        $this->parent_id=$pid;
        return array_filter($all,array(__CLASS__, 'filter'));
    }

    private $city_id;
    /**
     * 根据城市编号取得城市对象
     * 返回值是一个对象集合
     * @param $pid
     */
    public function getCity($pid){
        $all=$this->getAll();
        $this->city_id=$pid;
        $obj=array_filter($all,array(__CLASS__, 'filter_name'));
        return is_array($obj)?reset($obj):$obj;
    }
    private function filter_name($data){
        if($data['id']==$this->city_id){
            return true;
        }
        return false;
    }

    /**
     * 过滤函数
     * @param $data
     * @return bool
     */
    private function filter($data){
        if($data['class_parent_id']==$this->parent_id){
            return true;
        }
        return false;
    }
    /**
     * 获得省份信息
     */
    public function province(){
    	return $this->getChildrenCity();
    }
}