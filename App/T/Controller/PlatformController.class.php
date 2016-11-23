<?php
/**
 * Created by PhpStorm.
 * User: HuaYun
 * Date: 2016/4/26
 * Time: 13:09
 */

namespace T\Controller;


use Think\Controller;

/**
 * 平台支持
 * Class PlatformController
 * @package T\Controller
 */
class PlatformController extends Controller
{
    /**
     * 公益咨询
     */
    public function article($c=9){
        layout(false);
        $key = "GONGYI_BAODAO";
        $baodao = S($key);
 
        if (!$baodao) {
            $baodao =D('Article')->getHotArticle(36,$c); 
            S($key, $baodao, 300);
        }
        $this->assign('baodao', $baodao);
        $this->display();
    }
}