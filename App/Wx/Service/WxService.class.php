<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wx\Service;

/**
 * Description of WxService
 *
 * @author Administrator
 */
class WxService extends \Think\Model{
    
    public function valid($mp) {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if ($this->checkSignature($mp)) {
            echo $echoStr;
            exit;
        }
    }
    public function responseMsg() {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)) {

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
                                    <ToUserName><![CDATA[%s]]></ToUserName>
                                    <FromUserName><![CDATA[%s]]></FromUserName>
                                    <CreateTime>%s</CreateTime>
                                    <MsgType><![CDATA[%s]]></MsgType>
                                    <Content><![CDATA[%s]]></Content>
                                    <FuncFlag>0</FuncFlag>
                                    </xml>";
            if (!empty($keyword)) {
                $msgType = "text";
                $contentStr = "Welcome to wechat world!";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            } else {
                echo "Input something...";
            }
        } else {
            echo "";
            exit;
        }
    }

    private $mpInfo;
    private function getMpInfo($mp){
        if(!$this->mpInfo){
            $this->mpInfo=M('Mp')->where("MpOriginalId='%s'",$mp)->find();
        }
        return $this->mpInfo;
    }
    /***
     * 校验信息的真实性
     */
    public function checkSignature($mp) {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $_mpInfo=$this->getMpInfo($mp);
        $token = $_mpInfo['Token'];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    public function AccessToken($mp){
        $wx=D('WxAccessToken','Service');
        return $wx->AccessToken($mp); 
    }

    /**获取JS SDK 签名 的 算法
     * @param $noncestr 随机数
     * @param $jsapi_ticket 临时票据
     */
    public function getJsSdkSignature($url){
        $Signature_KEY='Signature_KEY'.$url;
        $sinature=S($Signature_KEY);
        if($sinature){
            return $sinature;
        }

        $at=new \Wx\Service\WxAccessTokenService();
        $access_token =$at->AccessToken('gh_44683d3dea40');
        //https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi
        $result=get('https://api.weixin.qq.com/cgi-bin/ticket/getticket', 'access_token='.$access_token.'&type=jsapi');
        $obj=json_decode($result);
        if($obj->errcode==0){
            $jsapi_ticket=$obj->ticket;
            $noncestr="JSsDkWeTouChWx123";
            $timestamp=time();
            $tmpArr=array('noncestr='.$noncestr,'timestamp='.$timestamp,'url='.$url,'jsapi_ticket='.$jsapi_ticket);
            sort($tmpArr,SORT_REGULAR);
            $tmpStr = implode($tmpArr,'&');
            $signature = sha1($tmpStr);

            $json_result='{"timestamp":"'.$timestamp.'","nonceStr":"'.$noncestr.'","signature":"'.$signature.'","url":"'.$url.'","jsapi_ticket":"'.$jsapi_ticket.'","string1":"'.$tmpStr.'"}';
            S($Signature_KEY,$json_result,7000);
            return $json_result;
        }
        return null;
    }
}
