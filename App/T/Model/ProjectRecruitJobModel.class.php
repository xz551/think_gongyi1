<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;
use Think\Model;
/**
 * Description of ProjectRecruitJobModel
 *
 * @author Administrator
 */
class ProjectRecruitJobModel extends Model{
    /**
     * 根据项目编号 取得项目 所有职位需要的总人数
     */
    public function getRecruitNeedCount($projectid){        
        return M('ProjectRecruitJob')->where('project_id='.$projectid)->sum('need_count');
    }
}
