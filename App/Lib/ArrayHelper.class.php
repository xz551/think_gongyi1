<?php

/**
 * 数组的特殊处理函数
 * @version 2013-5-30
 * @author wwpeng
 *
 */

namespace Lib;

class ArrayHelper {

    /**
     * 将数组中的元素全部trim
     * @version 2013-5-30
     * @author wwpeng
     * @param array $array
     * @return array
     */
    public static function arrayTrim($array) {
        foreach ($array as $key => $value) {
            $array[$key] = trim($value);
        }
        return $array;
    }

    /**
     * 将数组中的元素全部trim
     * 去除重复元素
     * 去除空元素
     * @version 2013-5-30
     * @author wwpeng
     * @param array $array
     * @return array:
     */
    public static function arrayClean($array) {
        
        $array = self::arrayTrim($array); 
        $array = array_filter($array);
        $array = array_unique($array);
        return $array;
    }

    /**
     * 将一个数组内容的编码 从utf8 变为 gb2312 一维数租
     * @version 2013-7-25
     * @author wwpeng
     * @param unknown_type $arr
     * @return string
     */
    public static function arrayUTF8toGBK($arr) {
        foreach ($arr as $key => $value) {
            $arr[$key] = iconv('utf-8', 'GBK', $value);
        }
        return $arr;
    }

    /**
     * 将一个数组中的两个key所对应的值转换为 key=>value 数组
     * 参考 CHtml::listData 的功能
     * @param array $data
     * @param string $key
     * @param string $value
     * @return multitype:unknown
     */
    public static function listData($data, $key = 'id', $value = 'name') {
        $result = array();
        foreach ($data as $v) {
            $result[$v[$key]] = $v[$value];
        }
        return $result;
    }

}
