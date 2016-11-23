<?php
namespace T\Controller;
use Think\Controller;
use Lib\UserSession;
use Lib\Image;
use T\Model\ConcurModel;
use T\Model\UserOauth2ShareModel;
/**
 * 互助详情页
 */
class ConcurinfoController extends Controller{
    /**
     * 加载互助详情页面
     */
    public function index($id=0){
        //根据ID获取基础信息
        if(!$id){
            $this->error("禁止操作");
        }
        $url = SERVER_VISIT_URL.'/Tapi/Concur/endConcur';
        api($url,array('id'=>$id));   
        $r = D('Concur')->getConcurById(intval($id));
        if($r['status'] == 404 ){
            $this->error("项目还未发布");
        }
        if($r['status'] == 403){
            $this->error("项目未审核");
        }
        if($r['status'] == -1){
            $this->error("项目审核失败");
        }
        $t = $r['type']==1?'资源':'求助';
        $this->assign('title',$r['title']);
        $this->assign('htype',$r['type']);
        $this->assign('helpname',$t);
        $this->assign('recommend',$r['recommend']);
        if(!$r){
            $this->error("查找的".$t."不存在");
        }
        //判断是他人浏览还是自己浏览 $isown -1他人访问 1-自己访问
        $isown = $r['creator'] == UserSession::getUser('uid')?1:0; 
        //详情页类别 $type 1-服务详情，2-物资详情,3-求助详情 
        $type = $r['type']?($r['is_supplies']?2:1):3;
        $this->assign('image', Image::getUrl($r['image']));
        $province = D('Common/ProvinceCity')->getCity($r['provinceid']);
        $city = D('Common/ProvinceCity')->getCity($r['cityid']);
        $county = D('Common/ProvinceCity')->getCity($r['countyid']);
        $area = $province['class_name'].' '.$city['class_name'].' '.$county['class_name'].' '.$r['address'];
        $this->assign('area',$area);
        $label = D('Common/CategoryServer')->getLabelName($r['label']);
        if($r['status'] == 100){
            $statusName = '进行中';
        }elseif($r['status'] == 888){
            $statusName = '已结束';
        }else{
            $statusName = '已完成';
        }
        //所有入口全部结束提示已完成
        if($r['money']<=0 && $r['is_supplies']<=0 && $r['is_service']<=0){
            $statusName = '已完成';
        }
        $this->assign('checkUserAuth',D('User')->checkAuth());  //检测用户是否为认证用户
        $this->assign('statusName',$statusName);
      // $sharenum=D('UserOauth2Share')->shareCount(UserOauth2ShareModel::SHARE_TYPE_DONATE);
        $sharenum=D('UserOauth2Share')->where(" relation_type=6 and relation_id=".$id)->count();

        $this->assign('sharenum',$sharenum);
        //爱心动态申请捐助总人数
        $count=D("ConcurServiceApply")->donName($id,$tag=-2,$status=-2);
        $count=count($count);
        $pageCount=ceil($count/8);
        $this->assign("pageCount",$pageCount);
        $this->assign('label',$label);
        $this->assign('isown',$isown);
        $this->assign('type',$type);
        $this->assign('info',$r);
        $this->assign('id',$id);
        $this->display('index');
    } 
}
