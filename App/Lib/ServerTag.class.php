<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2016/5/17
 * Time: 11:06
 */

namespace Lib;


class ServerTag
{
    public static function all(){
        return  array(
            '1'=>'其它',
            '2'=>'支教助学',
            '3'=>'医疗救助',
            '4'=>'环境保护',
            '5'=>'人文关怀',
            '6'=>'心愿梦想',
            '7'=>'海外服务',
            '8'=>'助残助困',
            '9'=>'扶幼敬老',
            '10'=>'紧急援助',
            '11'=>'社区服务'
        );
    }
    public static function get($key){
        $v=array();
        $all_tag=self::all();
        if(is_array($key)){
            foreach($key as $value){
                $tag=$all_tag[$value];
                array_push($v,$tag);
            }
        }else{
            $tag=$all_tag[$key];
            array_push($v,$tag);
        }
        return $v;
    }
}