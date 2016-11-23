<?php
namespace T\Controller;
use T\Model\ConcurModel;
use Think\Controller;
use Lib\UserSession;
use Lib\Image;
use T\Service\ConcurService;

/**
 * 发起及修改互助
 */
class ConcurController extends Controller {

    /**
     * 发起互助第一步页面加载
     */
    public function ConcurOne($type = 0, $id = 0) {
        tag('user_login');
        //判断是否为认证用户
        $user = D('User')->checkAuth();
        if(!$user){	
            $message = $type?'只有通过认证的用户才可以发布资源！':'只有通过认证的用户才可以发起求助！';
            $this->error($message);
        }
        //如果为更新，则获取互助内容
        if ($id) {
            //检测是否有权限操作
            $r = D('Concur')->checkIsOwnConcur($id);	    
            if(!$r['id'] && $r['status']==-1){
                $this->error($r['message']);
            }
            $type = $r['type'];
            $this->assign('concurInfo', $r);
        }
        $this->assign('id', $id);
        $title = "发起".($type ? '资源' : '求助');
        $this->assign('tag', $type ? '资源' : '求助');
        $this->assign('title',$title);
        $this->assign('type', $type); //0-表示为发起求助页面,1-我表示发布资源
        $this->display('concurOne');
    }

    /**
     * 发起或修改求助第一步数据入库
     * @param number $type 0-发布求助  1-发布资源
     * @param number $id concur表id
     */
    public function ConcurOneStorage($id = 0, $type = 0) {
        tag('user_login');
        tag('db_begin');
        $concur = D('Concur');
        //判断互助类型
        if($type !=0 && $type != 1){
            $this->error("互助类型错误");
        }
        //创建数据
        if ($concur->create()) {
            if ($id) {//修改
                //判断是否为自己创建的互助
                $id = intval(I('id'));
                $r = D('Concur')->checkIsOwnConcur($id);
                if(!$r['id'] && $r['status'] == -1) $this->error($r['message']);                     //判断是否为自己创建的互助
                if ($concur->save()) {                              //执行修改
                    $name = 'concur_' . $id;    //清空缓存
                    S($name, null);
                    $this->redirect('t/concur/concurTwo', array('id' => $id, 'type' => $type));
                } else {
                    $this->error("修改失败");
                }
            } else {//新添加
                $id = $concur->add();
                if ($id) {
                    if($type){
                        $this->redirect('t/concur/resourceTwo', array('id' => $id));    //加载我有资源页面
                    }else{
                        $this->redirect('t/concur/concurTwo', array('id' => $id));  //加载求助页面
                    }
                } else {
                    $this->error("添加失败");
                }
            }
        } else {
            $this->error($concur->getError());
        }
    }

    /**
     * 发起求助第二步页面加载
     * @param number $id concur表id
     */
    public function concurTwo($id = 0) {
        tag('user_login');
        $r = D('Concur')->checkIsOwnConcur($id);                          //验证是否为当前用户发的互助
        if(!$r['id'] && $r['status'] == -1)    $this->error($r['message']);
        //如果类型是资源，则跳转到资源页面
        if($r['type'] == 1){
            $this->redirect('t/concur/resourceTwo', array('id' => $id));  //加载求助页面
        }
        //互助物资资源
        if ($r['is_supplies']) {
            //获取物资和数量
            $sup = M('ConcurSupplies')->where("concur_id=%d", $id)->select();
            //获取地址信息
            $contactInfo = M('ConcurSuppliesAddress')->where("concur_id=%d", $id)->find();
            $this->assign('supplies', $sup);
            $this->assign('suparea', $contactInfo);
        }
        //互助服务
        if ($r['is_service']) {
            $this->assign('service', M('ConcurService')->where("concur_id=%d", $id)->find());
        }
        $this->assign('title', '发起求助');
        $this->assign('suppliessum',$sup?count($sup):0);
        $this->assign('cinfo', $r);
        $this->assign('id', $id);
        $this->display('concurTwo');
    }

    /**
     * 我有资源第二步页面加载
     * @param number $id concur表id
     */
    public function resourceTwo($id = 0) {
        tag('user_login');
        $r = D('Concur')->checkIsOwnConcur($id);                          //验证是否为当前用户发的互助
        if(!$r['id'] && $r['status'] == -1)    $this->error($r['message']);
        //如果类型是求助，则跳转到求助页面
        if($r['type'] == 0){
            $this->redirect('t/concur/concurTwo', array('id' => $id));  //加载求助页面
        }
        if($r['is_service']==1 && $r['is_supplies']==0){                             //获取服务内容
            $service = M('ConcurService')->where("concur_id=%d", $id)->find();
            $this->assign('service',$service);
        }elseif($r['is_supplies']==1 && $r['is_service']==0){                        //获取物资内容
            $supplies = M('ConcurSupplies')->where("concur_id=%d", $id)->select();
            $this->assign('supplies',$supplies);
        }
        $this->assign('title','发起资源');
        $this->assign('suppliessum',$supplies?count($supplies):0);
        $this->assign('id',$id);
        $this->display('resourceTwo');
    }
    
    /**
     * 互助第二步数据入库
     * @param number $id concur表id
     * @param array $need_chk 求助或提供的资源类型 1-money,2-物资,3-服务
     */
    public function concurTwoStorage($id,$need_chk) {      
        tag('user_login');
        tag('db_begin');    
        if(!$need_chk || !is_array($need_chk))  $this->error("最少选择一样互助类型");
        $need_standard = array(1,2,3);                                          //定义标准的类型数组
        if(array_diff($need_chk,$need_standard)){                               //判断类型是否合法
            $this->error("类型不合法");
        }
        $returnR = D('Concur')->checkIsOwnConcur($id);                          //验证是否为当前用户发的互助
        if(!$returnR['id'] && $returnR['status'] == -1)    $this->error($returnR['message']);
        //互助类型为资源时，判断是否只提供了一种类型的资源
        if($returnR['type'] == 1){
            if(count($need_chk) > 1)    $this->error("只能提供一种类型的资源");
            if($need_chk[0] == 1)       $this->error("此处没有捐助金钱的功能");
        }
        $concur = M('concur');
        $concur->startTrans();                                                  //开启事务
        $addConcur = $this->addConcur($id,$need_chk);                           //互助表数据添加修改
        $this->checkCall($addConcur,$concur);                                   //检测数据添加或修改是否成功，失败则回滚事务并结束操作
        //物资互助数据操作
        if(in_array(2,$need_chk)){
            $supplies = $this->supplies($id,$returnR['type']);                  //添加或修改物资相关信息
            $this->checkCall($supplies,$concur);                                //检测物资相关信息是否添加或修改成功，失败则回滚事务并结束操作
        }else{
            //没有提交物资互助，则删除此互助的物资互助信息
            M('ConcurSupplies')->where("concur_id=%d",$id)->delete();
            M('ConcurSuppliesAddress')->where(array('concur_id'=>$id,'user_id'=> UserSession::getUser('uid')))->delete();
        }
        //服务互助数据操作
        if(in_array(3,$need_chk)){
            $service = $this->service($id);                                     //添加或修改服务相关信息
            $this->checkCall($service,$concur);                                 //检测服务数据的修改或添加是否成功，失败则回滚事务并结束操作
        }else{//没有提交服务信息，则删除此互助的服务互助信息
            M('ConcurService')->where("concur_id=%d",$id)->delete();
        }
        $concur->commit();                                                      //提交事务
        $this->redirect("t/concur/concurThree", array('id' => $id));
    } 
    
    /**
     * 检查返回的结果，如果错误则回滚事务并提示错误信息
     */
    private function checkCall($data,&$concur){
        if($data['status'] == -1){
            $concur->rollback();
            $this->error($data['content']);
            die();
        }
    }
    
    /**
     * 互助表数据添加修改
     */
    private function addConcur($id,$need_chk){
        //判断是否有钱、物资、服务的互助
        $_d = array(
            'money'         => in_array(1,$need_chk)?intval(I('hp_money')):0,
            'is_supplies'   => in_array(2,$need_chk)?1:0,
            'is_service'    => in_array(3,$need_chk)?1:0,
            'update_date'   => time(),
        );
        $_w = array(
            'id'    => $id,
        );
        //将是否有钱，物资，服务等互助的信息添加进互助表中
        if(M('Concur')->where($_w)->save($_d)){
            return array('status'=>'1');
        }else{
            return array('status'=>'-1','content'=>'互助表信息添加失败');
        }
    }
    
    
    /**
     * 物资互助
     * @param number $id 互助信息ID
     * @param number $type 互助类型 0-求助 1-资源
     */
    private function supplies($id,$type){
        //物资及数目添加
        $suppliesAndNum = $this->addSuppliesAndNum($id);
        if($suppliesAndNum['status'] == -1){
            return $suppliesAndNum;
        }
        //在类型为求助时，添加物资详细地址信息
        if($type != 1){
            //物资详细地址信息添加
            $suppliesDetails = $this->suppliesDetails($id);
            if($suppliesDetails['status'] == -1){
                return $suppliesDetails;
            }
        }
    }
    
    /**
     * 物资及数目添加
     * @param number $id 互助信息ID
     * return array
     */
    private function addSuppliesAndNum($id){
        $_w = array(
            'concur_id' => $id,
        );
        M('ConcurSupplies')->where($_w)->delete();  //删除互助项目对应的物资及数目信息
        $supData = I('hp_cont');
        $numData = I('hp_num');
        if(count($supData)>10){
            return array('status'=>-1,'content'=>'每次最多提交10个物资项');
        }
        //循环获取物资名称及数目
        foreach($supData as $k=>$v){
            //判断物资数目是否正确
            if(!is_numeric($numData[$k]) || $numData[$k]<=0){
                return array('status'=>-1,'content'=>'物资数目不正确');
            }
            //判断物资名称是否为空
            if(empty($v)){
                return array('status'=>-1,'content'=>'物资名称不能为空');
            }
            //判断物资名称是否含有非法字符
            if(!unlawful($v)){
                return array('status'=>-1,'content'=>'物资名称不能含有非法字符');
            }
            //获取物资名称及数目
            $dataList[] = array(
                'concur_id' => $id,
                'name'      => $v,
                'num'       => $numData[$k],
            );
        }
        //添加物资名称及其数目
        if(M('ConcurSupplies')->addAll($dataList)){
            return array('status'=>1,'content'=>'success'); 
        }else{
            return array('status'=>-1,'content'=>'物资及数目添加失败'); 
        }        
    }
    
    /**
     * 物资详情添加
     */
    private function suppliesDetails($id){
        $concurSuppliesAddress = D('ConcurSuppliesAddress');
        //判断库中是否已经有该互助的信息
        $_w = array(
            'concur_id' => $id,
            'user_id'   => UserSession::getUser('uid'),
        );
        $result = $concurSuppliesAddress->where($_w)->find();
        //自动验证数据
        if($concurSuppliesAddress->create()){
            if($result){
                //修改数据,如果失败则返回
                if(!$concurSuppliesAddress->where("id=%d",$result['id'])->save()){
                    return array('status'=>-1,'content'=>'物资详情修改失败');
                }
            }else{
                //添加数据，如果失败则返回
                if(!$concurSuppliesAddress->add()){
                    return array('status'=>-1,'content'=>'物资详情添加失败');
                }
            }
            return array('status'=>1);
        }else{
            return array('status'=>-1,'content'=>$concurSuppliesAddress->getError());
        }
    }
    
    /**
     * 服务互助
     */
    private function service($id){
        //判断库中是否有该服务存在
        $_w = array(
            'concur_id'=>$id,
        );
        $result = M('ConcurService')->where($_w)->find();
        //获取并判断服务时间
        $stime = strtotime(I('act_btime'));
        $etime = strtotime(I('act_etime'));
        if(!$stime || !$etime){
            return array('status'=>'-1','content'=>'服务时间不能为空');
        }
        //获取需要添加或修改的数据
        $_d = array(
            'summary'       => $_POST['service_disc'],
            'concur_id'     => $id,
            'start_time'    => $stime,
            'end_time'      => $etime,
            'update_time'   => time(),
        );
        if(empty($_POST['service_disc'])){
            return array('status'=>-1,'content'=>'服务概述不能为空');
        }
        if(!contentUnlawful($_POST['service_disc'])){
            return array('status'=>-1,'content'=>'服务概述不能含有非法字符');
        }
        if($result){
            //修改服务项内容,如果失败则返回失败信息
            if(!M('ConcurService')->where("id=%d",$result['id'])->save($_d)){
                return array('status'=>-1,'content'=>'服务项修改失败');
            }
        }else{
            //增加服务项内容，如果失败则返回失败信息
            if(!M('ConcurService')->add($_d)){
                return array('status'=>-1,'content'=>'服务项添加失败');
            }
        }
        return array('status'=>1,'content'=>'serviceSuccess');
    }
    
    /**
     * 发起求助第三步页面加载
     */
    public function concurThree($id) {
        tag('user_login');
        //检测是否为当前用户发布
        $r =  D('Concur')->checkIsOwnConcur($id);
        if(!$r['id'] && $r['status'] == -1){
            $this->error($r['message']);
        }
        //已经有描述
        if($r['summary']){
            $summary = $r['summary'];
        }else{
            if($r['type']){
                $summary = '<h3 style="padding:10px 0px;color:#555;font-size:18px;">背景介绍</h3>
                            <p style="line-height:30px;color:#555;">请详细描述您所提供的资源信息，如物资的详细介绍、可提供服务的专业背景。图文并茂的形式，能让更多人了解到您的可靠与爱心。</p>
                            <h3 style="padding:10px 0px;color:#555;font-size:18px;">申请约束</h3>
                            <p style="line-height:30px;color:#555;">请详细描述对于申请人的约束条件，或可提供资源的约束条件。明确的约束条件可过滤掉部分约束条件之外的申请。</p>
                            <h3 style="padding:10px 0px;color:#555;font-size:18px;">联系方式</h3>
                            <p style="line-height:30px;color:#555;">加上详细的联系信息可以更好的实现资源对接~</p>';
            }else{
                $summary = '<h3 style="padding:0; margin:0; color:#555;font-size:18px;">求助背景</h3>
                            <p style="color:#555;margin:0px;padding:0px;">请详细论述求助背景及受助人情况、图文并茂、简洁生动。能够让更多的潜在捐助人选择支持您。</p>
                            <h3 style="padding:0; margin:0; color:#555;font-size:18px;">证明材料</h3>
                            <p style="color:#555;margin:0px;padding:0px;">请上传尽量多的、能够证明求助信息真实性的图文材料、不重复、有信度。</p>
                            <h3 style="padding:0; margin:0; color:#555;font-size:18px;">求助目标</h3>
                            <p style="color:#555;margin:0px;padding:0px;">如有需要您可以在此简述您的项目信息及概况。</p>
                            <h3 style="padding:0; margin:0; color:#555;font-size:18px;">求助内容要求</h3>
                            <p style="color:#555;margin:0px;padding:0px;">请详细描述您所需款项、物资或服务的大致要求、这能够帮助提高您的求助成功率。</p>
                            <h3 style="padding:0; margin:0; color:#555;font-size:18px;">联系方式</h3>
                            <p style="color:#555;margin:0px;padding:0px;">加上求助人信息更能体现求助的真实性哦。</p>';
            }
        }
        $this->assign('summary',$summary);
        $this->assign('type',$r['type']);
        $title = "发起".($r['type'] ? '资源' : '求助');
        $this->assign('title', $title);
        $this->assign('tag', $r['type'] ? '资源' : '求助');
        $this->assign('id', $id);
        $this->display('concurThree');
    }

    /**
     * 修改求助第三步数据入库
     */
    public function concurThreeStorage($id,$type) {
        tag('user_login');
        tag('db_begin');
        //判断是否为自己发布
        $r =  D('Concur')->checkIsOwnConcur($id);
        if(!$r['id'] && $r['status'] == -1){
            $this->error($r['message']);
        }
        //获取需要添加到互助表中的信息
        $_data = array(
            'id'            => $id,
            'status'        => ConcurModel::STATUS_WAITFORCHECK,
            'summary'       => $_POST['summary'],
            'update_date'   => time()
        );
        //添加数据
        if (M('Concur')->save($_data)) {
            $name = 'concur_' . $id;    //清空缓存
            S($name, null);
            $this->redirect('t/concur/userWait', array('id'=>$id,'type'=>$type));
        } else {
            $this->error(M('Concur')->getError());        
        }
    }
        
    
    /**
     * 提交成功提示页面
     */
    public function userWait($id,$type){
        $this->assign('uid',  UserSession::getUser('uid'));
        if(UserSession::getUser('type')==11){
            $typename = "个人";
        }elseif(UserSession::getUser('type')==21){
            $typename = '组织';
        }
        $this->assign('tag', $type ? '资源' : '求助');
        $this->assign('typename',$typename);
        $this->display('userWait');
    }

    
    /**
     * 互助发起者结束服务或资源互助
     * @param int type 1-结束服务  0-结束物资 2-结束money
     */
    public function end($id,$type=0){
        $data['update_date'] = time();
        if($type==1){
            $data['is_service'] = -1;
        }elseif($type==2){
            $data['money'] = -1;
        }else{
            $data['is_supplies'] = -1;
        }
        $map['creator'] = UserSession::getUser('uid');
        $map['id'] = $id;
        $result = M('Concur')->where($map)->save($data);
        $status = -1;
        $isend = 0; //是否所有全部都已经结束
        $message = '操作失败';
        if($result){
            $status = 1;
            $isend = $this->endConcur($id);
        } 
        $this->ajaxReturn(array('status'=>$status,'message'=>$message,'isend'=>$isend));   
    }
    
    /**
     * 在结束某个服务时，判断是否已经全部结束
     * @param $id concur表id
     */
    public function endConcur($id){
        //拒绝所有还没有通过请求的用户，拒绝理由：该互助项目已经结束
        $_wSupplies = array(
            'concur_id' => $id,
            'status'    => 0,
        );
        $_dSupplies['status'] = -1;
        $_dSupplies['reason'] = '该项目已经结束';
        M('concurSuppliesApply')->where($_wSupplies)->save($_dSupplies);
        //获取互助项目信息
        $_w = array(
            'creator'=> UserSession::getUser('uid'),
            'id'     => $id,
        );
        $result = M('Concur')->where($_w)->find();
        //判断concur是否已经全部结束
        if($result && $result['money']<=0 && $result['is_supplies']<=0 && $result['is_service']<=0 ){
            //给互助项目的状态修改成结束
            $_d['status'] = 888;
            M('Concur')->where("id=%d",$id)->save($_d);
            return 1;
        }else{
            return 0;
        }
    }   

}
