<?php

/*
 * 证书管理
 */

namespace Admin\Controller;
use Think\Controller;
/**
 * Description of CertificateController
 *
 * @author zhangzhigang
 */
class CertificateController extends Controller {
    /**
     * 颁发证书
     */
    public function Issued(){
        $this->certificate=D('Certificate')->getList();
        $this->display();
    }
    /**
     * 检验提交的用户，和选择的证书
     */
    public function examine(){
        //身份证集合
        $shenfz=I('shenfz');
        //证书
        $zhegnshu=I('zhengshu');
        //取得选择的人员集合
        $list=D('Volunteer')->getListByCard($shenfz);
        //取得证书信息
        $zs=M('Certificate')->find($zhegnshu);
        $this->ren=$list;
        $this->zs=$zs;
        $this->display();
    }
    /**
     * 人员绑定证书
     */
    public function bind(){
        $uids=I('uid');
        $zhengshu=I('zhengshu'); 
        $zs=M('certificate')->find($zhengshu);
        foreach ($uids as $key => $uid) {
            $yiyou=M('cartificate_user')->where(array(                
                'uid'=>$uid,
                'cartificate_id'=>$zhengshu
            ))->select();
            if(!$yiyou){
                $newid=M('cartificate_user')->add(array(
                    'uid'=>$uid,
                    'cartificate_id'=>$zhengshu,
                    'create_user'=>0,
                    'create_date'=>  now_time(),
                    'update_user'=>0,
                    'update_date'=>  now_time(),
                ));
                $user=D('Volunteer')->getInfo($uid);
                if($zhengshu==1){
                    //筑梦童声证书
                    $zsinfo=$this->getZsPath($user,$newid);
                    M('cartificate_user')->where("id=".$newid)->save(array(
                        "code"=>$zsinfo[0],
                        "path"=>$zsinfo[1]
                    ));
                }
            }
        }
        $this->success('证书颁发成功',U('Certificate/caruser','zs='.$zhengshu.'&zsname='.$zs['name']));
    }
    
    /**
     * 设置证书
     */
    private function getZsPath($user,$newid){
        $image = new \Think\Image(); 
        $image=$image->open(ROOT.'/zs/zmts.png'); 
        $font=ROOT.'/Font/xinsong.ttc';
        $color="#000000";
        $image_water=\Think\Image::IMAGE_WATER_WEST;
        if($newid<10){
            $newid="00".$newid;
        }elseif($newid<100){
            $newid="0".$newid;
        }
        $code="ZQ2014".$newid."zmts";
        $image=$image->text($code,$font,25,$color,$image_water,array(630,-540));        
        $image=$image->text($user[0]['real_name'],$font,22,$color,$image_water,array(140,-110));        
        $image=$image->text($user[0]['idcard_code'],$font,22,$color,$image_water,array(350,-10));
        $image=$image->text('筑梦童声公益计划',$font,22,$color,$image_water,array(500,45));
        $image=$image->text(date("Y"),$font,22,$color,$image_water,array(640,338));
        $image=$image->text(date("m"),$font,22,$color,$image_water,array(750,338));
        $image=$image->text(date("d"),$font,22,$color,$image_water,array(830,338));
        //证书文件名
        $file_name=uniqid();//'zs_fs_'.uniqid()."_png";
        //证书存放路径
        
        //IMAGE_SERVER_PATH
        $file_path=IMAGE_SERVER_PATH.'/zs/'; 
        $image=$image->save($file_path.$file_name.".png");
        return array($code,'zs_fs_'.$file_name."_png");
    }
    /**
     * 获得所有证书
     */
    public function carlist(){
        $this->data=M('certificate')->select();
        $this->display();
    }
    /**
     * 获得证书的人员
     */
    public function caruser($zs=0,$zsname=''){
        $car_user=M('cartificate_user')->where("cartificate_id=".$zs)->select();
        foreach ($car_user as $key => $value) {
           $car_user[$key]['user']= D('Volunteer')->getInfo($value['uid']);
        }
        $this->data=$car_user;
       // dump($car_user);
        $this->display();
    }
}
