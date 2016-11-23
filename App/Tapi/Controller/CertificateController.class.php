<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace TApi\Controller;
use Think\Controller;
/**
 * Description of CertificateController
 *
 * @author Administrator
 */
class CertificateController extends Controller{
    /**
     * 某用户所获的证书信息
     */
    public function cartificate_user($uid){
        $data=M('cartificate_user')->where("uid=".$uid)->select();
    	$this->ajaxReturn($data);
    }
}
