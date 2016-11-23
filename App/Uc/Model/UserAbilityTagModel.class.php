<?php
namespace Uc\Model;
use Think\Model;
use Lib\UserSession;
class UserAbilityTagModel extends Model{
    protected $connection =  'USER_CONTER'; //数据库连接
    /**
     * 获取当前登录用户的技能标签，如果为空返回false
     */
    public function getField(){
        $tname = "tb_category_tag";
	$utname = "tb_user_ability_tag";
        $result = $this->where("$utname.uid=%d",  UserSession::getUser('uid'))->join("tb_category_tag ON $tname.id = $utname.tag_id")->select();	
        if($result){
            $list = array();
            foreach($result as $v){
                if($v){
                        $list[$v['tag_id']] .= $v['name'];
                }
            }
            return $list;
        }else{
            return false;
        }
    }
    
    /**
     * 删除当前登录用户的技能标签
     */
    public function delLabel(){
        
        $_w['uid'] = UserSession::getUser('uid');
        //查看需要删除的技能标签
        $lable = $this->where($_w)->select();
        $tag = $this->where($_w)->delete();
	
        if(tag === false){
            return false;
        }else{		
            if($lable){
		foreach($lable as $v){
                   $labelArr[] = $v['tag_id'];
                }
                //将删除的标签的使用次数都减去1
                return D('CategoryTag')->addorminus($labelArr,-1);
            }else{
                return true;
            }
        }
    }
    
    /**
     * 给当前登录用户添加关技能标签
     * @param array $label, 需要添加的标签数组
     */
    public function addLabel($label){
        $r = D('CategoryTag')->addorminus($label,1);	
	if($r){
            foreach($label as $v){
                $dataList[] = array('uid'=>  UserSession::getUser('uid'),'tag_id'=>$v);
            }
            return $this->addAll($dataList);
        }else{
            return false;
        }
        
        
    }
    
}
