<?php
function line_db($key){
    $db=C($key);
    if($db){
        C($db);
    }
}
/**
 * 翻页html
 * 当前页 统一参数 p
 * @param $count 总页数
 */
function page($count,$pageSize=12){
	$pageCount=ceil($count/$pageSize);
	$nowp=I('get.p',1);
	$static_url=C('TMPL_PARSE_STRING.__STATIC_URL__');
	$h='<ul>';

	
	if($nowp==1){
		$h.='<li class=p_first>';
		$h.='<a style="display:none;" href="javascript:void(0)"><<</a></li>';
		$h.='<li class=p_first>';
		$h.='<a style="display:none;" href="javascript:void(0)"><</a></li>';
		
	}else{
		$h.='<li class=p_first>';
		$h.='<a href="'.page_url(1).'"><<</a></li>';
		$h.='<li class=p_first>';
		$h.='<a href="'.page_url($nowp-1).'"><</a></li>';
		
	}
		$h.='<li  class="'.($nowp==1?'curr':'').'"><a href="'.page_url(1).'">1</a></li>';


	if($pageCount<=5){
		$first=2;
		$last=$pageCount;
	}else{
		$first=$nowp-2;
		$last=$nowp+2;
	}

	if($first<=1){
		$first=2;
	}

	if($first>2){
		$h.='<li><a href="javascript:void(0)">...</a></li>';
	}
	if($last>$pageCount){
		$last=$pageCount;
	}
	for($i=$first;$i<=$last;$i++){
		$h.='<li   class="'.($nowp==$i?'curr':'').'"><a href="'.page_url($i).'">'.$i.'</a></li>';

	}
	if($pageCount-$last>=2){
		$h.='<li><a href="javascript:void(0)">...</a></li>';
	}

	if($last!=$pageCount){
		$h.='<li><a href="'.page_url($pageCount).'"  class="'.($nowp==$pageCount?'curr':'').'">'.$pageCount.'</a></li>';
	}
	if($nowp>=$pageCount){
		$h.='<li class="p_last">';
		$h.='<a style="display:none;" href="javascript:void(0)">></a></li>';
		$h.='<li class="p_last">';
		$h.='<a style="display:none;" href="javascript:void(0)">>></a></li>';
	}else{
		$h.='<li class="p_last">';
		$h.='<a href="'.page_url($nowp+1).'">></a></li>';
		$h.='<li class="p_last">';
		$h.='<a href="'.page_url($pageCount).'">>></a></li>';

	}
	$h.='</ul>';
	return str_replace('__STATIC_URL__',$static_url,$h);
}
/**
 * 生成链接URL
 * @param  integer $page 页码
 * @return string
 */
function page_url($page){
	$parm=$_GET;
	$parm['p']='[PAGE]';
	return  str_replace(urlencode('[PAGE]'), $page, U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'. ACTION_NAME, $parm));
}