<?php 
namespace Wx\Service;

/**
 * 导航条设置
 * 自定义菜单
 *
 * @author Administrator
 */
class WxMenuService {
    /**
     * 创建导航条信息
     */
    public function createMenu($mp){
        $wx=D('WxAccessToken','Service'); 
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$wx->AccessToken($mp);
        $res=post($url, C('wx_menu3'));
        echo $res;
    }
    public function del($mp){
        $wx=D('WxAccessToken','Service'); 
        $url='https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$wx->AccessToken($mp);
        $res=get($url);
        echo $res;         
    }
}
