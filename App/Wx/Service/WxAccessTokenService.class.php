<?php

namespace Wx\Service;

use Think\Model;

/**
 * Description of WxAccessTokenService
 *
 * @author Administrator
 */
class WxAccessTokenService extends Model {

    /**
     * session 存数 access_token的键
     */
    private $access_token = 'access_token';

    /**
     * session 存数 access_token创建的时间的键
     */
    private $access_token_createdate = 'access_token_createdate';

    /**
     * session 存数 access_token过期的键
     */
    private $access_token_expires_in = 'access_token_expires_in';

    /**
     * 提前过期时间
     * @var type 
     */
    private $forward = 10;

    /**
     * 从session中获取access_token
     * @return boolean
     */
    private function getAccessTokenBySession() { 
    	return false;
        $access_token = session($this->access_token);
        if (!$access_token) {
            return false;
        }
        $createdate = session($this->access_token_createdate);
        $expires_in = session($this->access_token_expires_in);
        if (!$createdate || !$expires_in) {
            return false;
        } 
        if (time() - strtotime($createdate) > $expires_in + $this->forward) {
            return false;
        }
        return $access_token;
    }

    /**
     * 将access_token的值保存到session中
     */
    private function setAccessTokenBySession($access_token, $createdate, $expires_in) {
        session($this->access_token, $access_token);
        session($this->access_token_createdate, $createdate);
        session($this->access_token_expires_in, $expires_in);
    }

    /**
     * 获取某组织 access_token
     * @param string $mp 组织微信 原始编号
     * @param bool $must 是否重新获取
     * @return string
     */
    public function AccessToken($mp,$must = false) {
      
        if ($must) {
            return $this->getAccessToken($mp);
        }
        $access_token = $this->getAccessTokenBySession();
        if ($access_token) {
            return $access_token;
        }
        $data = M('access_token')->where("MpOriginalId='%s'",$mp)->find();
         
        if ($data) { 
            $access_token = $data['access_token'];
            if ($access_token) {
                //上次获取的时间
                $CreateDate = $data['CreateDate'];
                //上次获取的时间戳
                $addtime = strtotime($CreateDate);
                //过期时间
                $expires_in = $data['expires_in'];
                //判断是否过期 提前十秒过期
                if (time() - $addtime > ($expires_in - $this->forward )) {
                    $access_token = '';
                } else {
                    $this->setAccessTokenBySession($access_token, $CreateDate, $expires_in);
                }
            }
        }
        
        if (!$access_token) {
            $access_token = $this->getAccessToken($mp);
        }
        return $access_token;
    }
    private $mpInfo;
    private function getMpInfo($mp){
        if(!$this->mpInfo){
            $this->mpInfo=M('Mp')->where("MpOriginalId='%s'",$mp)->find();
        }
        return $this->mpInfo;
    }
    private function getAccessToken($mp) {
        $_mpInfo=$this->getMpInfo($mp);
        $url = "https://api.weixin.qq.com/cgi-bin/token";
        $param = sprintf("grant_type=client_credential&appid=%s&secret=%s", $_mpInfo['AppId'], $_mpInfo['AppSecret']);
        $wxresult = get($url, $param); 
        $tokenobj = json_decode($wxresult);
        $access_token = $tokenobj->access_token;
        $errcode = $tokenobj->errcode;
        $errmsg = $tokenobj->errmsg;
        $expires_in = $tokenobj->expires_in;
        $CreateDate = now_time();
        $db = array(
            "access_token" => $access_token,
            'expires_in' => $expires_in,
            'errcode' => $errcode,
            'errmsg' => $errmsg,
            'CreateDate' => $CreateDate
        );        
        $token = M('access_token')->where("OriginalId='%s'",$mp)->find();
        if ($token) {
            $id = $token['Id'];
            M('access_token')->where('Id=' . $id)->save($db);
        } else {
            $db['OriginalId']=$mp;
            M('access_token')->add($db);
        }
        $this->setAccessTokenBySession($access_token, $CreateDate, $expires_in);
        return $access_token;
    }

}
