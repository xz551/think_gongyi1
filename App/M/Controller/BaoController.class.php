<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/8/26
 * Time: 18:07
 */

namespace M\Controller;


use Think\Controller;

class BaoController extends Controller
{

    public function index($id=0){
        $article=D('T/Article')->find($id);
        if(!$article){
            $this->error('报道不存在');
        }
        $this->title=$article['title'];
        $this->article=$article;
        $this->display('index');
    }
    /**
     *
     */
    public function all($last_id='',$pagesize=20){
        layout(!IS_AJAX);
        $this->title='公益报道';
        $cache_key='m_bao_dao_list_'.$last_id;
        $article=S($cache_key);
        if(!$article){
            $article=D('T/Article')->getHotBaoDaoArticle($pagesize,$last_id);
            S($cache_key,$article,60*10);
        }
        $this->data=$article;
        $this->display(IS_AJAX?'ajax_all':'all');
    }
}