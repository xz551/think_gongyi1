<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;
use Think\Model;

/**
 * Description of ArticleModel
 *
 * @author Administrator
 */
class ArticleModel extends Model{
    
    /**
     * 文章类型：公益舆情
     */
    const Category_Yuqing=35;
    
    /**
     * 文章类型：公益报道
     */
    const Category_Baodao=36;
    /**
     * 文章类型：头条新闻 正式库信息
     */
    const Type_TouTiao=3;

    protected $connection       =   'db_cms';
    //put your code here
    public function getArticle($category=35,$limit=4,$index_show=1){
        $data=getApiContent('http://www.gy.com/api/index/Article/category/'.$category.'/limit/'.$limit.'/index_show/'.$index_show);
       // $data = M('HotNews')->order("create_date desc")->limit($limit)->select(); 
        return $data;
    }
    /**
     * 取得头条新闻
     * @param type $category 文章类型
     * @param type $limit 几条
     */
    public function getHotArticle($category=0,$limit=4,$last_id=''){
        $w=array('type'=>  self::Type_TouTiao);
        if($category){
            $w['category']=$category;
        }
	    $w['status']=array("NEQ",'-1');
        if($last_id!==''){
            $w['id']=array("lt",$last_id);
        }
        $data= $this->where($w)->order('Id desc')->limit($limit)->select();
	    return $data;
    }
    /**
     * 公益公益 头条新闻
     * @param type $limit
     * @return type
     */
    public function getHotYuQingArticle($limit=4){
        return $this->getHotArticle(self::Category_Yuqing, $limit);
    }
    /**
     * 公益报道 头条新闻
     * @param type $limit
     * @return type
     */
    public function getHotBaoDaoArticle($limit=4,$last_id=''){
         return $this->getHotArticle(self::Category_Baodao, $limit,$last_id);
    }
}
