<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/4/13
 * Time: 11:08
 */

namespace Lib;

/**
 * 类包的创建工厂 Factory的简写
 * Class F
 * @package Lib
 */
class F {
    /**
     * 创建一个实例 Instance 简写
     * @param $class_name
     */
    public static function i($class_name){
        require_once(C('THINK_PUBLIC').'/l/'.$class_name.'.php');
        return new $class_name();
    }
}