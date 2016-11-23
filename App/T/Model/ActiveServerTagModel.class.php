<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;
use Think\Model;
/**
 * Description of ActiveServerTag
 *
 * @author Administrator
 */
class ActiveServerTagModel extends Model {
    /**
     * 根据活动编号 取得活动标签编号
     * @param type $activeid
     */
    public function getTagList($activeid,$limit=0){ 
       
        $mw=M('ActiveServerTag')->where('active_id='.$activeid);
        if($limit){
           $mw=$mw->limit($limit);
        }
        $c= $mw->getField('server_tag_id',true); 
        return $c;
    }
}