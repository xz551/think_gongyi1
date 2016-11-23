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
		$h.='<li class=p_first style="display:none;">';
		$h.='<a  href="javascript:void(0)"><<</a></li>';
		$h.='<li class=p_first style="display:none;">';
		$h.='<a  href="javascript:void(0)"><</a></li>';
		
	}else{
		$h.='<li class=p_first>';
		$h.='<a href="'.page_url(1).'"><<</a></li>';
		$h.='<li class=p_first>';
		$h.='<a href="'.page_url($nowp-1).'"><</a></li>';
		
	}
		$h.='<li  class="'.($nowp==1?'curr':'').'"><a href="'.($nowp==1?"javascript:void(0)":page_url(1)).'">1</a></li>';


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
		$h.='<li   class="'.($nowp==$i?'curr':'').'"><a href="'.($nowp==$i?"javascript:void(0)":page_url($i)).'">'.$i.'</a></li>';

	}
	if($pageCount-$last>=2){
		$h.='<li><a href="javascript:void(0)">...</a></li>';
	}

	if($last!=$pageCount){
		$h.='<li><a href="'.page_url($pageCount).'"  class="'.($nowp==$pageCount?'curr':'').'">'.$pageCount.'</a></li>';
	}
	if($nowp>=$pageCount){
		$h.='<li class="p_last" style="display:none;">';
		$h.='<a  href="javascript:void(0)">></a></li>';
		$h.='<li class="p_last" style="display:none;">';
		$h.='<a  href="javascript:void(0)">>></a></li>';
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


function objToArray($result){
    $arr = array();
    foreach($result as $k=>$v){
        if(is_object($v)){
            foreach($v as $key=>$val){
                $arr[$k][$key] = $val;
            }
        }else{
            $arr[$k] = $v;
        }
    }
    return $arr;
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 */
function time_format($time = NULL,$format='Y-m-d H:i:s'){
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

function ems($com,$nu,$id="109875",$secret="45e5f15bf96b6346b7c9c039807a2cf2",$encode="utf8",$type="json"){
	$gateway=sprintf("http://api.ickd.cn/?id=%s&secret=%s&com=%s&nu=%s&encode=%s&type=%s",$id,$secret,$com,$nu,$encode,$type);
	$ch=curl_init($gateway);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER,false);
	$resp=curl_exec($ch);
	$errmsg=curl_error($ch);
	if($errmsg){
		exit($errmsg);
	}
	curl_close($ch);
	return $resp;
}
/**
 * 将多维数组，按照其中的某一项排序
 * @param array $multi_array    要排序的数组
 * @param string $sort_key      要排序的字段
 * @param string $sort          排序的顺序  SORT_DESC-倒序  SORT_ASC 正序
 * @return array
 */
function multi_array_sort($multi_array,$sort_key,$sort=SORT_DESC){ 
        if(is_array($multi_array)){ 
                foreach ($multi_array as $row_array){ 
                        if(is_array($row_array)){ 
                                $key_array[] = $row_array[$sort_key]; 
                        }else{ 
                                return false; 
                        } 
                } 
        }else{ 
                return false; 
        } 
        array_multisort($key_array,$sort,$multi_array); 
        return $multi_array; 
}
/**
 * 非法字符验证,多用于input验证
 */
function unlawful($str){
    if(preg_match("/^[^~!@#\<\>\$\%\^&\*\(\)\+\'\"]{1,}$/",$str)){
    	return true;
    }else{
    	return false;
    }
}

/**
 * 非法字符验证，多用户textarea验证
 */
function contentUnlawful($str){
    if(preg_match("/^[^@#\<\>\$\%&\*\(\)\+]{1,}$/",$str)){
    	return true;
    }else{
    	return false;
    }    
}

/*
 * 验证电话
 */
function checkMobile($str){
    if(preg_match("/(^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$)|(^((\(\d{3}\))|(\d{3}\-))?(1[358]\d{9})$)/",$str)){
    	return true;
    }else{
    	return false;
    }   
}

/**
 * 验证邮编
 */
function checkCode($str){
    if(preg_match("/^[0-9]\d{5}$/",$str)){
    	return true;
    }else{
    	return false;
    }      
    
}