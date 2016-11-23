<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/4/13
 * Time: 10:27
 */

namespace Lib;


class Init {
    public static function getInit($class_name)
    {
        $c=require_once(C('THINK_PUBLIC').'/l/'.$class_name.'.php');
        return $c;
    }
}