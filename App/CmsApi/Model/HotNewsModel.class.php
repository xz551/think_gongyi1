<?php

namespace Api\Model;

use Think\Model;

/**
 * 
 * 幻灯片切换图
 * @author Administrator
 */
class HotNewsModel extends Model { 
    public function getHotList($limit = 5) { 
        $data = M('HotNews')->order("create_date desc")->limit($limit)->select();

        return $data;
    }

}
