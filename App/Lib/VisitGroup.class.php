<?php
namespace Lib;
class VisitGroup {
    //记录访问过的小组
    public static function recordGroup($result){
	if(empty($_SESSION['group'][$result['id']])){       
            $_SESSION['group'][$result['id']]['name'] = $result['name'];
            $_SESSION['group'][$result['id']]['id'] = $result['id'];
            $_SESSION['group'][$result['id']]['image'] = $result['image'];
            $_SESSION['group'][$result['id']]['introduce'] = $result['introduce'];
            $_SESSION['group'][$result['id']]['seltime'] = time();
        }else{
            $_SESSION['group'][$result['id']]['seltime'] = time();    
        } 
    }
    
    //调用访问过的小组
    public static function getVisitGroup(){
        return $_SESSION['group'];
    }
    
}
