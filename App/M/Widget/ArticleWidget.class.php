<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace M\Widget;
use Think\Controller;

/**
 * Description of ArticleWidget
 *
 * @author Administrator
 */
class ArticleWidget extends Controller {

    public function item($data){
        $this->data=$data;
        $this->display("Widget/Article:item");
    }
}
