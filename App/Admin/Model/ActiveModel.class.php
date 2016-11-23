<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/5/20
 * Time: 13:58
 */

namespace Admin\Model;


use Think\Model;

class ActiveModel extends Model {

    /**
     * 活动状态
     * 0 待审核
     * 1 审核通过
     * -1 审核不通过
     * @version 2013-9-16
     * @author wwpeng
     * @var unknown_type
     */
    const ACTIVE_STATUS_WAITING = 0;
    const ACTIVE_STATUS_NORMAL = 1;
    const ACTIVE_STATUS_REJECT = -1;
}