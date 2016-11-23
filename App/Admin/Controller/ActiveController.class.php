<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/5/20
 * Time: 13:55
 */

namespace Admin\Controller;


use Admin\Model\ActiveModel;
use Lib\Image;
use Think\Controller;
use Think\Page;

class ActiveController extends Controller {

    /**
     *待审核活动数量
     */
    public function verify_count(){
        echo M('Active')->where(array('status'=>ActiveModel::ACTIVE_STATUS_WAITING))->count();
    }
    /**
     * 处理强制关闭活动提交过来的数据
     * @param unknown $id
     * @param unknown $reject
     */
 	public function close($id,$reject){
    	$map['status']=ActiveModel::ACTIVE_STATUS_REJECT;
    	$map['reject_info']=$reject;
    	$r=M("active")->where("id=%d",$id)->save($map);
    	if($r){
    		$this->redirect("/active/admin");
    	}else{
    		$this->error("强制关闭活动出错了");
    	}
    }

    /**
     * 后台管理，取得活动上传图片
     * @param int $aid 活动编号
     */
    public function Picture($aid=0,$p=1,$pagesize=30,$id=0,$user_id=0,$username='',$description='',$stime='',$etime=''){
        $active=M('Active')->find($aid);
        if(!$active){
            $this->error('活动不存在');exit;
        }
        $count=M('project_back_ext as pic')->join(" left join tb_discuse  as d on pic.did=d.id")->where(array('relation_id'=>$aid,'is_creator'=>1,'type'=>6))->count();
        $page=new Page($count,$pagesize);

        $data=M('project_back_ext as pic')->join(" left join tb_discuse  as d on pic.did=d.id")->where(array('relation_id'=>$aid,'is_creator'=>1,'type'=>6))->field("did,d.user_id,d.status,d.create_date,pic.image,content")->order("d.status desc ,d.create_date desc")->limit($page->firstRow.','.$page->listRows)->select();
        foreach ($data as $key => $value) {
            $uid=$value['user_id'];
            $image=$value['image'];
            $data[$key]['img_url']=Image::getUrl($image,array(90,90));
            $data[$key]['user']=D('User')->find($uid);
        }
        $active['user']=D('User')->find($active['uid']);

        $this->data=$data;
        $this->count=$count;
        $this->firstRow=$page->firstRow;
        $this->lastRow= $page->firstRow+$page->listRows;
        $this->lastRow= $this->lastRow>$page->totalRows?$page->totalRows: $this->lastRow;
        $this->active=$active;
        $this->pageHtml=pageHtml($p,ceil($count/$pagesize));
        $this->display('Picture');
    }

    /**
     * 删除活动上传的图片
     * @param int $aid 活动编号
     * @param int $did 图片编号
     */
    public function delpic($aid=0,$did=0){
        $result=M("discuse")->where('id=%d and relation_id=%d and type=6 and status=1',$did,$aid)->save(array('status'=>-1));
        if($result){
            $this->ajaxReturn(array('status'=>1,'msg'=>'删除成功'));
        }else{
            $this->ajaxReturn(array('status'=>1,'msg'=>'删除失败'));
        }
    }
}