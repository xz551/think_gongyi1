<?php
namespace Uc\Model;
use Think\Model;
use Lib\UserSession;
class UserCategoryServerTagListModel extends Model{
    protected $connection =  'USER_CONTER'; //数据库连接
    /**
     * 添加用户的关注领域
     * @param $arr为用户关注领域的id组成的数组
     */
    public function addLabel($arr){
        foreach($arr as $v){
            $dataList[] = array('server_tag_id'=>$v,'uid'=>  UserSession::getUser('uid'));
        }
        return $this->addAll($dataList);
    }
    /**
     * 删除当前登录用户的关注领域
     */
    public function delLabel(){
        $r = $this->where('uid=%d',UserSession::getUser('uid'))->delete(); 
        if($r === false){
            return false;
        }else{
            return true;
        }
    }
    
    
   
   
    
    
    
}