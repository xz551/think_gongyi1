<?php
namespace T\Controller;
use Think\Controller;
use Lib\UserSession;
use T\Model\ConcurModel;
/**
 * 资源管理类
 */
class SuppliesController extends Controller{
    /**
     * 申请捐助或求助资源
     */
    public function applySupplies($id){
        tag('user_login');
        tag('db_begin');
        //检测是否为认证用户，非认证用户不能进行捐助或求助申请
        $userIsAuth = $this->checkUserIsAuth();
        if($userIsAuth['status'] == -1){
            $this->ajaxReturn(array('status'=>-1,'content'=>$userIsAuth['content'],'msg'=>'1'));
        }
        //检测权限,是否为自己发布的，是否为正常状态
        $auth = $this->checkAllAuth($id);
	
        if($auth['status'] == -1){
            $this->ajaxReturn(array('status'=>-1,'content'=>$auth['content'],'msg'=>'2'));
            die();
        }
        $analysisData = $this->analysisData();                                  //将数据分成3部分
        $concur = M('ConcurSuppliesApply');
        $concur->startTrans();
        //物资申请表部分数据添加
        $supplies = $this->supplies($id,$analysisData['main'],I('subtype'));
        if($supplies['status'] == -1){
            $concur->rollback();
            $this->ajaxReturn(array('status'=>-1,'content'=>$supplies['content'],'msg'=>'3'));
            die();
        }else{
            $applyid = $supplies['id'];
        }
        $isupdate = $supplies['isupdate'];      //0-表示数据是新添加的  1-表示数据是更新的
        $concurInfo = D('Concur')->getConcurById($id);
        //物资地址表部分数据添加(需要地址信息的情况下)
        if($concurInfo['is_supplies'] == 1 && $concurInfo['type']==1){
            $suppliesAddress = $this->suppliesAddress($id,$applyid,$analysisData['address']);
            $this->checkDate($suppliesAddress,$concur); //判断操作是否成功，不成功则回滚并暂停
        }
        //物资数目部分数据添加
        $suppliesNumber = $this->suppliesNumber($id,$applyid,$analysisData['supplies'],$concurInfo['type']);
        $this->checkDate($suppliesNumber,$concur);      //判断操作是否成功，不成功则回滚并暂停
        //新增申请时添加用户申请记录
        if(!$isupdate)   {
            //$concurApply = $this->addConcurApply($id);
            $this->checkDate($concurApply,$concur);         //判断操作是否成功，不成功则回滚并暂停
        }      
        $this->addMessage($id,$isupdate);   //添加站内消息
        //数据入库
        $concur->commit();
        $this->ajaxReturn(array('status'=>1,'content'=>'success'));
    }
    
    /**
     * 判断数据操作结果,如果失败则回滚
     */
    private function checkDate($data,&$concur){
        if($data['status'] == -1){
            $concur->rollback();
            $this->ajaxReturn(array('status'=>-1,'content'=>$data['content']));
            die();
        }  
    }
    
    /**
     * 将提交的数据解析成三部分,
     * 1.主体请求部分 2.地址部分 3.请求的物资及数量部分
     */
    private function analysisData(){
        $arr = array();
        //主体请求部分数据
        $arr['main'] = intval(I('anonymous'));
        //物资地址
        $t = time();
        $arr['address'] = array(
            'provinceid'=>I('provinceid'),
            'cityid'=>I('cityid'),
            'countyid'=>I('countyid'),
            'address'=>I('address'),
            'name'=>I('name'),
            'phone'=>I('phone'),
            'code'=>I('code'),
            'update_time'=>$t,
        );
        //物资及数量， id 序列  I('suppliesnum')，数量序列 I('suppliesname')
        $arr['supplies']=$this->getSuppliesNumber(I('suppliesnum'),I('suppliesname'));
        return $arr; 
    }
    
    /**
     * 根据物资的id序列和数量序列获取物资的id及数量的键值对应数组
     */
    private function getSuppliesNumber($suppliesnum,$suppliesid){
        $suppliesnum = explode(',',$suppliesnum);
        $suppliesid = explode(',',$suppliesid);
        $suppliesList = array();
        foreach($suppliesid as $k=>$v){
            $suppliesList[$v] = $suppliesnum[$k];
        }
        return $suppliesList;
    }
    
    /**
     * 添加物资申请记录
     * @param number $id 互助表id
     * @param number $type 0-撤销 1-已经申请等待审核 -1审核没有通过 2审核通过
     */
    private function addConcurApply($id,$type=1){
        return;
        $_w = array(
            'concur_id' => $id,
            'userid'    => UserSession::getUser('uid'),
        );
        $concurApply = M('ConcurApply')->where($_w)->find();
        $r = false;
        if($concurApply){
            $_d['supplies'] = $type;
            $_d['updatetime'] = date('Y-m-d H:i:s');
            $r = M('ConcurApply')->where("id=%d",$concurApply['id'])->save($_d);
        }else{
            $_d = array(
                'supplies'  => $type,
                'concur_id' => $id,
                'userid'    => UserSession::getUser('uid'),
		'datetime'  => date('Y-m-d H:i:s'),
                'updatetime'=> date('Y-m-d H:i:s'),
            );
            $r = M('ConcurApply')->add($_d);
        }
        $status = $r?1:-1;
        $content = $r?'':$a;
        return array('status'=>$status,'content'=>$content);
    }
    
    /**
     * 添加站内消息
     * @param number $id concur表id
     * @param number $isupdate 0-新添加 1-更新
     */
    private function addMessage($id,$isupdate){
        //求助或资源的详细信息
        $info = D('Concur')->getConcurById($id);
        $arr=array(
                        'id'=>$id,
                        'title'=>$info['title'],
                        'type'=>$info['type']
        );
        //添加站内消息
        D("Notification")->sendMsg($info['creator'],'supplies_request',$arr,1); 
    }
    
    
    
    /**
     * 物资申请
     * @param number subtype 1-表示捐物资  2-表示修改物资
     */
    private function supplies($id,$data,$subtype){
        $data = $data?1:0;
        //查询申请是否已经存在
        $_w = array(
            'concur_id'=>$id,
            'user_id'=>  UserSession::getUser('uid'),
            'status' => array('neq',-2),
        );
        $apply = M('concurSuppliesApply')->where($_w)->find();
        $isupdate = 0;  //是否为更新数组，默认为0 0表示新添加，1-表示更新
        //物资申请表数据更新
        if($apply){
            //在更新的情况下，如果是捐助页提交过来的则提示申请已经提交
            if($subtype == 1){
                return array('status'=>-1,'content'=>'物资申请已经存在');
            }
            //更新物资申请
            $_d = array(
                'update_time' => time(),
                'anonymous'   => $data,
                'status'      => 0,
            );
            $result = M('concurSuppliesApply')->where("id=%d",$apply['id'])->save($_d);
            $content = $result?'':'物资申请更新失败';
            $applyid = $apply['id'];
            $status = $result?1:-1;
            $isupdate = 1;
        //物资申请表数据添加
        }else{
            $_d = array(
                'anonymous'=>$data,
                'concur_id'=>$id,
                'user_id'=>  UserSession::getUser('uid'),
                'date_time'=>time(),
                'update_time'=>time(),
            );
            $applyid = M('concurSuppliesApply')->add($_d);
            $content = $applyid?'':'物资申请添加失败';
            $status = $applyid?1:-1;
        }
        return array('status'=>$status,'content'=>$content,'id'=>$applyid,'isupdate'=>$isupdate);
    }
    /**
     * 物资地址
     */
    private function suppliesAddress($concur_id,$applyid,$data){
        $address = D('ConcurSuppliesAddress');
        $_w = array(
            'concur_id'=>$concur_id,
            'user_id'=>  UserSession::getUser('uid'),
        );
        $result = $address->where($_w)->find();
        $status = 1;
        $content = 'success';
        if($address->create($data)){
            $data['concur_id'] = $concur_id;
            $data['user_id'] = UserSession::getUser('uid');
            if($result){
                if(!$address->where('id=%d',$result['id'])->save($data)){
                    $status = -1;
                    $content = '物资地址更新失败';
                }
            }else{
                if(!$address->add($data)){
                    $status = -1;
                    $content = '物资地址添加失败';
                }
            }
        }else{
            $status = -1;
            $content = $address->getError();
        }
        return array('status'=>$status,'content'=>$content);
    }
    /**
     * 物资及物资数目
     */
    private function suppliesNumber($concur_id,$applyid,$data,$type=0){
        $result = M('ConcurSupplies')->where('concur_id=%d',$concur_id)->select();          //获取当前互助的物资id列表
        $sup = D('ConcurSupplies')->getSuppliesById($concur_id);                            //需要募捐或打算捐助的物资
        $preparedSupplies = D('ConcurSuppliesApply')->getAlreadySupplies($concur_id);       //已经捐出或已经募捐到的物资    
        $alsoNeed = $this->alsoNeed($sup,$preparedSupplies);                                //还需要募捐或还可以捐出的物资
        $list = array();
        foreach($result as $v){
            $list[] = $v['id'];
        }
        $tagName = $type?'捐助':'筹备';
        $tagN = $type?'申请':'捐助';
        //循环添加请求的物资数目
        foreach($data as $k=>$v){
            if(!is_numeric($v)){
                return array('status'=>-1,'content'=>"物资数必须为数字");
            }
            if($v<=0){
                return array('status'=>-1,'content'=>"物资数必须大于0");
            }
            //判断物资是否可以申请
            if($alsoNeed[$k]['num']<=0){
                return array('status'=>-1,'content'=>"{$alsoNeed[$k]['name']}已被全部{$tagName}完，不可再次{$tagN}");
            }
            //判断物资的申请数量是否大于提供的数量      
	    if($v>$alsoNeed[$k]['num']){
                return array('status'=>-1,'content'=>"{$alsoNeed[$k]['name']}的数量最多为{$alsoNeed[$k]['num']}");
            }
            if(!is_numeric($k)){
                return array('status'=>-1,'content'=>"申请的物资错误");
            //判断物资id是否属于当前的访问的互助
            }else{
                if(!in_array($k,$list)){
                    return array('status'=>-1,'content'=>"请求的物资不属于该互助");
                }
            }
            M('ConcurSuppliesApplyDetails')->where("apply_id=%d",$applyid)->delete();   //删除已有的物资及数目
            $_d[] = array(
                'apply_id'=>$applyid,
                'supplies_id'=>$k,
                'num'=>$v,
                'update_time'=>time(),
            ); 
        }
        //物资数目入库
        if(M('ConcurSuppliesApplyDetails')->addAll($_d)){
            return array('status'=>1);
        }else{
            return array('status'=>-1,'content'=>"物资数提交失败");
        }
    }
  
    /**
     * 获取已经申请的物资项目
     */
    public function getAlSupplies($id){
        $alSupplies = D('ConcurSuppliesApply')->getAlreadySupplies($id,UserSession::getUser('uid'),-2);
        $arr = array();
        $show = '';
        foreach($alSupplies as $k=>$v){
            $arr[$k]['suppliesid'] = $v['supplies_id'];
            $arr[$k]['num'] = $v['num'];
            $show .= $v['num'].' X '.$v['name'].'、';
        }
        $show = rtrim($show,'、');
        $ret['data'] = $arr;
        $ret['show'] = $show;
        $this->ajaxReturn($ret);
    }
    
    /**
     * 撤销捐助或求助
     */
    public function cancelSupplies($id){
       $_w = array(
            'concur_id'=>$id,
            'user_id'=>  UserSession::getUser('uid'),
            'status' => array('neq',-2),
        );
       //没有登录用户禁止操作
       if(!UserSession::getUser('uid')){
           $this->ajaxReturn(array('status'=>-1,'content'=>'禁止操作'));
           die();
       }
       //查询要撤销的申请的信息
       $result = M('ConcurSuppliesApply')->where($_w)->find();
       //申请已经撤销或者还没申请过
       if(!$result){
            $this->ajaxReturn(array('status'=>-1,'content'=>'已撤销或还没申请'));
            die();
       }
       if($result['status'] == 1){
           $this->ajaxReturn(array('status'=>-1,'content'=>'已经通过互助不可撤销'));
           die();
       }
       $_d = array(
           'status'=> -2,
           'update_time'=>time(),
       );
       if(M('ConcurSuppliesApply')->where('id=%d',$result['id'])->save($_d)){
           $status = 1;
           $content = 'success';
       }else{
           $status =-1;
           $content = '操作失败';
       }
       //$this->addConcurApply($id,0);  //清除申请记录
       $this->ajaxReturn(array('status'=>$status,'content'=>$content));
    }
    
    
    /**
     * ajax返回物资申请或物资修改页
     * @param number $id 互助id
     * @param json $showneed 已经请求的物资及数量
     * @param number $type 类型，1-无物资收货地址等信息，2-有物资收货地址等信息
     *  @param number subtype 1-表示捐物资  2-表示修改物资
     */
    public function suppliesAjaxPage($id,$type=1,$subtype){
        layout(false);
        tag('user_login');
        tag('db_begin');
        //检测物资申请权限
        $auth = $this->checkAllAuth($id);
        if($auth['status'] == -1){
            echo $auth['content'];
            die();
        }
        $sup = D('ConcurSupplies')->getSuppliesById($id);                       //需要募捐或打算捐助的物资
        $preparedSupplies = D('ConcurSuppliesApply')->getAlreadySupplies($id);  //已经捐出或已经募捐到的物资    
        $alsoNeed = $this->alsoNeed($sup,$preparedSupplies);                    //还需要募捐或还可以捐出的物资
        $request = $this->isRequest($id);                                       //查询当前用户是否已经提交请求 
        //已经提交请求的获取已经提交的数值
        if($request){
            $this->assign('anonymous',$request['anonymous']);
            if($request['status'] != 0 && $request['status'] != -1){
                echo '该请求已经处理过，不能再次提交';
                die();
            }
            $alreadyApply = M('ConcurSuppliesApplyDetails')->where('apply_id = %d',$request['id'])->select();   //已经申请的物资信息
            $application = $this->application($alsoNeed,$alreadyApply);         //获取物资是否已申请的信息，已申请返回已申请的物资数，未申请的返回需求的申请数
            //已经提交的物资收取地址信息
            if($type == 2){
                $_w = array(
                    'concur_id'=>$id,
                    'user_id'=>  UserSession::getUser('uid')
                );
                $address = M('ConcurSuppliesAddress')->where($_w)->find();
                $this->assign('suppliesAddress',$address);
            }
        }
        $show = $application?$application:$alsoNeed;
        $this->assign('showneedInfo', $show);
        $this->assign('id',$id);
        $this->assign('subtype',$subtype);
        $this->assign('suppliesType',$type);
        if($type == 1){
            $this->assign('isaddress',0);
            $this->noAddressApply($id);
        }elseif($type == 2){
            $this->assign('isaddress',1);
            $this->containAddressApply($id);
        }
    }
    
    /**
     * 通用的权限检测，检测互助服务是否为正常发布状态，及是否申请了自己发布的服务
     * @param number $tag 是否检测已经申请过
     */
    private function checkAllAuth($cid){    
        //判断是否为空
        if(!$cid){
            return array('status'=>-1,'content'=>'禁止操作');
        }
        $concur = D('Concur')->getConcurById($cid);  //获取互助信息
        //判断是否申请了自己的项目
        if($concur['creator'] == UserSession::getUser('uid')){
            return array('status'=>-1,'content'=>'不能申请自己的服务');
        }
        //判断是否为正常发布状态
        if($concur['status'] != ConcurModel::STATUS_NORMAL){
           return array('status'=>-1,'content'=>'该服务不是正常的发布状态');
        }
        //判断服务是否已过期
        if($concur['end_time'] <= time()){
           return array('status'=>-1,'content'=>'该服务已经过期');
        }
        //判断是否已经申请
        /*
        if(!$tag){
            $_w = array(
                'concur_id' => $cid,
                'user_id'    => UserSession::getUser('uid'),
                'status'     => array('in','0,1'),
            );
            if(D("ConcurSuppliesApply")->where($_w)->count()){
                return array('status'=>-1,'content'=>'不可重复申请');
            }
        }
        */    
    }
    
    /**
     * 获取物资是否已申请的信息，已申请返回已申请的物资数，未申请的返回需求的申请数
     * @param array $alsoNeed       还需要募捐或还可以捐出的物资
     * @param array $alreadyApply   已经申请的物资信息
     * return array
     */
    private function application($alsoNeed,$alreadyApply){
        if(!$alreadyApply){
            return $alsoNeed;
        }
        $arr = array();
        //整理已经申请的求助或救助物资信息
        foreach($alreadyApply as $k=>$v){
            $arr[$v['supplies_id']] = $v;
        }
        $application = array();
        foreach($alsoNeed as $k=>$v){
            if(empty($arr[$v['id']])){       
                $application[$v['id']] = $v;
                $application[$v['id']]['isapply'] = 0;
            }else{
               $application[$v['id']] = $arr[$v['id']];
               $application[$v['id']]['isapply'] = 1;
               $application[$v['id']]['name'] = $v['name'];
            }
	    $application[$v['id']]['id'] = $v['id'];
        }
        return $application;
    }
    
    
    
    /**
     * 还需要募捐或求助的物资
     * @param array $sup 需要募捐或打算求助的物资
     * @param array $preparedSupplies   已经捐出或已经募捐到的物资
     * return array
     */
    private function alsoNeed($sup,$preparedSupplies){
        $ret = array();
        $arr = array();
        if($preparedSupplies){
            foreach($preparedSupplies as $v){
                if($arr[$v['id']]){
                    $arr[$v['id']]['num'] += $v['num'];
                }else{
                    $arr[$v['id']] = $v; 
                }
            }
        }
        foreach($sup as $k=>$v){
            $surplus = $arr[$v['id']]['num']?$arr[$v['id']]['num']:0;
            $num = $v['num'] - $surplus;
            $num = $num<0?0:$num;
            $sup[$k]['num'] = $num;
            $ret[$v['id']] = $sup[$k];
        }
        return $ret;
    }
    
    /**
     * 查询用户是否已经提交过物资求助或救助申请
     */
    private function isRequest($id){
        //根据互助id,查询是否有物资捐助或求助的请求
        $_w['concur_id'] = $id;
        $_w['user_id'] = UserSession::getUser('uid');
        $_w['status'] = array('neq',-2);
        $cr = M('ConcurSuppliesApply')->where($_w)->find();
        return $cr;
    }
   
    /**
     * 检测用户是否为认证用户
     */
    private function checkUserIsAuth(){
        if(!D('User')->checkAuth()){
            return array('status'=>-1,'content'=>'非认证用户不能进行操作');
        }
    }
    
    
    /**
     * 无物资收货地址等相关请求页面
     */
    private function noAddressApply($id){
        $this->display('noAddressApply');
    }
    
    /**
     * 获取物资地址收取等相关信息
     */
    private function containAddressApply($id){
        $this->display('containAddressApply');    
    }
    
    public function getSuppliesApplyNumber($id){
        //判断查询的是否为自己发布的项目
        $_w = array(
            'id'=>$id,
            'creator'=>  UserSession::getUser('uid'),
        );
        if(!M('Concur')->where($_w)->count()){
            $this->ajaxReturn(array('status'=>1,'message'=>'操作失败'));
        }
        //还没有通过审核的请求数目
        $_applyW = array(
            'concur_id' => $id,
            'status'    => 0,
        );
        $sum = M('ConcurSuppliesApply')->where($_applyW)->count();
        $this->ajaxReturn(array('status'=>1,'sum'=>$sum));       
    }
    
}
