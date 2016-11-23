<?php

namespace Wx\Service;
use Think\Model;
use Lib\Session\UserSession;
/**
 * 发送客服消息
 *
 * @author Administrator
 */
class WxCustomerService extends Model {    
    private $url;
    public function __construct($mp) {        
        $at=new \Wx\Service\WxAccessTokenService();
        $access_token =$at->AccessToken($mp);
        $this->url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" .$access_token;
    }

    /**
     * 发送文本消息
     */
    public function text($openid,$content) {
        $param = '{"touser":"' . $openid . '","msgtype":"text","text":{"content":"'.$content.'"}}';
        $result=post($this->url, $param);
        $r=json_decode($result);
        if($r->errcode!=0){
            //说明有问题，没有发送出去
            //存储到未读消息中
            M('message')->add(array(
                "MsgContent"=>$content,
                'CreateUser'=>  UserSession::GetUserId(),                
                'CreateDate'=>  now_time(),
                'UpdateUser'=>UserSession::GetUserId(),
                'UpdateDate'=>  now_time(),
                'MsgRead'=>0,
                'MsgStatue'=>1,
                'MsgType'=>'wx',
                'MsgSendUser'=>$openid
            ));
        }
    }
    /**
     * 
     */
    public function image($openid,$mediaid) {
        $param = '{
            "touser":"' . $openid . '",
            "msgtype":"image",
            "image":
            {
              "media_id":"'.$mediaid.'"
            }
        }';
        $result=post($this->url, $param);
        M('wx_sendmsg')->add(
                array(
                    'Url'=>$this->url,
                    'Content'=>$param,
                    'Type'=>'customer_image',
                    'Result'=>$result,
                    'CreateUser'=>  UserSession::GetUserId()?UserSession::GetUserId():UserSession::getOpenId(), 
                    'CreateDate'=>  now_time(),
                    'UpdateDate'=>  now_time(),
                    'UpdateUser'=>   UserSession::GetUserId()?UserSession::GetUserId():UserSession::getOpenId(), 
                ));
        
    }

    public function news($openid,$news) {
        $param = '{
            "touser":"' . $openid . '",
            "msgtype":"news",
            "news":{
                "articles": [%s]
            }
        }';
        $n='{
                "title":"%s",
                "description":"%s",
                "url":"'.web_site.'/t/%d?wcode=%s",
                "picurl":"%s"
            }';
        $ns='';
        $i=0;
        foreach($news as $key=>$value){
            $i++;
            $ns.=sprintf($n, $value['title'], $value['desc'], $value['topicId'], $openid, $value['picurl']);
            if($i<count($news)){
                $ns.=',';
            }
        }
        $param=  sprintf($param, $ns);
        $result=post($this->url, $param);
    }

}
