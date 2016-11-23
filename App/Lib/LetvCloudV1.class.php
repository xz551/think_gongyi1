<?php
/**
 * PHP SDK for  OpenAPI V1
 *
 * @version 1.2.0
 * @author open.letvcloud.com
 * @copyright © 2013, Letv Corporation. All rights reserved.
 */
namespace Lib;
/**
 * 如果您的 PHP 没有安装 cURL 扩展，请先安装 
 */
if (!function_exists('curl_init'))
{
	throw new Exception('OpenAPI needs the cURL PHP extension.');
}
/**
 * 如果您的 PHP 不支持JSON，请升级到 PHP 5.2.x 以上版本
 */
if (!function_exists('json_decode'))
{
	throw new Exception('OpenAPI needs the JSON PHP extension.');
}

/** 
 * 状态码定义
 * 0表示全部；10表示可以正常播放；20表示处理失败；30表示正在处理过程中。 
 */
define('ALL',0);
define('PLAY_OK', 10);
define('FAILED',20);
define('WAIT', 30);
    
    
/**
  * 提供访问乐视云视频开放平台的接口
  */
class LetvCloudV1 {
		
		public $user_unique='3c12276fcc';
		public $secret_key='f41da92c2c9a529cf5bdac7509fb3089';
		public  $restUrl= 'http://api.letvcloud.com/open.php';
		public  $format = 'json';	
		protected $apiVersion = '2.0';		
        
        /**
         * 视频上传初始化
         * @param  string $video_name 视频名称
         * @param  string $client_ip  用户ip地址
         * @param  int $file_size  文件大小，单位为字节
         * @return string
         */
        public function videoUploadInit($video_name,$client_ip='',$file_size=0){
        	$api = 'video.upload.init';
        	$params['video_name'] = $video_name;
        	if(!empty($client_ip)){
        		$params['client_ip'] = $client_ip; 
        	}
        	if(!empty($file_size)){
        		$params['file_size'] = $file_size;
        	}
        	return self::httpCall($api, $params);
        }       
        	
        /**
         * 视频上传 (web方式)
         * @param  string $video_file 文件绝对路径
         * @param  string $upload_url 视频上传地址，视频上传时提交地址
         * @return string
         */
        public function videoUpload($video_file,$upload_url){
       		$postFields['video_file'] = "@".$video_file;
       		return $this->curl($upload_url,$postFields);        	
        }
        
        /**
         * 视频断点续传
         * @param  string $token 视频上传标识
         * @return string
         */
        public function videoUploadResume($token){
        	$api = 'video.upload.resume';
        	$params['token'] = $token;
        	return self::httpCall($api, $params);
        }
        
        /**
         * 视频上传（Flash方式）
         * @param  string $video_name 视频名称
         * @param  string $js_callback Javascript回调函数，视频上传完毕后调用
         * @param  int $flash_width Flash宽度，默认值为600
         * @param  int $flash_height Flash高度，默认值为450
         * @param  string $client_ip 用户IP地址
         * @return string
         */
        public function videoUploadFlash($video_name,$js_callback='',$flash_width=600,$flash_height=450,$client_ip=''){
        	$api = 'video.upload.flash';
        	$params['video_name'] = $video_name;
        	if(!empty($js_callback)){
        		$params['js_callback'] = $js_callback;
        	}
        	$params['flash_width'] = $flash_width;
        	$params['flash_height'] = $flash_height;
        	if(!empty($client_ip)){
        		$params['client_ip'] = $client_ip;
        	}
        	return self::httpCall($api, $params);        	
        }
        
        /**
         * 视频信息更新
         * @param  int $video_id 视频ID
         * @param  string $video_name 视频名称
         * @param  string $video_desc 视频简介
         * @param  string $tag 标签
         * @param  int $is_pay 视频是否收费：0表示不收费；1表示收费（收费视频播放时会进行用户鉴权，请不要随便设置）
         * @return string
         */
        public function videoUpdate($video_id,$video_name='',$video_desc='',$tag='',$is_pay=0){
        	$api = 'video.update';
        	$params['video_id'] = $video_id;
        	if(!empty($video_name)){
        		$params['video_name'] = $video_name;
        	}        	
        	if(!empty($video_desc)){
        		$params['video_desc'] = $video_desc;
        	}
        	if(!empty($tag)){
        		$params['tag'] = $tag;
        	}
        	if(!empty($is_pay)){
        		$params['is_pay'] = $is_pay;
        	}
        	return self::httpCall($api, $params);
        }
        
        /**
         * 获取视频列表
         * @param  int $index 开始页索引，默认值为1
         * @param  int $size 分页大小，默认值为10，最大值为100
         * @param  const $status 视频状态：ALL表示全部；PLAY_OK表示可以正常播放；FAILED表示处理失败；WAIT表示正在处理过程中。默认值为ALL
         * @return string
         */
        public function videoList($index=1,$size=10,$status=ALL){
        	$api = 'video.list';
        	$params['index'] = $index;
        	$params['size'] = $size;
        	$params['status'] = $status;
        	return self::httpCall($api, $params);
        }
        
		/**
		 * 获取单个视频信息
		 * @param  int $video_id 视频id
		 * @return string
		 */
        public function videoGet($video_id){
        	$api = 'video.get';
        	$params = array('video_id'=>$video_id);
        	return self::httpCall($api, $params);
        }
        
        /**
         * 删除视频
         * @param  int $video_id 视频ID
         * @return string
         */
        public function videoDel($video_id){
        	$api = 'video.del';
        	$params['video_id'] = $video_id;
        	return self::httpCall($api, $params);
        }
        
        /**
         * 批量删除视频
         * @param  string $video_id_list 视频ID列表，使用符号-作为间隔符，每次最多操作50条记录
         * @return string
         */
        public function videoDelBatch($video_id_list){
        	$api = 'video.del.batch';
        	$params['video_id_list'] = $video_id_list;
        	return self::httpCall($api, $params);
        }
        
        /**
         * 视频暂停
         * @param  int $video_id 视频ID
         * @return string
         */
        public function videoPause($video_id){
        	$api = 'video.pause';
        	$params['video_id'] = $video_id;
        	return self::httpCall($api, $params);
        }
        
        /**
         * 视频恢复
         * @param  int $video_id 视频ID
         * @return string
         */
        public function videoRestore($video_id){
        	$api = 'video.restore';
        	$params['video_id'] = $video_id;
        	return self::httpCall($api, $params);
        }
        
        /**
         * 获取视频截图
         * @param  int $video_id 视频ID
         * @param  string $size 截图尺寸，每种尺寸各有8张图。
         * @return string
         */
        public function imageGet($video_id,$size){
        	$api = 'image.get';
        	$params['video_id'] = $video_id;
        	$params['size'] = $size;
        	return self::httpCall($api, $params);
        }
        
        /**
         * 视频小时数据
         * @param  string $date 日期，格式为：yyyy-mm-dd
         * @param  int $hour 小时，0-23之间
         * @param  int $video_id 视频ID
         * @param  int $index 开始页索引，默认值为1
         * @param  int $size 分页大小，默认值为10，最大值为100
         * @return string
         */
        public function dataVideoHour($date,$hour=null,$video_id=null,$index=1,$size=10){
        	$api = 'data.video.hour';
        	$params['date'] = $date;
        	if($hour != null){
        		$params['hour'] = $hour;
        	}
        	if($video_id != null){
        		$params['video_id'] = $video_id;
        	}
        	$params['index'] = $index;
        	$params['size'] = $size;
        	return self::httpCall($api, $params);
        }
        
        /**
         * 视频天数据
         * @param  string $start_date 开始日期，格式为：yyyy-mm-dd 
         * @param  string $end_date 结束日期，格式为：yyyy-mm-dd
         * @param  int $video_id 视频ID，不输入该参数将返回所有视频的数据
         * @param  int $index 开始页索引，默认值为1
         * @param  int $size 分页大小，默认值为10，最大值为100
         * @return string
         */
        public function dataVideoDate($start_date,$end_date,$video_id=null,$index=1,$size=10){
        	$api = 'data.video.date';
        	$params['start_date'] = $start_date;
        	$params['end_date'] = $end_date;
        	if($video_id != null){
        		$params['video_id'] = $video_id;
        	}
        	$params['index'] = $index;
        	$params['size'] = $size;
        	return self::httpCall($api, $params);
        }
        
        /**
         * 所有数据
         * @param  string $start_date 开始日期，格式为：yyyy-mm-dd
         * @param  string $end_date 结束日期，格式为：yyyy-mm-dd
         * @param  int $index 开始页索引，默认值为1
         * @param  int $size 分页大小，默认值为10，最大值为100
         * @return string
         */
        public function dataTotalDate($start_date,$end_date,$index=1,$size=10){
        	$api = 'data.total.date';
        	$params['start_date'] = $start_date;
        	$params['end_date'] = $end_date;
        	$params['index'] = $index;
        	$params['size'] = $size;
        	return self::httpCall($api, $params);
        }
        
        /**
         * 获取视频播放接口
         * @param string $uu 用户唯一标识码，由乐视网统一分配并提供
         * @param string $vu 视频唯一标识码
         * @param string $type 接口类型：url表示播放URL地址；js表示JavaScript代码；flash表示视频地址；html表示HTML代码
         * @param string $pu 播放器唯一标识码
         * @param int $auto_play 是否自动播放：1表示自动播放；0表示不自动播放。默认值由双方事先约定
         * @param int $width 播放器宽度
         * @param int $height 播放器高度 
         * @return string
         */
        public function videoGetPlayinterface($uu,$vu,$type,$pu="",$auto_play=null,$width=0,$height=0){
        	$args = array();
        	$args['uu'] = $uu;
        	$args['vu'] = $vu;
        	if(!empty($pu)){
        		$args['pu'] = $pu;
        	}
        	if($auto_play != null){
        		$args['auto_play'] = $auto_play;
        	}
        	if($width>0){
        		$args['width'] = $width;
        	}
        	if($height>0){
        		$args['height'] = $height;
        	}
        	$queryString = http_build_query($args);
        	$jsonString = json_encode($args);
        	if($type == "url"){
        		$res = "http://yuntv.letv.com/bcloud.html?".$queryString; 
        	}elseif ($type == "js"){
        		$res = '<script type="text/javascript">var letvcloud_player_conf = '.$jsonString.';</script><script type="text/javascript" src="http://yuntv.letv.com/bcloud.js"></script>';
        	}elseif ($type == "flash"){
        		$res = "http://yuntv.letv.com/bcloud.swf?".$queryString;
        	}elseif ($type == "html"){
        		$res = '<embed src="http://yuntv.letv.com/bcloud.swf" allowFullScreen="true" quality="high" width="800" height="450" align="middle" allowScriptAccess="always" flashvars="'.$queryString.'" type="application/x-shockwave-flash"></embed>';
        	}
        	return $res;
        }
        
		/** 
		 * 构造云视频Sign
		 * @param array $params 业务参数
		 * @return string
		 */
		public function generateSign($params){
			ksort($params);
			$keyStr = '';
			foreach($params as $key => $value){
				$keyStr .= $key.$value;
			}
			$keyStr .= $this->secret_key;
			$key = md5($keyStr);
			return $key;
		}
		
	    /** 
	     * 发送http请求
	     * @param $url 请求地址
	     * @param $postFields HTTP方法为POST时的请求参数
	     * @return string HTTP请求相应结果
	     */
		public function curl($url, $postFields = null) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FAILONERROR, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//https 请求
			if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			}
	
			if (is_array($postFields) && 0 < count($postFields))
			{
				$postBodyString = "";
				$postMultipart = false;
				foreach ($postFields as $k => $v)
				{
					if("@" != substr($v, 0, 1))//判断是不是文件上传
					{	
						$postBodyString .= "$k=" . urlencode($v) . "&"; 
					}
					else//文件上传用multipart/form-data，否则用www-form-urlencoded
					{
						$postMultipart = true;
					}
				}
				unset($k, $v);
				curl_setopt($ch, CURLOPT_POST, true);
				if ($postMultipart)
				{
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
				}
				else
				{
					curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString,0,-1));
				}
			}
			$reponse = curl_exec($ch);
			
			if (curl_errno($ch))
			{
				throw new Exception(curl_error($ch),0);
			}
			else
			{
				$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if (200 !== $httpStatusCode)
				{
					throw new Exception($reponse,$httpStatusCode);
				}
			}
			curl_close($ch);
			return $reponse;
		}
		
	    /**
	     * 获取HTTP请求结果
	     * @param $api API名称 , 如：video.get
	     * @param $apiParams API业务参数
	     * @return string
	     */
	    public function httpCall($api,$apiParams){
	    	//组装系统参数
	    	$sysParams['user_unique'] = $this->user_unique;
			//$sysParams['timestamp'] = time();	
			$sysParams['timestamp'] = "1369300735578";	
			$sysParams['ver'] = $this->apiVersion;		
			$sysParams['format'] = $this->format;
			$sysParams["api"] = $api;
	    	//参数集合=系统参数+业务参数
	    	$params = array_merge($sysParams, $apiParams);
	    	//构造请求URL
	    	$resurl = '';
	    	$resurl .= $this->restUrl;	    	
	    	//签名	
			$params['sign'] = $this->generateSign($params);		
			//参数放入GET请求串
			foreach($params as $key=>$v)
			{
				if(!strpos($resurl, '?'))
				{
					$resurl .= "?{$key}=" . urlencode($v);
				}
				else
				{
					$resurl .="&{$key}=" . urlencode($v);
				}
			}
	    	//发起HTTP请求
			$respObj = $this->curl($resurl);
			return $respObj;
	    }
	
}