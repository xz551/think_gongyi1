<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;
class ConcurProcessModel extends Model{
    /*
     * 添加透明记录信息
     * @param int $concur_id 项目id
     * @param string $process  用户的操作说明
     * @param int $type 动作的类型
     * @param int $save 0-新添加透明记录，1-修改透明记录
     * @param int $oldid 修改透明记录时的主键id
     */
    public function addProcess($concur_id,$process,$user_type,$save=0,$oldid=0){
        $_d['uid'] = UserSession::getUser('uid');
        $_d['time'] = time();
        $_d['concur_id'] = $concur_id;
        $_d['process'] = $process;
        $_d['user_type'] = $user_type;
        if($save){
            return $this->where("id=%d",$oldid)->save($_d);
        }else{
            return $this->add($_d);
        }
    }
}
