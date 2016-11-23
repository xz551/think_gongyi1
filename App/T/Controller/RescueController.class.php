<?php
namespace T\Controller;
use Think\Controller;
use Lib\UserSession;
use T\Model\ConcurModel;
use Lib\Image;
/**
 * 用户的求助捐助等信息
 */
class RescueController extends Controller {
    
    /**
     * ajax返回用户的求助捐助等信息
     * @param number $uid 用户id
     * @param number $type 互助的类型 1-发起的求助，2-捐助的求助 3-认证的求助 4-提供的资源 5-申请的资源
     * @param number $donateType  捐助类型 1-全部，2-求物资，3-求服务 4-求款项 
     * @param number $page 访问的页数
     * @param number $pagesize 每页的记录数目
     */
    public function concur($uid,$type,$donateType,$page=1,$pagesize=10){
        layout(false);
        //查询用户是否存在
        
        //判断是自己访问还是其他人访问
        $ismine = UserSession::getUser('uid')==$uid?1:0;
        
        $this->assign('ismine',$ismine);
        //发起的求助和提供的资源
        if($type==1 || $type==4){
            $this->initiative($uid,$type,$donateType,$page,$pagesize);
        //捐助的求助和申请的资源    
        }elseif($type == 2 || $type == 5){
            $this->request($uid,$type,$donateType,$page,$pagesize);
        //认证的求助    
        }elseif($type == 3){
            $this->certified($uid,$type,$donateType,$page,$pagesize);
        }else{
            $this->ajaxReturn(array('status'=>-1,'content'=>'提交的互助的类型不正确'));
        }
    }
    
    /**
     * 主动发起的求助或资源页面
     * @param number $type 互助的类型 1-发起的求助，2-捐助的求助 3-认证的求助 4-提供的资源 5-申请的资源
     * @param number $donateType  捐助类型 1-全部，2-求物资，3-求服务 4-求款项 
     */
    private function initiative($uid,$type,$donateType,$page,$pagesize){
        //获取互助的类型并加入到查询条件中，1-提供资源 0-发起求助
        $_w = array(
            'type'=> $type==4?1:0,
        );
        //获取捐助的类型并加入到查询条件中，1-全部，2-求物资，3-求服务 4-求款项 
        switch ($donateType) {
            case 2:
                $_w['is_supplies'] = array('neq',0);
                break;
            case 3:
                $_w['is_service'] = array('neq',0);
                break;
            case 4:
                $_w['money'] = array('neq',0);
                break;
        }
        $start = ($page-1)*$pagesize;
        $sum =  M('Concur')->where($_w)->count();                               //获取总数
        $result = M('Concur')->where($_w)->limit($start,$pagesize)->select();   //获取满足条件的记录
        $result = $this->getImage($result,'image',120,120);                     //获取图片的URL地址
        $this->assign('infolist',$result);
        $this->assign("page",page_new($sum,$pagesize));
        $this->display('initiative');
    }
    
    /**
     * 申请提供的资源或申请的资源
     * @param number $type 互助的类型 1-发起的求助，2-捐助的求助 3-认证的求助 4-提供的资源 5-申请的资源
     * @param number $donateType  捐助类型 1-全部，2-求物资，3-求服务 4-求款项 
     */
    private function request($uid,$type,$donateType,$page,$pagesize){   
        $result = $this->sqlrequest($uid,$donateType,$page,$pagesize);   //获取用户申请的求助或捐助信息
        //获取物资信息
        if($type == 1 || $type==2){
            $info = $this->getSuppliesInfo($result['data']);
        }
        $this->assign('infolist',$info);
        $this->assign("page",page_new($result['num'],$pagesize));
        $this->display('request');
    }
    
   
      
    /**
     * 获取物资信息
     * @param array $data 用户申请的互助信息及申请信息
     */
    private function getSuppliesInfo($data){
        $supplies = '';
	foreach($data as $k=>$v){
            $data[$k]['image'] = Image::getUrl($v['image'], array(120,120),'default');
            if($v['suppliesid']){
                $supplies .= $v['suppliesid'].',';
            }
        }
        $supplies = rtrim($supplies,',');
        $info = D('ConcurSupplies')->getInfoByIdList($supplies);    //获取物资信息
        //将物资信息添加进数据
        foreach($data as $k=>$v){
            if($v['suppliesid']){
                $data[$k]['suppliesinfo'] = $info[$v['suppliesid']];
            }  
        }
        return $data;
    }
    
    /**
     * 认证的求助
     * @param number $donateType  捐助类型 1-全部，2-求物资，3-求服务 4-求款项 
     */
    private function certified($uid,$type,$donateType,$page,$pagesize){
        $_w = array(
            'userid'=>$uid,
        );
        $result = M('ConcurVerify')->where($_w)->select();  //查询用户所有认证的信息
        $idlist = array();
        foreach($result as $v){
            $idlist[] = $v['concur_id'].',';
        }
        $_m = array(
            'id'=>array('in'=>$idlist),
        );
         //获取捐助的类型并加入到查询条件中，1-全部，2-求物资，3-求服务 4-求款项 
        switch ($donateType) {
            case 2:
                $_m['is_supplies'] = array('neq',0);
                break;
            case 3:
                $_m['is_service'] = array('neq',0);
                break;
        }
        $start = ($page-1)*$pagesize;
        $num = M('Concur')->where($_m)->count();
        $info = M('Concur')->where($_m)->limit($start,$pagesize)->select();
        $info = $this->getImage($info,'image',120,120,'default');
        $this->assign("page",page_new($num,$pagesize));
        $this->assign('info',$info);
        $this->display('certified');
    }
    
    /**
     * 根据结果获取图片地址
     * @param array $info 数据库中查询的结果
     * @param string $field 图片的字段名
     * @param number $w 返回图片的宽
     * @param number $h 返回图片的高
     * @param string $default 找不到图片时候的默认图片名
     * return array
     */
    private function getImage($info,$field='image',$w=0,$h=0,$default='default'){
        $_wh = (!$w && !$h)?array(200,200):array($w,$h);
        foreach($info as $k=>$v){
            $info[$k][$field] = Image::getUrl($v->$field, $_wh,$default);
        }
        return $info;
    }
    
    /**
     * 获取用户的物资、服务等互助申请信息
     * @param number $uid 用户id
     * @param number $donateType  捐助类型 1-全部，2-求物资，3-求服务 4-求款项 
     * @param number $page 访问第几页
     * @param number $pagesize 每页的记录数目
     * return array
     * 
     * ps:暂未使用缓存，此处需要考虑使用缓存
     */
    public function sqlrequest($uid,$donateType,$page,$pagesize){
	$prefix =  C('DB_PREFIX');
        $concurTab = $prefix.'concur';
        $suppliesTab = $prefix.'concur_supplies_apply';
        $serviceTab = $prefix.'concur_service_apply';
        $serviceInfoTab = $prefix.'concur_service';
        $where = '';
        $join = '';
        $field = "$concurTab.id as cid, $concurTab.title,$concurTab.image,$concurTab.summary,$concurTab.status as cstatus,$concurTab.type as ctype,$concurTab.creator, ";
         switch ($donateType) {
            case 1:
                $field .= " $suppliesTab.id as suppliesid,$suppliesTab.user_id as suppliesuid,$suppliesTab.status as suppliesstatus,$suppliesTab.date_time as suppliestime, ";
                $field .= " $serviceTab.id as serviceid,$serviceTab.apply_uid as serviceuid,$serviceTab.status as servicestatus,$serviceTab.datetime as servicetime, ";
                $field .= " $serviceInfoTab.summary as content ";
                $where = " (($suppliesTab.user_id =$uid  && $serviceTab.apply_uid = $uid) || ($suppliesTab.user_id =$uid  && $serviceTab.apply_uid is null) || ($suppliesTab.user_id is null && $serviceTab.apply_uid = $uid))  && ($suppliesTab.status != -2 || $suppliesTab.status is null) && ($serviceTab.status != -2 || $serviceTab.status is null)";
                $join = " left join $suppliesTab  on $suppliesTab.concur_id=$concurTab.id left join $serviceTab  on $serviceTab.concur_id=$concurTab.id "
                        . "left join  $serviceInfoTab on $serviceTab.concur_id=$serviceInfoTab.concur_id";
                break;
            case 2:
               $field .= " $suppliesTab.id as suppliesid, $suppliesTab.user_id as suppliesuid,$suppliesTab.status as suppliesstatus,$suppliesTab.date_time as suppliestime ";
                $where = " $suppliesTab.user_id =$uid && $suppliesTab.status!=-2";
                $join = " left join $suppliesTab  on $suppliesTab.concur_id=$concurTab.id ";
                break;
            case 3:
                $field .= " $serviceTab.id as serviceid,$serviceTab.apply_uid as serviceuid,$serviceTab.status as servicestatus,$serviceTab.datetime as servicetime,$serviceInfoTab.summary as content ";
                $where = " $serviceTab.apply_uid = $uid && $serviceTab.status != -2";
                $join = " left join $serviceTab  on $serviceTab.concur_id=$concurTab.id "
                        . " left join  $serviceInfoTab on $serviceTab.concur_id=$serviceInfoTab.concur_id";
                break;
            case 4:
                return false;
        }
        $start = ($page-1)*$pagesize;
        $sql1 = "select $field from $concurTab $join where $where limit $start,$pagesize";
        $sql2 = "select count(*) as num  from $concurTab $join where $where";
        $result['data'] = $this->query($sql1);
        $result['num'] = $this->query($sql2)[0]['num'];
        return $result;
    }
    
}