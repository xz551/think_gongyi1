<?php
/**
 * api調用函數
 */
function api($url,$post = false){
    if(stripos($url,'http')===false){
        $url = API_URL.$url;
    }
    $ch = curl_init( );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
    if (is_array($post) && !empty($post))
    {
        $post['token']=C('API_TOKEN');
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }else{
        //stripos("Hello world!","WO");
        if(!stripos($url,'?')){
            $url.="?";
        }
        $url.='&token='.C('API_TOKEN');
    }
    curl_setopt( $ch, CURLOPT_URL, $url );
    $contents = curl_exec( $ch );
    curl_close($ch);
    return $contents;
}
/**
 * 获得API接口返回的内容
 * @version 2013-11-19
 * @author wwpeng
 * @param string $url
 * @return array
 */
function getApiContent($url, $post = false, $result = false) {    
    $page = I('page');
    if (!empty($page)) { 
        $url = str_replace(C('URL_HTML_SUFFIX'), '/page/' . $page, $url); 
    }
    $sessionKey = C('sessionKey'); // Y::paramsConfig('sessionKey');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, $sessionKey . '=' . (isset($_COOKIE[$sessionKey]) ? $_COOKIE[$sessionKey] : '')); 
    if (is_array($post) && !empty($post)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $contents = curl_exec($ch); 
    curl_close($ch);
    
    if (Lib\Helper::isJson($contents)) {
        
        $contents=\Lib\CJSON::decode($contents);
        
        if ($result) {
            $status= $contents['status'];
            
            if ($status) {
                return $contents['data'];
            }
        }
        return $contents;
    }
    return "";
}

/**
 * 返回用户中心的一个url
 * @param array $param
 * @param array $param2
 * @return string
 */
function ucUrl($uid, $param2 = array()) {
    if(is_numeric($uid))    //return C('ucenter_url') ._param($param, $param2);
        return '/uc/'.$uid;
    else{
        return C('ucenter_url') ._param($uid, $param2);
    }
}
function _param($url,$param){
    $_p="";
    if(is_array($param)){
        foreach ($param as $key => $value) {
            if(is_array($value)){
                foreach ($value as $v) {
                    //%5B1%5D
                    //[]
                    $_p.='/'.$key.'%5B1%5D/'.trim($v); 
                }
            }else{
                $_p.='/'.$key.'/'.trim($value); 
            }
        }
    }
    return $url.$_p;
}
function now_time(){
    return date("Y-m-d H:i:s");
}

function img_lazyload(&$content){    
     $reg="/<img[^src]+src=['\"](.[^\"'>]+)[^>]+>/i";
     $regArr = array();
     preg_match_all($reg, $content, $regArr);
     foreach ($regArr[0] as $key => $img) {
         $new_img=  str_replace("src", "data-original", $img);         
          
         $new_img=  str_replace("/>", " class='lazy' /> ", $new_img);
         $content=  str_replace($img, $new_img, $content);
     }    
}
function h_dump($a){
    echo "<div style='display:none'>";
    dump($a);
    echo "</div>";
}

function unicode_decode($name)
{
    // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
    $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
    preg_match_all($pattern, $name, $matches);
    if (!empty($matches))
    {
        $name = '';
        for ($j = 0; $j < count($matches[0]); $j++)
        {
            $str = $matches[0][$j];
            if (strpos($str, '\\u') === 0)
            {
                $code = base_convert(substr($str, 2, 2), 16, 10);
                $code2 = base_convert(substr($str, 4), 16, 10);
                $c = chr($code).chr($code2);
                $c = iconv('UCS-2', 'UTF-8', $c);
                $name .= $c;
            }
            else
            {
                $name .= $str;
            }
        }
    }
    return $name;
}
function pagestring($p,$pcount){
    $min=$pcount-3;
    $min=$min<1?1:$min;
    $max=$pcount+3;
    $max=$max>$pcount?$pcount:$max;
    $pramp=$_GET;
    $pramp['p']='[page]';
    $url=U(ACTION_NAME, $pramp);
    //return str_replace(urlencode('[PAGE]'), $page, $this->url);

    $h='<div class="pagination" >';
    $h.='<ul>';
    if($p==1){
        $h.='<li class="disabled"><a href="javascript:void(0)">&laquo;</a></li>';
    }else{
        $h.='<li ><a href="'.str_replace(urlencode('[page]'), ($p-1), $url).'">&laquo;</a></li>';
    }
    for($i=$min;$i<=$max;$i++){
        if($i==$p){
            $h.='<li class="active"><a href="javascript:void(0)">'.$i.'</a></li>';
        }else{
            $h.='<li><a href="'.str_replace(urlencode('[page]'), ($i), $url).'">'.$i.'</a></li>';
        }
    }
    if($p==$max){
        $h.='<li class="disabled"><a href="javascript:void(0)">&raquo;</a></li>';
    }else{
        $h.='<li><a href="'.str_replace(urlencode('[page]'), ($p+1), $url).'">&raquo;</a></li>';
    }

    $h.='</ul>';
    $h.='</div>';

    return $h;

}

/**
 * 将字符串变成 url安全的base64编码
 */
function urlSafeBase64_encode($string)
{
    $data = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_',''),$data);
    return $data;
}
/**
 * url安全的base64编码 解码为正常字符串
 */
function urlSafeBase64_decode($string)
{
    $data = str_replace(array('-','_'),array('+','/'),$string);
    $mod4 = strlen($data) % 4;
    if ($mod4)
    {
        $data .= substr('====', $mod4);
    }
    return base64_decode($data);
}
/**截取字符串并加上省略号
 * @param $str
* @param $leng
*/
function str_ellipsis($str,$leng){
	if(mb_strlen($str,'utf8')>$leng){
		return '<span title="'.$str.'">'.mb_substr($str,0,$leng,'utf8').'...</span>';// ;
	}
	return $str;
}

/**截取字符串并加上省略号，添加em标签
 * @param $str
* @param $leng
*/
function str_ellipsis_new($str,$leng){
	if(mb_strlen($str,'utf8')>$leng){
		return '<em title="'.$str.'">'.mb_substr($str,0,$leng,'utf8').'...</em>';// ;
	}
	return $str;
}

/**
 * 翻页html
 * 当前页 统一参数 p
 * @param $count 总页数
 */
function page_new($count,$pageSize=12){
	$pageCount=ceil($count/$pageSize);
	$nowp=I('get.p',1);
	$static_url=C('TMPL_PARSE_STRING.__STATIC_URL__');
	$h='';	
	if($nowp==1){		
		$h.='<a style="display:none;" href="javascript:void(0)"><<</a>';		
		$h.='<a style="display:none;" href="javascript:void(0)"><</a>';		
	}else{		
		$h.='<a href="'.page_url_new(1).'"><<</a>';
		$h.='<a href="'.page_url_new($nowp-1).'"><</a>';		
	}
		$h.='<a href="'.page_url_new(1).'">1</a>';

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
		$h.='<a href="javascript:void(0)">...</a>';
	}
	if($last>$pageCount){
		$last=$pageCount;
	}
	for($i=$first;$i<=$last;$i++){
		$h.='<a href="'.page_url_new($i).'">'.$i.'</a>';

	}
	if($pageCount-$last>=2){
		$h.='<a href="javascript:void(0)">...</a>';
	}

	if($last!=$pageCount){
		$h.='<a href="'.page_url_new($pageCount).'">'.$pageCount.'</a>';
	}
	if($nowp>=$pageCount){
		
		$h.='<a style="display:none;" href="javascript:void(0)">></a>';
		
		$h.='<a style="display:none;" href="javascript:void(0)">>></a>';
	}else{
		
		$h.='<a href="'.page_url_new($nowp+1).'">></a>';
	
		$h.='<a href="'.page_url_new($pageCount).'">>></a>';

	}
	
	return str_replace('__STATIC_URL__',$static_url,$h);
}
/**
 * 生成链接URL
 * @param  integer $page 页码
 * @return string
 */
function page_url_new($page){
	$parm=$_GET;
	$parm['p']='[PAGE]';
	return  str_replace(urlencode('[PAGE]'), $page, U(MODULE_NAME.'/'.CONTROLLER_NAME.'/'. ACTION_NAME, $parm));
}


/**
 * 翻页ajax html
 * 当前页 统一参数 p
 * @param $count 总页数
 */
function ajax_page($count,$pageSize=12){
	$pageCount=ceil($count/$pageSize);
	$nowp=I('get.p',1);
	$static_url=C('TMPL_PARSE_STRING.__STATIC_URL__');
	$h='';	
	if($nowp==1){		
		$h.='<a style="display:none;" href="javascript:void(0)"><<</a>';		
		$h.='<a style="display:none;" href="javascript:void(0)"><</a>';		
	}else{		
		$h.='<a href="javascript:void(0)" data-url="'.page_url_new(1).'"><<</a>';
		$h.='<a href="javascript:void(0)" data-url="'.page_url_new($nowp-1).'"><</a>';
	}
	if($pageCount<=5){
		$last=$pageCount;
	}else{
		$first=$nowp-2;
		$last=$nowp+2;
	}

	if($first<=0){
		$first=1;
	}

	if($first>2){
		$h.='<a href="javascript:void(0)">...</a>';
	}
	if($last>$pageCount){
		$last=$pageCount;
	} 
	for($i=$first;$i<=$last;$i++){
		$h.='<a href="javascript:void(0)"';
        if($nowp==$i) {
            $h.=' class="selected" ';
        }else {
            $h .= ' data-url="' . page_url_new($i) . ' ';
        }
        $h.='  ">'.$i.'</a>';

	}
	if($pageCount-$last>=2){
		$h.='<a href="javascript:void(0)">...</a>';
	}

	if($last!=$pageCount){
		$h.='<a href="javascript:void(0)" data-url="'.page_url_new($pageCount).'">'.$pageCount.'</a>';
	}
	if($nowp>=$pageCount){
		
		$h.='<a style="display:none;" href="javascript:void(0)">></a>';
		
		$h.='<a style="display:none;" href="javascript:void(0)">>></a>';
	}else{
		
		$h.='<a href="javascript:void(0)" data-url="'.page_url_new($nowp+1).'">></a>';
	
		$h.='<a href="javascript:void(0)" data-url="'.page_url_new($pageCount).'">>></a>';

	}
	
	return str_replace('__STATIC_URL__',$static_url,$h);

}


/**
 * 手机ajax分页
 * 当前页 统一参数 p
 * @param $count 总页数
 */
function photo_page($count,$pageSize=12){
	$pageCount=ceil($count/$pageSize);
	$nowp=I('get.p',1);
	$static_url=C('TMPL_PARSE_STRING.__STATIC_URL__');
	$h='';	
	if($nowp==1){		
		$h.='<a style="display:none;" href="javascript:void(0)"><<</a>';		
		$h.='<a style="display:none;" href="javascript:void(0)"><</a>';		
	}else{		
		$h.='<a href="javascript:void(0)" data-url="'.page_url_new(1).'"><<</a>';
	}
	if($pageCount<=5){
		$last=$pageCount;
	}else{
		$first=$nowp-1;
		$last=$nowp+1;
	}

	if($first<=0){
		$first=1;
	}

	if($first>2){
		$h.='<a href="javascript:void(0)">...</a>';
	}
	if($last>$pageCount){
		$last=$pageCount;
	} 
	for($i=$first;$i<=$last;$i++){
		$h.='<a href="javascript:void(0)"';
        if($nowp==$i) {
            $h.=' class="selected" ';
        }else {
            $h .= ' data-url="' . page_url_new($i) . ' ';
        }
        $h.='  ">'.$i.'</a>';

	}
	if($pageCount-$last>=2){
		$h.='<a href="javascript:void(0)">...</a>';
	}

	if($last!=$pageCount){
		$h.='<a href="javascript:void(0)" data-url="'.page_url_new($pageCount).'">'.$pageCount.'</a>';
	}
	if($nowp>=$pageCount){
		
		$h.='<a style="display:none;" href="javascript:void(0)">></a>';
		
		$h.='<a style="display:none;" href="javascript:void(0)">>></a>';
	}else{
		$h.='<a href="javascript:void(0)" data-url="'.page_url_new($pageCount).'">>></a>';

	}
	
	return str_replace('__STATIC_URL__',$static_url,$h);

}




function time_day($time){
    if(!is_numeric($time)){
        $time=  strtotime($time);
    }
    $now=  time();
    $c=$now-$time;
   
    if($c<60){
        return $c.'秒前';
    }
    if($c<60*60){
        return intval($c/60).'分钟前';
    }
    if($c<60*60*24){
        return intval($c/(60*60)).'小时前';
    }
    if($c<60*60*24*30){
        return intval($c/(60*60*24)).'天前';
    }
    if($c<60*60*24*30*12){
        return intval($c/(60*60*24*30)).'月前';
    }
    if($c>60*60*24*30*12){
        return intval($c/(60*60*24*30*12)).'年前';
    }    
}
function userUrl($uid,$url=''){
	if($uid){
		return "/uc/".$uid;
	}else{
		return "/Uc/login/index?returnurl=$url";
	}
}
function nowTime($time){
	return date("Y年m月d日 H:i",$time);
}
function date_time($time){
	return date("Y年m月d日 H:i:s",$time);
}
/**
 * 汉字转化为unicode
 * @param $text 文本
 * @return mixed
 */
function unicode_encode($text){
    return $script = preg_replace_callback("/[\x{4e00}-\x{9fa5}]/iu",function($match){
        return '\u' . (String) bin2hex(iconv('UTF-8', 'UCS-2', $match[0]));
    }, $text);
}
//url生成
function create_href($name,$value){
	$get=$_GET;
	$get[$name]=$value;
	$get['p']=1;
	return U(__ACTION__,$get);
}
//发送短信接口
function sms($mobile, $message) {		
    $url = 'http://192.168.0.51:8085/HTTP9007/smsend.jsp?app_type=huayun&phone='.$mobile.'&if_encode=1&content=' . iconv('UTF-8', 'GB2312//IGNORE', $message);
    return api($url);
}


/**名称的合法验证 数字字母下划线
 * @param $uname
 * @return int
 */
function verify_name($uname){
    //$uname=\Org\Util\String::autoCharset($uname);
    return preg_match('/^[0-9a-zA-Z_\x{4e00}-\x{9fa5}]{2,10}$/u', $uname);

}
/**
 * Email 检测
 */
function check_email($email){
	$pattern = '/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/';
	return preg_match($pattern,$email);
}
/**验证手机号
 * @param $phone
 * @return int
 */
function verify_phone($phone)
{
    return preg_match('/^1\d{10}$/', $phone);
}
/**
 * 
 */
function check_card($card){
	$pattern = '/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[A-Z])$/';
	return preg_match($pattern,$card);
}
//统计浏览量
function pv($view){
	$length=strlen($view);
	if($length<4){
		return $view;
	}
	if($length<7){
		$pv1=substr($view,-3);
		$pv2=substr($view,0,$length-3);
		return $pv2.",".$pv1;
	}
	if($length>=7){
		$pv1=substr($view,-3);
		$pv2=substr($view,-6,-3);
		$pv3=substr($view,0,$length-6);
		return $pv3.",".$pv2.",".$pv1;
	}
}