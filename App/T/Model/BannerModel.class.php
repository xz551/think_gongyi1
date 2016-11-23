<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;

use Think\Model;

/**
 * Description of BannerModel
 *
 * @author Administrator
 */
class BannerModel extends Model {

    //put your code here
    /**
     * BANNER图片的显示状态  
     * 0 正常  
     * -1 隐藏
     * @var unknown_type
     */
    const BANNER_STATUS_SHOW = 0;
    const BANNER_STATUS_HIDDEN = -1;

    public function getStatusName() {
        switch ($this->status) {
            case self::BANNER_STATUS_SHOW:
                return '显示';
                break;
            case self::BANNER_STATUS_HIDDEN:
                return '隐藏';
                break;
        }
    }

}
