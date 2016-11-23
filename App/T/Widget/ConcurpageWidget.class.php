<?php
namespace T\Widget;
use Lib\UserSession;
class ConcurpageWidget extends WController {
    /**
     * 物资页面
     * @param $isown 是否为登录用户自己访问 1-自己访问  0-其他人访问
     * @param $status 1-申请 2-已申请等待确认 3-已申请且已确认，4-拒绝请求,0-已结束
     * @param $islogin 1-已登录 0-未登录
     * @param $type 1-求助， 2-捐助
     */
    public function supplies($isown=1,$status=1,$islogin=1,$type=1){
        /**
         * 自己访问
         */

        $this->assign('concurtype',$type);
        if($isown == 1 && $islogin == 1){
            //未结束
            if($status){        
                $this->display("Widget/Concurpage:supMineNotend");
            //已结束    
            }else{ 
                $this->display("Widget/Concurpage:supMineEnd");           
            }
        }
        /**
         * 其他人访问
         */
        if($isown == 0 && $islogin == 1){
            //没有任何操作（捐物资界面）
            if($status==1){
                $this->display("Widget/Concurpage:supOther");
            //已经请求，等待发布用户确认
            }elseif($status==2){
                $this->display("Widget/Concurpage:supOtherRequest");
            //请求成功，且发布用户已确认
            }elseif($status==3){
                $this->display("Widget/Concurpage:supOtherConfirm");
            //已拒绝
            }elseif($status==4){
                $this->display("Widget/Concurpage:supOtherRefuse");
            //已结束
            }elseif(!$status){
                $this->display("Widget/Concurpage:supOtherRequestEnd");
            }  
        }
        
        /**
         * 未登录用户访问
         */
        if(!$islogin){
            //未结束
            if($status){
                $this->display("Widget/Concurpage:supOther");
            //已结束
            }else{
                $this->display("Widget/Concurpage:noLoginEnd");
            }
        }
        
    }
    /**
     * @param number $type 1-求助， 2-救助
     * @param $status 1-申请 2-已申请等待确认 3-已申请且已确认，4-拒绝请求,0-已结束
     */
    public function service($isown=1,$status=1,$islogin=1,$type=1){
        $this->assign('servicetype',$type);
        /**
         * 自己访问
         */
        if($isown == 1 && $islogin == 1){
            //未结束
            if($status){        
                $this->display("Widget/Concurpage:serviceMineNotend");
            //已结束    
            }else{ 
                $this->display("Widget/Concurpage:serviceMineEnd");           
            }
        }
        /**
         * 其他人访问
         */
        if($isown == 0 && $islogin == 1){
            //没有任何操作（捐服务界面）
            if($status==1){
                $this->display("Widget/Concurpage:serviceApply");
            }
            //已经请求，等待发布用户确认
            if($status==2){
                $this->display("Widget/Concurpage:serviceApplyWait");
            }
            //请求成功，且发布用户已确认
            if($status==3){
                $this->display("Widget/Concurpage:serviceApplyAgree");
            }
            //已拒绝
            if($status==4){
                $this->display("Widget/Concurpage:serviceRefuse");
            }
            //已结束
            if(!$status){
                $this->display("Widget/Concurpage:serviceEnd");
            }  
        }
        
        /**
         * 未登录用户访问
         */
        if(!$islogin){
            //未结束
            if($status){
                $this->display("Widget/Concurpage:serviceApply");
            //已结束
            }else{
                $this->display("Widget/Concurpage:serviceEnd");
            }
        }
        
    }

    
    
    
    
}