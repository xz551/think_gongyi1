<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Widget; 

/**
 * Description of ArticleWidget
 *
 * @author Administrator
 */
class ArticleWidget extends WController {

    //put your code here
    //put your code here
    public function init() {
	layout(false);
        $yuqing_key = "article_yuqing";
        $baodao_key = "article_baodao";

        $yuqing = S($yuqing_key);
        $baodao = S($baodao_key);
        if (!$yuqing) {
            $yuqing =  \T\Model\ArticleModel::getArticle(35);
            S($yuqing_key, $yuqing, 60);
        }
        if (!$baodao) { 
            $baodao =D('Article')->getHotArticle();// \T\Model\ArticleModel::getArticle(36);
            S($baodao_key, $baodao, 60);
        }

        $this->assign('yuqing', $yuqing);
        $this->assign('baodao', $baodao);


        $this->display("Widget/Article:init");
    }

}
