<?php

namespace T\Controller;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Lib\Ftp;
use Lib\Image;
use Lib\Init;
use Lib\LetvCloudV1;
use Lib\QqLoginCfg;
use Lib\UserSession;
use Lib\VideoUrlParser;
use Think\Controller;
use Think\Log;

/**
 * Description of TestController
 *
 * @author Administrator
 */
class TestController extends Controller
{

    public function index()
    { 
    phpinfo();
       //$ftp=new Ftp();
       // $ftp->up_file('/Library/Server/Web/Data/Sites/think_gongyi/wx.jpg','');
    }

    /**
     * 抓取志愿时活动
     * 根据活动抓取人
     */
    public function active_125cn(){
        $url="http://125cn.net/activity/detail/";
        $max=152136;
        $error_con='Redirecting to http://125cn.net/404.html';
        for($i=$max;$i>1;$i--){
            $active_data=array();
            $_content=api($url.$i);
            if(stripos($_content,$error_con)!==false){
                continue;
            }
            //去掉换行
            $_content = str_replace(PHP_EOL, '', $_content);

            if (preg_match('/<p>地点：(.[^<]+)</m', $_content, $regs)) {
                $address = $regs[1];
            }else{
                continue;
            }
            if($address=="上线"){
                $active_data['type']=1;
            }else{
                $active_data['type']=2;
                $this->search_add($address,$active_data);
            }
            //取得项目标题
            if (preg_match('/<h1>(.[^<]+)/', $_content, $regs)) {
                $active_data['name']=$regs[1];
            } else {
               continue;
            }
            //取得项目时间 <p>时间：(.[^<]+)<
            if (preg_match('/<p>时间：(.[^<]+)</', $_content, $regs)) {
                //2016-03-12 08:30 -- 2016-03-12 12:30
                $_begin_end=mb_split($regs[1],' -- ');
                $_start_time=strtotime($_begin_end[0]);
                $_end_time=strtotime($_begin_end[1]);
                $active_data['start_time']=$_start_time;
                $active_data['end_time']=$_end_time;
            } else {
                continue;
            }
            //取得活动图片 image
            if (preg_match('/<img src="(.[^"]+)" alt=""  width="180" height="180" class="projectDetail-img"/m', $_content, $regs)) {
                $active_data['image']=$regs[1];
            } else {
                continue;
            }
            //取得项目描述
            $_content = str_replace('</div>', '$', $_content);
            $_content = str_replace('class="activityDetail-content">', 'class="activityDetail-content">$', $_content);
            if (preg_match('/content\">\$.[^\$]+/', $_content, $regs2)) {
                $active_data['description']=$regs[1];
            } else {
                continue;
            }
            //取得项目创建时间
            $active_data['description']=time();
            //最后更新时间
            $active_data['update_date']=time();
            //服务时长
            $active_data['server_time']=0;
            //标记
            $active_data['copy_type']=1;
            //标记
            $active_data['source']="125cn";
            //记录主键
            $active_data['sourcepid']=$i;
            //提取用户保存用户编号
            dump($active_data);
            exit;
        }
    }

    /**
     * 检索地址
     * provinceid
    cityid
    address
     */
    private function search_add($address,&$active_data)
    {
        //截取市字符串
        $_city_index =mb_stripos($address, '市');
        $_city = mb_substr($address, 0, $_city_index);
        //剩余部分
        $address = mb_substr($address, $_city_index + 1);
        //查找区
        $_qu_index = mb_stripos($address, '区');

        $address = mb_substr($address, $_qu_index + 1);
        //查找对应的城市编号
        $all = D('ProvinceCity')->getAll();
        $provinceid = 0;
        $cityid = 0; 
        foreach ($all as $key => $value) {
            if ($value['class_type'] == 2 && $value['class_name'] == $_city) {
                $provinceid = $value['class_parent_id'];
                $cityid = $value['id'];
                break;
            }
        }
        $active_data['provinceid'] = $provinceid;
        $active_data['cityid'] = $cityid;
        $active_data['address'] = $address;
    }
    private function getUser($_content){
        if (preg_match('%发起组织：<a href="(http://125cn\.net/space/(\d+))"%m', $_content, $regs)) {
            //$user_url=$regs[1];
            $user_125cn_id=$regs[2];
            //查找平台用户是否存在
            $user=D('user')->where('source="125cn" and qingyunid='.$user_125cn_id)->find();
            if($user){
                return $user['uid'];
            }else{
                //保存用户信息
            }
        }
        return 0;

    }
    private function curl_user($user_125cn_id){
        //http://125cn.net/space/15836
        $url="http://125cn.net/activity/detail/";

        $error_con='404错误，你访问的页面不存在';
        $user_data=array();
        $_content=api($url.$user_125cn_id);
        if(stripos($_content,$error_con)!==false){
            return 0;
        }
        //去掉换行
        $_content = str_replace(PHP_EOL, '', $_content);

    }
}

