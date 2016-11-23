<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;
use Think\Model;
/**
 * Description of CertificateModel
 *
 * @author zhangzhigang
 */
class CertificateModel extends Model{
    /**
     * 获得证书列表
     */
    public function getList(){
        return M('Certificate')->select();
    }
}
