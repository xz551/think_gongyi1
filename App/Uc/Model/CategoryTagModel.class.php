<?php
namespace Uc\Model;
use Think\Model;
use Lib\UserSession;
class CategoryTagModel extends Model{
    protected $connection =  'USER_CONTER'; //数据库连接
    
    /**
     * 检测技能标签是否存在，存在则返回id,不存在则添加，返回id组成的数组
     * @param type $labelArr  需要添加的技能标签
     */
    public function checkLabel($labelArr){
        $arr = array();
        foreach($labelArr as $val){
            $_w = array(
                'name'=>$val,
                'type'=>1,
            );
            $id = $this->where($_w)->find();
            if($id){
                $arr[] = $id['id'];
            }else{
                $data['name'] = $val;
                $data['type'] = 1;
                $newid = $this->add($data);
                if($newid){
                    $arr[] = $newid;
                }else{
                    return false;
                }
            }
        }
        return $arr;
    }
    
    
    /**
     * 将指定技能标签加上或减去固定数
     * @param array $label 技能标签数组
     * @param int $type 1,加1,-1，减去1
     */
    public function addorminus($label,$type=1){
	$label = implode(',',$label);
	if($type == 1){
		$order = "`order` + 1";
	}else{
		$order = "`order` - 1";
	}
	$sql = "UPDATE `tb_category_tag` SET `order`=$order WHERE id in ($label)";
	return $this->execute($sql);    
	
    }
    
    
}
