<?php
namespace Admin\Controller;
use Think\Controller;
class CompController  extends Controller{


    public function complain($p=1,$pageSize=50){
        $result=D('Complain')->page($p.','.$pageSize)->select();
        /**
         * 1.项目投诉 2.用户投诉 3互助 4活动
         */
        $type_arr=array(
            '1'=>'投诉项目',
            '2'=>'投诉用户',
            '3'=>'投诉互助',
            '4'=>'投诉活动'
        );
        foreach($result as $key=>$value){
            $type=$value['complain_type'];
            $result[$key]['type_name']=$type_arr[$type];
            //投诉对象
            $cuser_id=$value['cuser_id'];
            if($cuser_id!=-1){
                //查询user
                $_user=D('User')->cache(true)->find($cuser_id);
	 
                if($_user){
                    $result[$key]['cuser']=$_user['nickname'];
                }else{
                    $result[$key]['cuser']="中青公益";
                }
            }else{
                $result[$key]['cuser']="中青公益";
            }
        } 

        $count=D('Complain')->count();
        $this->data=$result;
        $pageCount = ceil($count / $pageSize);
        $this->pageHtml = pageHtml($p, $pageCount);
        $this->display();
    }
}
