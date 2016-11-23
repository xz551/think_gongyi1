<?php
namespace Common\Model;
use Think\Model;

class CategoryServerModel extends Model{

    protected $connection = USER_CONTER;   //数据库连接

    /**
     * 获取标签列表
     */
    public function getAllLabel() {
        if(S('interimCache_label')){
            return S('interimCache_label');
        }else{
            $r = $this->order('`order`')->select();
            $arr = array();
            foreach ($r as $k => $v) {
                $arr[$v['id']] = $v['name'];
            }
            S('interimCache_label', $arr, 3600 * 24 * 365);
            return $arr;
        }
    }
    
    /**
     * 根据标签序列获取标签名称
     */
    public function getLabelName($id){
        $id = rtrim($id,',').',';
        $labelArr = explode(',',$id);
        $arr = array();
        $list = $this->getAllLabel();
        foreach($labelArr as $k=>$v){
            if($v){
                $arr[$k] = $list[$v];
            }
        }
        return $arr;
    }

}
