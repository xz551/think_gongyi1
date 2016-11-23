<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace T\Widget;
/**
 * 招募项目
 */
class RecruitWidget extends WController{
    public function searchTerms(){
        $this->display("Widget/Recruit:searchTerms");
    }
    public function projectList(){
        $this->display("Widget/Recruit:projectList");
    }
    public function projectRecommed(){
        $this->display("Widget/Recruit:projectRecommed");
    }
}
