<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/22 0022
 * Time: 下午 8:06
 */

namespace Admin\Controller;


use Think\Controller;

class UserController extends Controller
{
    /**
     * @param int $uid
     * @param string $nickname
     * @param string $real_name
     * @param string $phone
     * @param string $idcard_code
     * @param string $type
     * @param string $gender
     * @param $provinceid
     * @param string $cityid
     * @param string $countyid
     * @param string $iscard
     * @param string $isphone
     */
    public function userlist($uid=0,$nickname="",$real_name="",$phone="",$idcard_code="",$type="",$gender="",$provinceid=""
    ,$cityid='',$countyid="",$iscard="",$isphone="",$tags = "",$p=1,$pageSize=25){
        if($tags!='' && is_array($tags)){
            $tags=join(",",$tags);
        }
        $result=D("User")->userlist($uid,$nickname,$real_name,$phone,$idcard_code,$type,$gender,$provinceid,$cityid,$countyid,$iscard,$isphone,$tags,$p,$pageSize);
        $data=$result->data;
     
        $count=$result['count'];
        $pageCount=ceil($count/$pageSize);
        $this->pageHtml=pageHtml($p,$pageCount);
        $data=$result['data'];
        /**
         * 0 未申请志愿者
        10 待审核状态
        1 审核通过
        -1 审核未通过
         */
        foreach($data as $key=>$value){
            $status=$value['volunteer_status'];
            if($status==10){
                $data[$key]['volunteer_status']="待审核状态";
            }else if($status==1){
                $data[$key]['volunteer_status']="审核通过";
            }else if($status==-1){
                $data[$key]['volunteer_status']="审核未通过";
            }else{
                $data[$key]['volunteer_status']="未申请志愿者";
            }
        }
        $this->data=$data;
        $this->province=D("ProvinceCity")->province();
        if($provinceid!=""){
            $this->city=D("ProvinceCity")->getChildrenCity($provinceid);
        }
        if($cityid!=""){
            $this->county=D("ProvinceCity")->getChildrenCity($cityid);
        }
        $this->server=D('CategoryServer')->getAllLabel();
	
        $this->display();
        //$this->ajaxReturn($result,"json");
    }
}