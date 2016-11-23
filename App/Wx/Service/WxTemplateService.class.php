<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/3/6
 * Time: 16:37
 */

namespace Wx\Service;


use Think\Model;

class WxTemplateService extends Model {

    public $access_token;

    public $template_id='HyUvI4MzPFjO6xI32G1ZtaZflx4Vsn1r1vfPJeWhiJE';//'skYNkwGJiXDOELSi72D2ZZ7bDi0KSaeesXLyQUcvu_Q';
    public function __construct($mp) {
        $at=new \Wx\Service\WxAccessTokenService();
        $this->access_token =$at->AccessToken($mp);

    }

    /**发送模版消息
     * @param $first string 模版消息头
     * @param $keynote array 模版消息健
     * @param $remark 模版消息介绍
     * @param $openid 接受的用户
     * @param int $template_id 模版编号
     */
    public function sendMsg($first,$keynote,$remark,$url,$openid,$template_id=0){
        $template_id=$template_id?$template_id:$this->template_id;
        $send_data=array();
        $send_data['touser']=$openid;
        $send_data['template_id']=$template_id;
        $send_data['url']=$url;
        $send_data['topcolor']="#FF0000";
        $send_data['data']=array();
        $send_data['data']['first']=array('value'=>$first."\n",'color'=>'#173177');
        $_i=0;
        $_count=count($keynote);

        foreach($keynote as $key=> $value){
            $_i++;
            if($_i==$_count){
                $value.="\n";
            }
            $send_data['data']['keyword'.($key+1)]=array('value'=>$value,'color'=>'#173177');
        }

        $send_data['data']['remark']=array('value'=>$remark,'color'=>'#173177');

        $send_s=json_encode($send_data);
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->access_token;
        post($url,$send_s);

    }
}