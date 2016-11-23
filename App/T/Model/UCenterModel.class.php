<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;

use Lib\Image;
use Think\Model;
use Lib\Image\UploadedFile;
use Lib\City;
use Lib\Helper;

/**
 * Description of UCenterModel
 *
 * @author Administrator
 */
class UCenterModel{

    /**
	 * 用户状态常量
	 * 0：用户注册成功，但是邮箱未验证
	 * 1：用户邮箱验证成功
	 * -1：用户有已经被屏蔽
	 * @version 2013-5-20
	 * @author wwpeng
	 */
	const USER_STATUS_UNEMAIL = 0;
	const USER_STATUS_ENEMAIL = 1;
	const USER_STATUS_DISABLE = -1;
	
	const USER_PHONE_STATUS_NOT_VERIFIED = 0;
	const USER_PHONE_STATUS_VERIFIED = 1;
	
	/**
	 * 用户类型常量
	 * 个人账户  10 普通用户  11 认证用户
	 * 组织账户
	 * 20 未填写资料的组织
	 * 21 填写了资料的组织
	 * @version 2013-5-20
	 * @author wwpeng
	 */
	const USER_TYPE_NORMAL = 10;
	const USER_TYPE_VOLUNTEER = 11;
	const USER_TYPE_UNORG = 20;
	const USER_TYPE_ORG = 21;
	/**
	 * 用户性别类型数值常量
	 * 0 未知
	 * 1 男
	 * 2 女
	 * @version 2013-7-1
	 * @author wwpeng
	 */
	const USER_GENDER_UNKNOW = 0;
	const USER_GENDER_MAN = 1;
	const USER_GENDER_WOMAN = 2;
	/**
	 * 用户昵称和组织名称限制规则的正则表达式
	 * @version 2013-8-27
	 * @author wwpeng
	 * @var string
	 */
	const USER_NICKNAME_PREG = '/^[-_\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u';
    /**
     * 公益组织
     */
    public function orgvip() {
        //从配置中 获取首先显示的组织
        $vipIds = explode(',', ORGANIZATIONVIPIDS);
        //根据组织编号 调取组织信息
        $data = getApiContent(UCENTER.'/api/organization/getListByUids?uids='.$vipIds, false, true);
        if($data && isset($data['item']) && is_array($data['item'])){
            foreach ($data['item'] as $key => $value) {
                $uid=$value['uid'];
                $data['item'][$key]['url']=$this->orgUrl($uid);
                //<?php echo City::getName($organization['provinceid']).' - '.City::getName($organization['cityid']);
                $data['item'][$key]['area']= City::getName($value['provinceid']).' - '.City::getName($value['cityid']);
            }
        }
        return $data;
    }
    /**
     * 组织链接地址
     * 用原来的地址 待转移后用 U函数
     */
    private function orgUrl($uid){
        return UCENTER.'/user/index?uid='.$uid;
       //  Uc::userUrl($organization['uid']);
    }
    /**
     * 用户个数
     * @param type $limit
     */
    public function UserCount(){
        $_key='ucenter_model_usercount';
        $data=S($_key);
        if($data){
            return $data;
        }
        $data = getApiContent(UCENTER.'/api/user/UserAll/',false,true);
        S($_key,$data? $data['page']['totalCount']:0);
        return $data? $data['page']['totalCount']:0;
    }
    /**
     * 认证用户
     */
    public function volunteer($limit=12){
        $data = getApiContent(UCENTER.'/api/volunteer/list/limit/'.$limit,false,true);
        /**
         *   ["uid"] => string(3) "214"
      ["nickname"] => string(7) "Rogeecn"
      ["photo"] => string(68) "http://fs.yiii.org/user/120_120_b5a9dc9c8fa7b6cf6f013fb3c84d2d4f.jpg"
      ["provinceid"] => string(1) "2"
      ["cityid"] => string(3) "451"
         */
        if($data && isset($data['item']) && is_array($data['item'])){
            foreach($data['item'] as $key=>$value){
                $uid=$value['uid']; 
                $area=City::getName($value['provinceid']).'-'.City::getName($value['cityid']);
                $data['item'][$key]['url']=$this->orgUrl($uid);
                $data['item'][$key]['area']=$area;
            }
        }
        return $data;
    }
    /**
     * 组织信息 不是配置的信息
     */
    public function org($limit=16){
        $_key='ucenter_model_org_'.$limit;
        $data=S($_key);
	
        if($data){
            return $data;
        }

        $vipIds ='';// explode(',', ORGANIZATIONVIPIDS);
        $data =getApiContent(UCENTER.'/api/organization/list/limit/'.$limit.'?removeUids='.$vipIds,false,true);
        
        if($data && isset($data['item']) && is_array($data['item'])){
            foreach($data['item'] as $key=>$value){
                $data['item'][$key]['url']=$this->orgUrl($value['uid']);
                $photo=$value['photo_photo'];
                $gender=$value['gender'];
                $data['item'][$key]['photo_photo']=Image::getUrlThumbFilled($photo,array(150,70),$this->getPhotoDefaultType($gender));// Image::getUrl($photo,null,$this->getPhotoDefaultType($gender));// UploadedFile::getFileUrl($photo,null,$this->getPhotoDefaultType($gender));
            }
        }
        S($_key,$data,600);
        return $data;
    }
     /**
    * 获得用户头像图片的时候用户获得差异化的默认图片类型
    * @version 2013-9-10
    * @author wwpeng
    * @return string
    */
   public function getPhotoDefaultType($gender = null)
   {
       if ($gender == self::USER_GENDER_WOMAN || $gender == '女')
           {
                   return 'user_girl';

           }else{

                   return 'user';
           }
   }
   /**
    * 判断用户是否登录
    */
   public static function getUserInfo(){
       return getApiContent(UCENTER.'/api/user/IsLogin',false,false);
   }

}
