<?php
namespace Lib\Image;
/**
 * 图像处理类:缩放/裁剪/水印
 * @Author: Rogee<rogeecn@gmail.com>
 * Date: 12-12-17 下午1:36
 * $Id: Image.php 5 2012-12-21 03:19:49Z rogee $
 */
class Image
{
    private $_debug;
    private $_quality = 100;
    //加载的源图像资源
    private $_src;
    private $_src_info;
    private $_dst; //目标图像资源
    private $_mask_img;
    private $_mask_info;
    private $_mask_pos;
    private $_mask_words;
    private $_mask_words_size;
    private $_font_name;
    private $_mask_words_angle;
    private $_mask_words_pos;
    private $_mask_words_color;

    private $_ext_arr =  array(
        'gif' => 'gif',
        'jpg' => 'jpeg',
        'jpeg' => 'jpeg',
        'png' => 'png',
        'xbm' => 'xbm',
    );
    //切割图像用
    const CUT_BY_WIDTH  = 0;
    const CUT_BY_HEIGHT = 1;
    const CUT_BY_AUTO   = 2;
    const CUT_BY_FIXED  = 3;
    const CUT_BY_REGION = 4;

    //水印PADDING
    private $_mask_padding = 10;
    //水印透明度
    private $_mask_alpha=100;
    //水印图片的大小
    private $_mask_size=array();
    //水印位置常量
    const MASK_POS_1    = 1;
    const MASK_POS_2    = 2;
    const MASK_POS_3    = 3;
    const MASK_POS_4    = 4;
    const MASK_POS_5    = 5;
    const MASK_POS_6    = 6;
    const MASK_POS_7    = 7;
    const MASK_POS_8    = 8;
    const MASK_POS_9    = 9;

    //debug类型
    const DEBUG_WARN = ' WARN ';
    const DEBUG_NOTICE = 'NOTICE';
    const DEBUG_INFO = ' INFO ';
    private static $debug_info = array();
    private static $debug_start;
    private $_debug_make;
    private $_debug_visit;

    public function __construct(){}

    /**
     * 设置DEBUG的 生成路径和访问路径
     * @param $make
     * @param $visit
     */
    public function setDebugPath($make, $visit)
    {
        $this->_debug = true;
        self::$debug_start = microtime(true);
        $this->_addDebugInfo('Author: Rogee&lt;rogeecn@gmail.com&gt;');
        $this->_addDebugInfo( sprintf("[%s] 开启图片调试模式", __FUNCTION__) );
        $this->_debug_make = $make;
        $this->_debug_visit = $visit;
        return $this;
    }
    /**
     * 打印debug信息
     */
    public function debug_info()
    {
        if( $this->_debug)
        {
            header('Content-type: text/html;charset=utf-8');
            $this->_addDebugInfo( sprintf('运行耗时 [ %s ] 秒', (microtime(true)-self::$debug_start) ), self::DEBUG_NOTICE );
            $info = self::$debug_info;
            echo '<ul style="font-size:12px;border: 2px solid black; border-top:10px solid black; padding:10px; list-style: none;">';
            foreach($info as $value)
                echo $value;
            echo '</ul>';
            echo sprintf('<img src="%s/debug.jpg" />',  $this->_debug_visit);
        }else{
            echo '调试未打开';
        }
    }

    /**
     * 设置操作源文件
     * @param $file
     */
    public function setSrcFile($file)
    {
        $this->_addDebugInfo( sprintf("[%s] 设置源图片为 %s", __FUNCTION__, $file) );

        $src = $this->_load_from_file($file);
        $this->_src = $src['file_src'];
        $this->_src_info = $src['file_info'];

        return $this;
    }

    /**
     * 设置水印文字的字体
     * @param $string
     * @return Image
     */
    public function setMaskWordSize( $size)
    {
        $this->_mask_words_size = floatval($size);
        $this->_addDebugInfo( sprintf('[%s]调用字体文件设置大小为 %s', __FUNCTION__, $size) );
        return $this;
    }

    /**
     * 设置字体文件
     * @param $font_name
     * @return Image
     */
    public function setMaskWordFont($font_name)
    {
        $this->_font_name = trim($font_name);
        $this->_addDebugInfo( sprintf('[%s]调用字体文件设置为 %s', __FUNCTION__, $font_name) );

        return $this;
    }
    /**
     * 设置水印字体的坐标
     * @param array $pos 坐标 array(x, y);
     * @param string $angle 角度 float
     * @return Image
     */
    public function setMaskWordPos($pos = array(0,0), $angle='0')
    {
        $this->_mask_words_pos = $pos;
        $this->_mask_words_angle = $angle;
        $this->_addDebugInfo( sprintf('[%s]设置水印字体方向为 坐标[%d,%d]，角度%d', __FUNCTION__, $pos[0], $pos[1], $angle) );
        return $this;
    }

    /**
     * 设置字体的颜色
     * @param $color
     * @return Image
     */
    public function setMaskWordColor($color = array(0,0,0) )
    {
        if( !is_array($color) )
        {
            $this->_addDebugInfo( sprintf('[%s] 正在转换颜色', __FUNCTION__));
            $color = $this->_parsHexColor($color);
        }
        list($red, $green, $blue) = $color;
        $this->_mask_words_color = imagecolorallocate($this->_src, $red, $green, $blue);
        $this->_addDebugInfo( sprintf('[%s]设置字体颜色为RGB(%d,%d,%d)', __FUNCTION__, $red, $green, $blue) );
        return $this;
    }
    /**
     * 把设置好的坐标和字体解决等画到图片资源上
     * @param string $string 要写到图片上的文字
     * @return Image
     */
    public function drawMaskWord($string)
    {
        imagettftext(
            $this->_src,
            $this->_mask_words_size,
            $this->_mask_words_angle,
            $this->_mask_words_pos[0],
            $this->_mask_words_pos[1],
            $this->_mask_words_color,
            $this->_font_name,
            $string
        );
        return $this;
    }
    /**
     * 设置水印图片
     * @param $file
     * @return Image
     */
    public function setMaskImage($file)
    {
        $this->_addDebugInfo( sprintf("[%s] 设置水印图片为 %s", __FUNCTION__, $file) );


        $src = $this->_load_from_file($file);
        $this->_mask_img = $src['file_src'];
        $this->_mask_info = $src['file_info'];

        return $this;
    }

    /**
     * 设置水印的坐标
     * @param array $position array(x, y);如果设置为常量则使用九宫格位置方式1-9
     */
    public function setMaskImagePosition( $position = array(0,0) )
    {
        if( !is_array($position))
            $position = $this->_getMaskImagePosition($position);

        list($src_width, $src_height) = $this->_src_info;
        list($mask_width, $mask_height) = $this->_mask_info;
        list($pos_x, $pos_y) = $position;

        $this->_addDebugInfo( sprintf('[%s] 设置水印图片坐标 {%d, %d}', __FUNCTION__, $pos_x, $pos_y) );

        if( ($pos_x+$mask_width)>$src_width || ($pos_y+$mask_height)>$src_height)
        {
            $this->_addDebugInfo( sprintf('[%s] 水印图的宽或高超出原图', __FUNCTION__), self::DEBUG_NOTICE );
        }

        $this->_mask_pos = $position;

        return $this;
    }

    public function setMaskImgSize($size = array())
    {
        if( empty($size) )
        {
            $this->_mask_size = array($this->_mask_info[0], $this->_mask_info[1] );
            $this->_addDebugInfo( sprintf('[%s] 设置水印图片大小为原图大小 {%d, %d}', __FUNCTION__, $this->_mask_size[0], $this->_mask_size[1]) );
        }else{
            $this->_mask_size = $size;
            $this->_addDebugInfo( sprintf('[%s] 设置水印图片大小为 {%d, %d}', __FUNCTION__, $this->_mask_size[0], $this->_mask_size[1]) );
        }

        return $this;
    }
    /**
     * 设置水印的PADDING
     * @param int $padding
     */
    public function setMaskPadding($padding=10)
    {
        $this->_addDebugInfo( sprintf('[%s] 设置水印图片间隔边距 %d', __FUNCTION__, $padding) );

        $this->_mask_padding = intval($padding);
        return $this;
    }
    /**
     * 获取水印的位置
     * @param int $position
     */
    private function _getMaskImagePosition( $position = self::MASK_POS_9)
    {
        $this->_addDebugInfo( sprintf('[%s] 水印图片使用九宫格的第 %d 号位置', __FUNCTION__, $position) );

        list($src_width, $src_height) = $this->_src_info;
        list($mask_width, $mask_height) = $this->_mask_info;
        $half_src_width     = intval($src_width/2);
        $half_src_height    = intval($src_height/2);
        $half_mask_width    = intval($mask_width/2);
        $half_mask_height   = intval($mask_height/2);

        $center = $half_src_width-$half_mask_width-$this->_mask_padding/2;
        $middle = $half_src_height-$half_mask_height-$this->_mask_padding/2;
        $right  = $src_width - $mask_width - $this->_mask_padding;
        $bottom = $src_height - $this->_mask_padding - $mask_height;

        switch($position)
        {
            case self::MASK_POS_1:
                $ret = array($this->_mask_padding, $this->_mask_padding);;
                break;
            case self::MASK_POS_2:
                $ret = array($center, $this->_mask_padding);
                break;
            case self::MASK_POS_3:
                $ret = array($right, $this->_mask_padding);
                break;
            case self::MASK_POS_4:
                $ret = array($this->_mask_padding, $middle);
                break;
            case self::MASK_POS_5:
                $ret = array($center, $middle);
                break;
            case self::MASK_POS_6:
                $ret = array($right, $middle);
                break;
            case self::MASK_POS_7:
                $ret = array($this->_mask_padding, $bottom);
                break;
            case self::MASK_POS_8:
                $ret = array($center, $bottom);
                break;
            default:
                $ret = array($right, $bottom);
                break;
        }
        return $ret;
    }
    public function setMaskAlpha($alpha = 100)
    {
        if( $alpha > 100 || $alpha < 0){
            $this->_addDebugInfo(sprintf('[%s] 水印图片透明度[%d]设置非法, 重置为默认 100 ', __FUNCTION__, $alpha), self::DEBUG_NOTICE);
            $alpha = 100;
        }
        $this->_addDebugInfo(sprintf('[%s] 设置水印图片的透明度为 %d',__FUNCTION__,  $alpha));
        $this->_mask_alpha = $alpha;
        return $this;
    }

    /**
     * 把水印图片画到原图上并调换资源
     */
    public function drawMaskImage()
    {
        $this->_addDebugInfo( sprintf('[%s] 向源图片写入水印', __FUNCTION__) );

        list($x, $y) = $this->_mask_pos;

        if(empty($this->_mask_size))
            $this->setMaskImgSize();

        if( $this->_mask_alpha == 100)
            imagecopyresampled($this->_src, $this->_mask_img,$x, $y, 0, 0, $this->_mask_size[0], $this->_mask_size[1], $this->_mask_info[0], $this->_mask_info[1]);
        else
            imagecopymerge($this->_src,$this->_mask_img, $x, $y, 0, 0, $this->_mask_size[0], $this->_mask_size[1], $this->_mask_alpha);
        return $this;
    }

    /**
     * 用于对16进制颜色的解析
     */
    private function _parsHexColor($color)
    {
        $arr = array();
        $len = strlen($color);
        for($ii=1; $ii<$len; $ii++)
        {
            $arr[] = hexdec(substr($color,$ii,2));
            $ii++;
        }

        return $arr;
    }

    /**
     * 用于调试生成图像
     */
    private function _debugCreate()
    {
        $this->_addDebugInfo( sprintf('[%s] Debug模式下自动转入图片测试生成模式', __FUNCTION__), self::DEBUG_NOTICE );
        $file = $this->_debug_make.'/debug.jpg';
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $ext = $this->_ext_arr[$ext];

        $function = 'image'.$ext;
        $function($this->_src, $file, $this->_quality);
        @imagedestroy($this->_src);
        @imagedestroy($this->_dst);
        $this->_addDebugInfo('调试结束,退出运行!', self::DEBUG_WARN);
    }
    /**
     * 生成功输出图像
     * @param null $file
     */
    public function create($file = null)
    {
        if($this->_debug){
            $this->_debugCreate();
        }
        if( !is_null($file))
        {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            $ext = $this->_ext_arr[$ext];

        }
        else
        {
            $file_info = $this->_src_info ;
            $ext = $file_info[2];
            $mime = $file_info['mime'];
            header("Content-type: {$mime}");
        }

        $function = 'image'.$ext;
        if($ext == 'png')
            $function($this->_src, $file);
        else
            $function($this->_src, $file, $this->_quality);

        @imagedestroy($this->_dst);
        @imagedestroy($this->_src);
    }


    /**
     * 加载文件资源
     * @param $file
     * @return mixed
     */
    private function _load_from_file($file)
    {
        if ( empty($file) )
        {
            $this->_addDebugInfo( sprintf('[%s] 加载文件为空', __FUNCTION__), self::DEBUG_WARN );
        }
        else
        {
            $file_info = $this->get_file_info($file);
            if( $file_info !== false )
            {
                list($width, $height, $ext) = $file_info;
                $function = 'imagecreatefrom'.$ext;
                $ret_arr['file_info'] = $file_info;
                $ret_arr['file_src'] = $function($file);
                return $ret_arr;
            }
            $this->_addDebugInfo( sprintf('[%s] 加载文件信息错误', __FUNCTION__), self::DEBUG_WARN );

        }
    }
    /**
     * 根据不同的类型来切割图像
     * @param $size_arr 目标图像大小信息 array($width, $height);
     * @param int $type
     * @return mixed
     */
    public function resize($size_arr, $type = 100)
    {
    	if (count($size_arr) == 2 && isset($size_arr[1]) && empty($size_arr[1]))
    	{
    		$size_arr[1] = 0;
    		$type = self::CUT_BY_WIDTH;
    		
    	}elseif(count($size_arr)==2 && $size_arr[0]==0){
            $type = self::CUT_BY_HEIGHT;
        }
        elseif(count($size_arr) == 4){
			
			$type = self::CUT_BY_REGION;
			
		}elseif (count($size_arr) == 1 && isset($size_arr[0])){
    		
			$size_arr[1] = 0;
			$type = self::CUT_BY_WIDTH;
		}
		
        switch($type)
        {
            case self::CUT_BY_WIDTH:
                $function = '_cut_by_width';
                break;
            case self::CUT_BY_HEIGHT:
                $function = '_cut_by_height';
                break;
            case self::CUT_BY_FIXED:
                $function = '_cut_by_fixed';
                break;
            case self::CUT_BY_REGION:
                $function = '_cut_by_region';
                break;
            default:
                $function = '_cut_by_auto';
                break;
        }
        $size_arr = $this->$function($size_arr);
        //完成resize后把src转换为dst
        $this->_src = $this->_dst;
        $this->_src_info[0] = $size_arr[0];
        $this->_src_info[1] = $size_arr[1];
        return $this;
    }

    /**
     * 根据给出的参数大小返回一个画面资源
     * @param array $siza_arr array($width, $height);
     */
    private function _create_canvas($size_arr = array(100,100))
    {
        list($width, $height) = $size_arr;
        $this->_addDebugInfo( sprintf('[%s] 创建画布大小 {%d , %d}', __FUNCTION__, $width,$height) );
        
        $img = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img,0,0,$white);

        return $img;
    }

    /**
     * 根据坐标截取区域图形
     * @param $param
     * @todo 修正压缩图片时 对于 宽或者 高 不足的图片 适配 和 空白补充
     * @version 2013-6-27
     * @author wwpeng
     */
    private function _cut_by_region($param)
    {
    	$dst_x = 0;
    	$dst_y = 0;
    	$top_x = 0;
    	$top_y = 0;
    	if (count($param) == 4)
    	{
    		list($top_x, $top_y, $width, $height) = $param;
    		if($top_x < 0)
    		{
    			$this->_addDebugInfo( sprintf('[%s] 左止角不可以小于0, 自动调整为 0 ', __FUNCTION__), self::DEBUG_NOTICE );
    			$top_x = 0;
    		}
    		if($top_y < 0)
    		{
    			$this->_addDebugInfo( sprintf('[%s] 左止角不可以小于0, 自动调整为 0 ', __FUNCTION__), self::DEBUG_NOTICE );
    			$top_y = 0;
    		}
    		
    	}elseif (count($param) == 2){
    		
    		list($width, $height) = $param;
    		$width_rate = $width/$this->_src_info[0];
    		$height_rate = $height/$this->_src_info[1];
    		if ($width_rate >= 1 || $height_rate >= 1)
    		{
    			$dst_x = $width_rate >= 1 ? (int)(($width - $this->_src_info[0])/2) : 0;
    			$dst_y = $height_rate >= 1 ? (int)(($height- $this->_src_info[1])/2) : 0;
    			$top_x = $width_rate >= 1 ? 0 : (int)(($this->_src_info[0] - $width)/2);
    			$top_y = $height_rate >= 1 ? 0 : (int)(($this->_src_info[1] - $height)/2);
    			
    		}else{
    			
    			if ( $width_rate > $height_rate)
    			{
    				$function = '_cut_by_width';
    			
    			}elseif( $width_rate < $height_rate ){
    			
    				$function = '_cut_by_height';
    			}else{
    			
    				$function = '_cut_by_fixed';
    			}
    			$size_arr = $this->$function($param);
    			//完成resize后把src转换为dst
    			$this->_src = $this->_dst;
    			$this->_src_info[0] = $size_arr[0];
    			$this->_src_info[1] = $size_arr[1];
    			$top_x = (int)(($this->_src_info[0] - $width)/2);
    			$top_y = (int)(($this->_src_info[1] - $height)/2);	
    		}
    		
    	}
    	if($width <= 0)
    	{
    		$this->_addDebugInfo( sprintf('[%s] 宽度不可以小于0, 自动调整为 1 ', __FUNCTION__), self::DEBUG_NOTICE );
    		$width = 1;
    	}
    	if($height <= 0)
    	{
    		$this->_addDebugInfo( sprintf('[%s] 高度不可以小于0, 自动调整为 1 ', __FUNCTION__), self::DEBUG_NOTICE );
    		$height = 1;
    	}

        $size_arr = array( $width, $height);
        $this->_dst = $this->_create_canvas($size_arr);
        
        $width = $width > $this->_src_info[0] ? $this->_src_info[0] : $width;
        $height = $height > $this->_src_info[1] ? $this->_src_info[1] : $height;
        
        imagecopyresampled($this->_dst, $this->_src, $dst_x, $dst_y, $top_x, $top_y, $width, $height, $width, $height);
        return $size_arr;
    }

    /**
     * 根据宽度的比例来处理图片的缩略图
     * @param float $width 图像的百分比
     */
    private function _cut_by_width($param)
    {
        if($param[0] <= 0)
        {
            $this->_addDebugInfo( sprintf('[%s] 目标图像的宽度不可以小于或等于 0, 自动调整为 1 ', __FUNCTION__), self::DEBUG_NOTICE );
            $param[0] = 1;
        }
        if( $param[0] >= $this->_src_info[0] )
        {
            $this->_addDebugInfo( sprintf('[%s] 图像原宽小于指定宽， 不进行处理', __FUNCTION__), self::DEBUG_NOTICE );
            $size_arr = array( $this->_src_info[0], $this->_src_info[1]);
            $this->_dst = $this->_src;
            
        }else{
        	
            $height = ceil($this->_src_info[1]*($param[0]/$this->_src_info[0]));
            $size_arr = array( $param[0], $height);
            $this->_dst = $this->_create_canvas($size_arr);
            imagecopyresampled($this->_dst, $this->_src, 0, 0, 0, 0, $size_arr[0], $size_arr[1], $this->_src_info[0], $this->_src_info[1]);
        }
        return $size_arr;
    }

    /**
     * 根据图片的宽度来处理图片的缩略图
     * @param float $height 图像的调试百分比
     */
    private function _cut_by_height($param)
    {
        if($param[1] <= 0)
        {
            $this->_addDebugInfo( sprintf('[%s] 目标图像的高度不可以小于或等于 0, 自动调整为 1 ', __FUNCTION__), self::DEBUG_NOTICE );
            $param[1] = 1;
        }

        if( $param[1] >= $this->_src_info[1] )
        {
            $this->_addDebugInfo( sprintf('[%s] 图像原高小于指定高， 不进行处理', __FUNCTION__), self::DEBUG_NOTICE );
            $size_arr = array( $this->_src_info[0], $this->_src_info[1]);
            $this->_dst = $this->_src;
            
        }else{
        	
            $width = ceil($this->_src_info[0]*($param[1]/$this->_src_info[1]));
            $size_arr = array( $width, $param[1] );
            $this->_dst = $this->_create_canvas($size_arr);
            imagecopyresampled($this->_dst, $this->_src, 0, 0, 0, 0, $size_arr[0], $size_arr[1], $this->_src_info[0], $this->_src_info[1]);
        }
        return $size_arr;
    }

    /**
     * 根据图片的固定宽高来处理图片缩略图生成强制固定大小
     * @param $width
     * @param $height
     */
    private function _cut_by_fixed($param)
    {
        if($param[0] <= 0)
        {
            $this->_addDebugInfo( sprintf('[%-020s] 目标图像的宽度不可以小于或等于 0, 自动调整为 1 ', __FUNCTION__), self::DEBUG_NOTICE );
            $param[0] = 1;
        }

        if($param[1] <= 0)
        {
            $this->_addDebugInfo( sprintf('[%s] 目标图像的高度不可以小于或等于 0, 自动调整为 1 ', __FUNCTION__), self::DEBUG_NOTICE );
            $param[1] = 1;

        }

        $size_arr = $param;
        $this->_dst = $this->_create_canvas($size_arr);
        imagecopyresampled($this->_dst, $this->_src, 0, 0, 0, 0, $size_arr[0], $size_arr[1], $this->_src_info[0], $this->_src_info[1]);
        return $size_arr;
    }

    /**
     * 根据图片的宽高来自动处理图片缩略图生成强制固定大小
     * @param $width
     * @param $height
     */
    private function _cut_by_auto($param)
    {
        if($param[0] <= 0)
        {
            $this->_addDebugInfo( sprintf('[%-020s] 目标图像的宽度不可以小于或等于 0, 自动调整为 1 ', __FUNCTION__), self::DEBUG_NOTICE );
            $param[0] = 1;
        }

        if($param[1] <= 0)
        {
            $this->_addDebugInfo( sprintf('[%s] 目标图像的高度不可以小于或等于 0, 自动调整为 1 ', __FUNCTION__), self::DEBUG_NOTICE );
            $param[1] = 1;
        }

        $width_rate = $param[0]/$this->_src_info[0];
        $height_rate = $param[1]/$this->_src_info[1];

        if ( $width_rate > $height_rate)
        {
            $function = '_cut_by_height';

        }elseif( $width_rate < $height_rate ){

            $function = '_cut_by_width';
        }else{
        	
        	$function = '_cut_by_fixed';
        }
        return $this->$function($param);
    }
    /**
     * 获取文件的扩展名和图像大小信息, 并判定其后缀名合法性.
     * @param $file 文件路径
     * @return 返回文件的宽高和扩展名,失败返回false
     */
    public function get_file_info($file)
    {
        if ( empty($file) || !file_exists($file) )
        {
            $this->_addDebugInfo( sprintf('[%s] 加载文件为空 或 不存在', __FUNCTION__), self::DEBUG_WARN );
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $file_info = getimagesize($file);


        //如果是未知的扩展名的话
        if( !array_key_exists($ext, $this->_ext_arr) )
        {
            $this->_addDebugInfo( sprintf('[%s] 非法的文件后缀', __FUNCTION__), self::DEBUG_WARN );
        }else{
            $file_info[2] = $this->_ext_arr[$ext];
        }

        return $file_info;

    }
    
    /**
     * 添加一条调试信息
     * @param string $info
     * @param string $type
     */
    private function _addDebugInfo($info = '', $type = self::DEBUG_INFO)
    {
        if(!$this->_debug)
            return false;

        $style = 'color: green;';
        $can_break = false;
        switch($type)
        {
            case self::DEBUG_NOTICE:
                $style = 'color: purple;';
                break;
            case self::DEBUG_WARN:
                $style = 'color: red;';
                $can_break = true;
                break;
        }

        $msg = sprintf('<li style="%s">[%s][%10.4f] %s</li>', $style, $type, microtime(true) ,$info);
        self::$debug_info[] = $msg;

        if($can_break)
        {
            $this->debug_info();
            exit;
        }
    }
}
