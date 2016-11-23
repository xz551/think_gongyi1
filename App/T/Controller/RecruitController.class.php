<?php
namespace T\Controller;
use Think\Controller;

class RecruitController extends Controller {
    /**
     * 招募项目列表
     * @author WuWangCheng
     * @param type $p
     * @param type $pageSize
     * @param type $type
     * @param type $status
     * @param type $label
     * @param type $provi
     * 
     */
    public function recruit($p=1,$pageSize=16,$type=1,$status=100,$label=0,$provi=0){
        //根据条件获取该条件下数据总数
        $count = D("Project")->getProjectCount($type,array(100,888,889),$pro_id,$provi);
        //服务标签
        $serverlabel = D("CategoryServer")->getAllLabel();
        //地域标签
        $address = D("ProvinceCity")->getChildrenCity();
        //获取所有服务标签的ID
        foreach($serverlabel as $key=>$val){
           $serv_id[] = $key;
        }
        //获取所有的地域标签ID
        foreach($address as $val){
            $addr_id[] = $val['id'];
        }
        $label = in_array($label,$serv_id)?$label:0;
        //判断参数是否合法
        $provi = in_array($provi,$addr_id)?$provi:0;
        //项目详细信息
        $project = D("Project")->getRecruitInfo(intval($p),$pageSize,$type,array(100,888,889),$label,$provi);
        //推荐
        $prohot = D("ProjectHot")->getHotList();
        //变量输出
        $this->assign("project",$project);
        $this->assign("label",$serverlabel);
        $this->assign("address",$address);
        $this->assign("prohot",$prohot);
        $this->assign('page',page($count,$pageSize));
        $this->assign("lab_cur",$label);
        $this->assign("pro_cur",$provi);
        $this->display("recruit");
    }
}