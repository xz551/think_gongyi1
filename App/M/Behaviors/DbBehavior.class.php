<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/2/3
 * Time: 14:12
 */

namespace M\Behaviors;

/**检测数据库 频繁提交
 * Class DbBehavior
 * @package Home\Behaviors
 */
class DbBehavior {

    private $time_differ=2;

    private $time_session_key='action_time_session_key';

    public function  run(&$params){
        $time=session($this->time_session_key);
        if($time && time()-$time<$this->time_differ){
            if(IS_AJAX){
                echo "您操作太频繁了";exit;
            }else{
                redirect(U('/t/Tpl/dispatch_jump',array('error'=>urlSafeBase64_encode('您操作太频繁了'))));
            }
        }else{
            session($this->time_session_key,time());
        }
    }
}
