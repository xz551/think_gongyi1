<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/10/30
 * Time: 10:24
 */

namespace TApi\Controller;

use Think\Controller;

class IndexController extends Controller
{

    private function getEWMPath($type = 0)
    {
        switch ($type) {
            case 1:
                return '/active';
            case 2:
            case 3:
                return '/project';
            case 4:
                return '/event';
            default:
                return '/other';
        }
    }

    private function getWEMContent($text, $type)
    {
        switch ($type) {
            case 1:
                return SERVER_VISIT_URL . '/active/view/id/' . $text . '.html';
            case 2:
                return SERVER_VISIT_URL . '/project/view/id/' . $text . '.html';
            case 3:
                return SERVER_VISIT_URL . '/raisegoods/view/id/' . $text . '.html';
            case 4:
                return SERVER_VISIT_URL . '/project/alleventlist/id/' . $text . '.html';
            default:
                return $text;
        }
    }

    /**
     * 生成二维码图片 不存放本地
     */
    public function ewm($text='http://www.gy.com'){
        if(!I('wemtype')){
            //$this->wx_ewm($text);
            //return;
        }
        //HTTP_REFERER
        $referer=$_SERVER['HTTP_REFERER'];

        if($referer!=null  && stripos($referer,DOMAIN)===false){
            $text='http://www.gy.com';
        }
        import("Lib.phpqrcode");
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 6;//生成图片大小
        //生成二维码图片
        \QRcode::png($text, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }
    public function wx_ewm($text){
        $logo =STATIC_SERVER_URL. '/public/images/wx.png';//准备好的logo图片
        $QR =SERVER_VISIT_URL .'/tapi/index/ewm?wemtype=1&text='.$text;
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $QR_height = imagesy($QR);//二维码图片高度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
        //输出图片
        Header("Content-type: image/png");
        ImagePng($QR);
    }
    /**生成二维码的接口 存放本地图片
     * 如果 type 为 1.2  生成的内容就是一个 url地址 传递过来的text是个编号
     * @param string $text 二维码的内容
     * @param int $type 二维码的类型 1：active活动 2：project项目 3 求助  4：征集  0：other其他
     */
    public function erweima($text = 'http://www.gy.com', $type = 0)
    {
        $result = array('status' => 0, 'data' => '');

        $text = urldecode($text);
        $content = $this->getWEMContent($text, $type);
        if($type!=0) {
            $name = '/' . bin2hex($text);//生成二维码的名字
        }else{
            $name = '/' . bin2hex($content);//生成二维码的名字
        }


        $path = ER_WEI_MA_PATH . $this->getEWMPath($type);//生成二维码存放的路径
        $name_png = $name . '.png';//没有待logo的二维码logo
        $logo_name = $name . '_2.png';//带有logo的二维码名称
        $logo = C('ER_WEI_MA_LOGO');//准备好的logo图片
	    if(!$logo){
            $logo_name=$name_png;
        }
        $logo_img = $this->getEWMPath($type) . $logo_name;

        

        //判断二维码是否存在
        if (!is_file($path . $logo_name)) { 
            import("Lib.phpqrcode");
            $errorCorrectionLevel = 'L';//容错级别
            $matrixPointSize = 6;//生成图片大小
            $QR = $path . $name_png;//已经生成的原始二维码图
            //生成二维码图片
            \QRcode::png($content, $QR, $errorCorrectionLevel, $matrixPointSize, 2);

            if ($logo) { 
                $QR = imagecreatefromstring(file_get_contents($QR));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $QR_width = imagesx($QR);//二维码图片宽度
                $QR_height = imagesy($QR);//二维码图片高度
                $logo_width = imagesx($logo);//logo图片宽度
                $logo_height = imagesy($logo);//logo图片高度
                $logo_qr_width = $QR_width / 5;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                //重新组合图片并调整大小
                imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                    $logo_qr_height, $logo_width, $logo_height);
	    //输出图片
            imagepng($QR, ER_WEI_MA_PATH . $logo_img);
            unlink($path . $name_png);
            }
            
        }
        $result['status'] = 1;
        $result['data'] = ER_WEI_MA_URL . $logo_img;
        $this->ajaxReturn($result);
    }

} 