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
     * ץȡ־Ըʱ�
     * ���ݻץȡ��
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
            //ȥ������
            $_content = str_replace(PHP_EOL, '', $_content);

            if (preg_match('/<p>�ص㣺(.[^<]+)</m', $_content, $regs)) {
                $address = $regs[1];
            }else{
                continue;
            }
            if($address=="����"){
                $active_data['type']=1;
            }else{
                $active_data['type']=2;
                $this->search_add($address,$active_data);
            }
            //ȡ����Ŀ����
            if (preg_match('/<h1>(.[^<]+)/', $_content, $regs)) {
                $active_data['name']=$regs[1];
            } else {
               continue;
            }
            //ȡ����Ŀʱ�� <p>ʱ�䣺(.[^<]+)<
            if (preg_match('/<p>ʱ�䣺(.[^<]+)</', $_content, $regs)) {
                //2016-03-12 08:30 -- 2016-03-12 12:30
                $_begin_end=mb_split($regs[1],' -- ');
                $_start_time=strtotime($_begin_end[0]);
                $_end_time=strtotime($_begin_end[1]);
                $active_data['start_time']=$_start_time;
                $active_data['end_time']=$_end_time;
            } else {
                continue;
            }
            //ȡ�ûͼƬ image
            if (preg_match('/<img src="(.[^"]+)" alt=""  width="180" height="180" class="projectDetail-img"/m', $_content, $regs)) {
                $active_data['image']=$regs[1];
            } else {
                continue;
            }
            //ȡ����Ŀ����
            $_content = str_replace('</div>', '$', $_content);
            $_content = str_replace('class="activityDetail-content">', 'class="activityDetail-content">$', $_content);
            if (preg_match('/content\">\$.[^\$]+/', $_content, $regs2)) {
                $active_data['description']=$regs[1];
            } else {
                continue;
            }
            //ȡ����Ŀ����ʱ��
            $active_data['description']=time();
            //������ʱ��
            $active_data['update_date']=time();
            //����ʱ��
            $active_data['server_time']=0;
            //���
            $active_data['copy_type']=1;
            //���
            $active_data['source']="125cn";
            //��¼����
            $active_data['sourcepid']=$i;
            //��ȡ�û������û����
            dump($active_data);
            exit;
        }
    }

    /**
     * ������ַ
     * provinceid
    cityid
    address
     */
    private function search_add($address,&$active_data)
    {
        //��ȡ���ַ���
        $_city_index =mb_stripos($address, '��');
        $_city = mb_substr($address, 0, $_city_index);
        //ʣ�ಿ��
        $address = mb_substr($address, $_city_index + 1);
        //������
        $_qu_index = mb_stripos($address, '��');

        $address = mb_substr($address, $_qu_index + 1);
        //���Ҷ�Ӧ�ĳ��б��
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
        if (preg_match('%������֯��<a href="(http://125cn\.net/space/(\d+))"%m', $_content, $regs)) {
            //$user_url=$regs[1];
            $user_125cn_id=$regs[2];
            //����ƽ̨�û��Ƿ����
            $user=D('user')->where('source="125cn" and qingyunid='.$user_125cn_id)->find();
            if($user){
                return $user['uid'];
            }else{
                //�����û���Ϣ
            }
        }
        return 0;

    }
    private function curl_user($user_125cn_id){
        //http://125cn.net/space/15836
        $url="http://125cn.net/activity/detail/";

        $error_con='404��������ʵ�ҳ�治����';
        $user_data=array();
        $_content=api($url.$user_125cn_id);
        if(stripos($_content,$error_con)!==false){
            return 0;
        }
        //ȥ������
        $_content = str_replace(PHP_EOL, '', $_content);

    }
}

