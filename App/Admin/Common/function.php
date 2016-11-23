<?php
function page_url($page){
    $parm=$_GET;
    $parm['p']='[PAGE]';
    return  str_replace(urlencode('[PAGE]'), $page, U(ACTION_NAME, $parm));
}
/**返回page的Html
 * @param $p 当前页
 * @param $pageCount 总页数
 */
function pageHtml($p,$pageCount){

    $html='<div class="pagination pagination-right">';
    $html.='<ul id="api-link-page">';
    if($p==1){
        $html.='<li class="first disabled"><a href="javascript:void(0)">&lt;&lt;</a></li>';
        $html.='<li class="previous disabled"><a href="javascript:void(0)">&lt;</a></li>';
    }else{
        $html.='<li class="first"><a href="'.page_url(1).'">&lt;&lt;</a></li>';
        $html.='<li class="previous"><a href="'.page_url($p-1).'">&lt;</a></li>';
    }
    $first= $p-5<1?1:$p-5;
    $last=$p+5>$pageCount?$pageCount:$p+5;
    for($i=$first;$i<=$last;$i++){
        if($p==$i){
            $html.='<li class="page active"><a href="javascript:void(0)">'.$i.'</a></li>';
        }else{
            $html.='<li class="page"><a href="'.page_url($i).'">'.$i.'</a></li>';
        }
    }

    if($p>=$pageCount){
        $html.='<li class="next  disabled"><a href="javascript:void(0)">&gt;</a></li>';
        $html.='<li class="last  disabled"><a href="javascript:void(0)">&gt;&gt;</a></li>';
    }else{
        $html.='<li class="next"><a href="'.page_url($p+1).'">&gt;</a></li>';
        $html.='<li class="last"><a href="'.page_url($pageCount).'">&gt;&gt;</a></li>';
    }
    $html.='</ul>';
    $html.='</div>';
    return $html;
}