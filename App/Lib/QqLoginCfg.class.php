<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/9/11
 * Time: 11:07
 */

namespace Lib;

/**qq
 *登录配置文件
 * Class QqLoginCfg
 * @package Lib
 */
class QqLoginCfg
{
    public $appid="101248760";
    public $appkey="acb7f4685e4272c1f470255dab6e8623";
    public $callback='';//
    public function __construct(){
        $this->callback=WEB_SITE."/t/Author/qq_callback";
    }
}