<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/9/17
 * Time: 09:51
 */

namespace Common\Model;


use Lib\UserSession;
use Think\Model;

class WxUserModel extends Model
{

    protected $connection =  'USER_CONTER'; //数据库连接

    /**
     * 解除绑定
     */
    public function unbind(){
        $this->where('uid=%d',UserSession::getUserId())->save(array(
            'uid'=>0,
            'updatetime'=>date('Y-m-d H:i:s',time())
        ));
    }
}