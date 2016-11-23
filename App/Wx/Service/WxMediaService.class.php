<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/2/28
 * Time: 16:16
 */

namespace Wx\Service;


class WxMediaService  {

    /**将微信服务器图片下载到自己服务器上
     * @param $mp 公众号
     * @param $mediaId 多媒体编号
     * @return string 路径
     */
    public function downImg($mp,$mediaId){
        $at=new \Wx\Service\WxAccessTokenService();
        $token =$at->AccessToken($mp);
        $data=get('http://file.api.weixin.qq.com/cgi-bin/media/get','access_token='.$token.'&media_id='.$mediaId);
        $filepath="/Uploads/Public/Uploads/".date('Y-m-d',time()).'/';
        $serverpath = $_SERVER['DOCUMENT_ROOT'].$filepath;//图片保存的路径目录
        if(!is_dir($serverpath)){
            mkdir($serverpath,0777, true);
        }
        $filename=uniqid().'.jpg';
        $fp = @fopen($serverpath.$filename,"w"); //以写方式打开文件
        @fwrite($fp,$data); //
        fclose($fp);//完工，哈
        return $filepath.$filename;
    }
}