<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/26
 * Time: 14:05
 */

namespace M\Widget;


use M\Session\MUserSession;

class ProjectWidget extends WController
{
    public function item($data){
        $this->data=$data;
        $this->display("Widget/Project:item");
    }
    public function job($data){
        $this->data=$data;
        $this->is_show_bm=!MUserSession::islogin() || MUserSession::is_vol();
        //检查用户是否参与过项目
        if(MUserSession::islogin()){
            $c=M('project_join')->where("project_id=%d and uid=%d and status=1 ",I('id'),MUserSession::getUserId())->count();
        }else{
            $c=0;
        }
        $this->user_join=$c>0;
        $this->display("Widget/Project:job");
    }
}