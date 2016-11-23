<?php
namespace T\Model;
use Think\Model;

class ProjectCategoryServerTagListModel extends Model{
    /**
     * 根据项目ID获取项目标签
     */
    public function getListByLabel($projectid){
        $list = $this->where('project_id=%d',$projectid)->select();
        if($list){
            $tagList = '';
            foreach($list as $v){
                $tagList .= $v['server_tag_id'].',';
            }
            return D('CategoryServer')->getLabelName($tagList);
        }
        return;
    }
}
