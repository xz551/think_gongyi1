<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/5/20
 * Time: 14:00
 */

namespace Admin\Model;


use Think\Model;

class ProjectModel extends Model {

    /**
     * 定义项目当前状态
     */
    const STATUS_VERIFY_DENY    = -1;       // 项目审核失败
    const STATUS_EDITING        = 404;      //项目编辑中,未发布
    const STATUS_WAITFORCHECK   = 403;      // 项目审核状态
    const STATUS_NORMAL         = 100;      //正常状态&招募状态
    const STATUS_ENDED          = 888;      //项目完成
    const STATUS_CLOSED         = 889;      //项目关闭
}