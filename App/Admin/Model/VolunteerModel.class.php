<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

use Think\Model;
use Lib\Image\UploadedFile;
use Lib\City;

/**
 * 认证用户管理
 * @author Administrator
 */
class VolunteerModel extends Model {

    protected $connection = "USER_CONTER";

    /**
     * 根据身份证号，取得用户信息
     * @param type $card
     */
    public function getListByCard($card) {
        $card="'". str_replace(",", "','", $card)."'";
        return $this->uData("`idcard_code` IN (" . $card . ")");
    }
    /**
     * 根据用户编号获得用户信息
     */
    public function getInfo($uid){
        return $this->uData("u.uid=".$uid);
    }
    private function uData($w){
        $sql = "SELECT real_name,u.uid,v.phone,u.photo,provinceid,cityid,v.idcard_code,u.email,u.gender,u.type FROM tb_volunteer as  v LEFT JOIN tb_user as u on v.uid=u.uid  WHERE ".$w;
        $list = D('Volunteer')->query($sql);
        foreach ($list as $key => $value) {
            $photo = $value['photo'];
            $list[$key]['photo'] = UploadedFile::getFileUrl($photo, array(200, 220), 'user');
            $list[$key]['province'] = sprintf('%s %s', City::getName($value['provinceid']), City::getName($value['cityid']));
            $list[$key]['gender'] = $value['gender'] == 0 ? '未知' : ($value['gender'] == 1 ? '男' : '女');
            $type=$value['type'];
            switch($type){
                case 10:
                    $type='普通帐号';
                    break;
                case 11:
                    $type='组织帐号';
                    break;
                case 20:
                    $type='未填写资料组织';
                    break;
                case 21:
                    $type='填写资料组织';
                    break;

            }
            $list[$key]['type'] =$type;
        }
        return $list;
    }
}
