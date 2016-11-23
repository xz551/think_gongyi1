<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2016/5/14
 * Time: 10:31
 */

namespace T\Controller;


use Think\Controller;
use Org\Util\String;

/**
 * 数据操作控制器
 * Class DataController
 * @package T\Controller
 */
class DataController extends Controller
{
    /**
     * 导入用户
     */
    public function user(){
    layout(false);
      
         $data = D('User')->where(" server_tag is null ")->limit('0,20000')->select();
	   
	$i=0;
        foreach($data as $key=>$value){
            $_new_data=array();
            $uid=$value['uid'];
	    
            $server_tag=$value['server_tag'];
            if(empty($server_tag)){
                $_s_tag=$this->mrand_server_tag();
                $_new_data['server_tag']=$_s_tag;
                $_new_data['server_tag_leng']=mb_strlen($_s_tag);
            }
            $ability=$value['ability'];
            if(empty($ability)){
                $_s_ability=$this->mrand_label();
                $_new_data['ability']=$_s_ability;
                $_new_data['ability_leng']=mb_strlen($_s_ability);
            } 
            if(!empty($_new_data)){
                $result=D('User')->where(" uid =".$uid)->save($_new_data); 
		if($result){
			$i++;
		}
            }
        }
	if(IS_AJAX){
		echo $i;
	}else{
	$this->display();
	}
        

    }
    public function mrand_server_tag(){
        $server_tag=array(
            '1'=>'其它',
            '2'=>'支教助学',
            '3'=>'医疗救助',
            '4'=>'环境保护',
            '5'=>'人文关怀',
            '6'=>'心愿梦想',
            '7'=>'海外服务',
            '8'=>'助残助困',
            '9'=>'扶幼敬老',
            '10'=>'紧急援助',
            '11'=>'社区服务'
        );
        //取几个
        $count=mt_rand(1,11);
        $rand_key=array_rand($server_tag,$count);
        if(!is_array($rand_key)){
            $rand_key=array($rand_key);
        }
        $result_tag=array();
        foreach($server_tag as $key=>$value){
            if(in_array($key,$rand_key)){
                $result_tag[$key]=$value;
            }
        }
        return join($rand_key,',');
    }

    private function mrand_label(){
        $label=array('医疗急救','体育竞技','驾驶运输','法律服务','信息技术','经验管理、公共关系','教育培训','电器维修','安全保卫','财会金融','客户接待','新闻写作','设计创意','物业养护','摄像摄影','音乐','其他');
        //取几个
        $count=mt_rand(1,count($label));
        $rand_key=array_rand($label,$count);
        //取几个
        $count=mt_rand(1,11);
        $rand_key=array_rand($label,$count);
        if(!is_array($rand_key)){
            $rand_key=array($rand_key);
        }
        $result_tag=array();
        foreach($label as $key=>$value){
            if(in_array($key,$rand_key)){ 
		array_push($result_tag,$value);
            }
        }  
        return join($result_tag,',');

    }

    /**、
     * 密码重置
     * @param int $uid
     * @param string $pwd
     */
    public function pwd($uid=0,$pwd='888888'){
        layout(false);
        if(IS_AJAX) {
            if(empty($uid)){
                echo '编号错误';exit;
            }
            $result=D('User')->where("uid=" . $uid)->save(array("password" => md5($pwd)));
	   
            echo $result===false?'失败':'成功';
        }else{
            $this->display();
        }

    }

    public function uemail(){ 
    layout(false);
        if(!IS_AJAX){ 
            return $this->display();
        }
        $x=array('zhao','qian','sun','li','zhao','wu','zheng','wang','feng','chen','chu','wei','jiang','shen','han','yang','zhu','qin','you','he','xu','lv','shi','zhang','kong','cao','yan','hua','jin','wei','tao','qi','xie','zou','yu','bo','shui','dou','yun','su','pan','ge','xi','fan','peng','lang','lu','ma','miao','feng','hua','fang','ren','yuan','bao','shi','tang','fei','lian','qin','xun','lei','he','ni','teng','yin','luo','hua','he','an','chang','le','pi','meng','huang','mao','pang','xiong','dong','liang','du','jiang','tong','mei','sheng','lin','guo');
        $email_fix=array('126.com','163.com','sina.com.cn','yahoo.cn');
        $max=count($x);
        $uids=D('ucenter.user_uid')->order("id desc")->limit(0,2000)->select();
        if($uids){
            foreach($uids as $key=>$value){
                $max_uid=$value['max_uid'];
                $id=$value['id'];
                $type=String::randNumber(1,4);
                $mail='';
                if($type==1){
                    //qq邮箱
                    $qq=$this->qq(7,11);
                    $mail=$qq.'@qq.com';
                }else {
                    //163 126 邮箱
                    //产生一个索引
                    $index=String::randNumber(0,$max-1);
                    $xing=$x[$index];
                    //取出一个字符
                    $xing_ming=$xing.String::randString(String::randNumber(3,6),3);
                    $fix=$email_fix[String::randNumber(0,3)];
                    $mail=$xing_ming.'@'.$fix;
                }
                $result=D('User')->where('uid='.$max_uid)->save(array('email'=>$mail));
                if($result){
                    D('ucenter.user_uid')->where('id='.$id)->delete();
                }
            }
        }
	    echo time();
    }
    private function qq($leg,$end){
        $count=String::randNumber($leg,$end);
        $q='';
        for($i=0;$i<=$count;$i++){
            $q.=String::randNumber(1,9);
        }
        return $q;
    }

}