<?php
namespace T\Widget;
use Lib\City;
use Lib\Helper;
use Lib\UserSession;
use T\Service\ConcurFeedbackService;
class ConcurWidget extends WController {
    /**
     * 获取类别标签模块
     */
    public function label($list = array()) {
        //如果为修改信息则选中默认标签
        if ($list) {
            $this->assign('slist', $list);
        }
        $label = D('Common/CategoryServer')->getAllLabel();
        $this->assign('list', $label);
        $this->display("Widget/Concur:label");
    }

    /**
     * 获取地点标签模块
     */
    public function area($provinceid = '', $cityid = '', $countyid) {
        //如果为修改信息则默认选中地址信息 
        if (S(C('areaList'))) {
            $area = S(C('areaList'));
        } else {
            $area = M('ProvinceCity')->where("class_type=1")->select();
            S(C('areaList'), $area, C('areaTime'));
        }
        $this->assign('areaList', $area);
        $this->assign('provinceid', $provinceid);
        $this->assign('cityid', $cityid);
        $this->assign('countyid', $countyid);
        $this->display("Widget/Concur:area");
    }

    /**
     * 获取时间标签模块
     * @param number $timeShowType 时间显示格式 0-年月日 1-年月日时分秒
     */
    public function time($start = '', $end = '', $auto = 1,$timeShowType=0) {
        //如果为修改信息则默认填充时间信息
        if($timeShowType && $start && $end){
                $this->assign('stime', date('Y-m-d H:i:s', $start));
                $this->assign('etime', date('Y-m-d H:i:s', $end));
        }elseif ($start && $end) {
                $this->assign('stime', date('Y-m-d', $start));
                $this->assign('etime', date('Y-m-d', $end));
        }
        $this->assign('timeShowType',$timeShowType);
        $this->auto = $auto;
        $this->display("Widget/Concur:time");
    }

    /**
     * 获取图片标签模块
     */
    public function image($src = '') {
        //如果为修改信息，则获取默认图片信息
        if ($src) {
            $this->assign('imgsrc', \Lib\Image::getUrl($src, array(100, 100), 'concur'));
            $this->assign('imgname', $src);
        }
        $this->display("Widget/Concur:image");
    }

    /**
     * 提示信息
     * @param type $type 0表示求助 1-表示提供资源
     */
    public function prompt($type = 0) {
        if ($type == 1) {
            $this->display("Widget/Concur:hprompt");
        } else {
            $this->display("Widget/Concur:sprompt");
        }
    }

    /**
     * 获取互助的具体内容
     * @param $id 互助id
     * @param $service 0-没有服务的捐助或求助  1-有服务的捐助或求助
     * @param $supplies  0-没有物资的捐助或求助  1-有物资的捐助或求助
     * @param $money    0-没有钱的求助   大于0-求助的钱数
     * @param $type 0-求助 1-捐助
     * @param $isown 是否为自己访问 1-自己访问  0-他人访问
     */
    public function concurInfo($id, $service = 0, $supplies = 0, $money = 0, $type = 0, $isown = 0) {
        $isLogin = UserSession::getUser('uid')?1:0;
        $this->assign('islogin',$isLogin);
        $this->assign('supplies',$supplies);
        $this->assign('money',$money);
        $this->assign('service',$service);
        if ($type) {//捐助
            if ($service) {   //捐助服务
                $this->service($id, $isown,$service);
            } else {  //捐助资源
                $this->supplies($id, $isown,$supplies);
            }
        } else {  //求助
            $this->help($id, $service, $supplies, $money, $isown);
        }
    }

    /**
     * 捐助服务详情
     */
    private function service($id, $isown,$service) {
        $this->serviceInfo($id, $isown,$service);
        $this->display("Widget/Concur:serviceInfo");
    }
    
    
     /**
     * 服务求助详情
     */
    private function serviceInfo($id, $isown,$service){
        //查询服务详情
        $_w['concur_id'] = $id;
        $r = M('ConcurService')->where($_w)->find();
        $sid = $r['id'];    //话题id
        $this->assign('serviceInfo', $this->getService($id));
        $this->assign('isown', $isown);   
        //查询用户是否已经提交请求
        $_m = array(
            'service_id'=> $sid,
            'concur_id' => $id,
            'status'    => array('neq',-2),
            'apply_uid' => UserSession::getUser('uid'),
        );
        $sr = M('ConcurServiceApply')->where($_m)->find();
        //还没有通过审核的请求数目
        $_applyW = array(
            'service_id'=> $sid,
            'concur_id' => $id,
            'status'    => 0,
        );
        $sum = M('ConcurServiceApply')->where($_applyW)->find();
        $this->assign('applicationNumber',$sum);
        
        //判断用户的申请状态
        $this->assign('serviceApplyStatus',$this->applyStatus($service,$sr,$id)); 
        $this->assign('subject',$sr);
        $this->assign('id', $id);
        $this->assign('sid',$sid);
        $this->assign('serviceReason',$sr['reason']);
    }
    
    
    
    /**
     * 获取申请的资源
     * @param type int $type 1-则表示获取已经接受的申请 0-表示请求状态的  -1表示拒绝的  -2表示全部
     */
    private function getAlreadySuppliesShow($cr='',$id,$type=-2){
        if ($cr) {  
            $alSupplies = D('ConcurSuppliesApply')->getAlreadySupplies($id,  UserSession::getUser('uid'), $type);
            $alSuppliesShow = '';
            foreach ($alSupplies as $v) {
                $alSuppliesShow .= $v['num'] . ' x ' . $v['name'] . '、';
            }
            return $alSuppliesShow = rtrim($alSuppliesShow, '、');
        }else{
            return null;
        }   
    }
    
    /**
     * 根据互助id获取申请物资的请求或提供物资的请求
     * @param number $id 互助表id
     * return array
     */
    private function getSupRequestInfo($id){
        $_w['concur_id'] = $id;
        $_w['user_id'] = UserSession::getUser('uid');
        $_w['status'] = array('neq', -2);
        return M('ConcurSuppliesApply')->where($_w)->find();
    } 
    
    
    /**
     * 捐助物资详情
     * @param number $id 互助表id
     * @param number $isown 是否为互助信息发布者自己访问 1-自己访问 0-其他人访问
     * @param number $supplies       捐助物质状态 0-没有捐助 1-有捐助  -1发布者结束了该捐助
     */
    private function supplies($id, $isown,$supplies) { 
	//获取物资的相关捐助或求助信息
        $this->getSuppliesAllInfo($id, $isown,$supplies);
        //查询地址   
        $this->assign('address', $this->getAddress($id));
        $this->assign('isown', $isown);
        $this->assign('id', $id);
        $this->display("Widget/Concur:suppliesInfo");
    }
    
    /**
     * 获取物资的相关捐助或求助信息
     */
    private function getSuppliesAllInfo($id, $isown,$supplies){
        $cr = $this->getSupRequestInfo($id);                                            //互助信息的 物资申请请求或物资提供请求
        $this->assign('suppliesReason',$cr['reason']);
        if (!$isown) {//其他人访问
             $this->assign('alSuppliesShow', $this->getAlreadySuppliesShow($cr,$id));   //互助信息的浏览者已经申请的物资(直接显示的格式)
             $endSuppliesShow = $this->getAlreadySuppliesShow($cr,$id,1);
             $this->assign('endSuppliesShow', $endSuppliesShow);                        //已经申请并已通过的物资请求
             //获取物流信息
             if($endSuppliesShow){
	     	$logisticsInfo = json_decode($cr['content']);
		foreach($logisticsInfo->data as $k=>$v){
			$logisticsarr[$k]['time'] = $v->time;
			$logisticsarr[$k]['context'] = $v->context;	
		}	
                $this->assign('logisticsInfo',array_reverse($logisticsarr)); 
                //物流方式和运单编号
                $this->assign("logisticsNumber",explode(',',$cr['logistics_number']));
             }
        }   
        $sup = D('ConcurSupplies')->getSuppliesById($id);                       //需要募捐或打算捐助的物资
        $preparedSupplies = D('ConcurSuppliesApply')->getAlreadySupplies($id);  //已经捐出或已经募捐到的物资
        $alShow = $this->also($preparedSupplies);                               //已经捐出或已经募捐到的物资的页面直接显示格式
        $this->assign('alShow', $alShow);
        $needSupplies = $this->surplusShow($sup, $preparedSupplies);            //还需要或还能捐出的物资的显示 string 格式
        $this->assign('needSupplies', $needSupplies);   
        $need = $this->surplus($sup,$preparedSupplies);                         //还需要或还能捐出的物资 array 格式
        $showneed = $need ? $need : $sup;
        $this->assign('showneed', $showneed);
        //判断用户的申请状态
        $this->assign('applyStatus',$this->applyStatus($supplies,$cr,$id));
    }
    
    /**
     * 获取用户的物资申请状态
     * @param array $supplies   打算捐助或需要募捐的物资信息
     * @param array $cr         申请物资或提供物资的请求信息
     * return array
     */
    private function applyStatus($supplies,$cr,$id){
        $applyStatus = ($supplies==-1)?0:1;
        if($applyStatus){
            if(!$cr){
                $applyStatus = 1;
            }elseif($cr['status'] == 0){ //请求状态
                $applyStatus = 2;
            }elseif($cr['status'] == -1){//拒绝状态
                $applyStatus = 4;
            }elseif($cr['status'] == 1){//接受状态
                $applyStatus = 3;
            }
        }
        //判断服务是否已经结束
        $result = M('concur')->where("id=%d",$id)->find();
        if($result['status'] == 888){
            $applyStatus = 0;
        }
        return $applyStatus;
    }
    

    /**
     * 获取物流信息
     */
    private function getLogistics($cr){
        $content = json_decode($cr['content']);
        $content = $content->data;
        $con = array();
        foreach($content as $k=>$v){
            $con[$k]['time'] = $v->time;
            $con[$k]['context'] = $v->context;
        }
        return array_reverse($con);
    }
    
    
    /**
     * 求助详情
     * @param $id 互助id
     * @param $service 0-没有服务的捐助或求助  1-有服务的捐助或求助 -1 -服务或捐助已结束
     * @param $supplies  0-没有物资的捐助或求助  1-有物资的捐助或求助   -1 -物资捐助或求助已结束 
     * @param $money    0-没有钱的求助   大于0-求助的钱数   -1 钱的求助已结束
     * @param $isown 是否为自己访问 1-自己访问  0-他人访问
     */
    private function help($id, $service, $supplies, $money, $isown) {
        //查询求助服务详情
        if ($service == 1 || $service == -1) {
            $this->serviceInfo($id, $isown,$service);
        }

        //查询求助物资详情
        if ($supplies == 1 || $supplies == -1) {
             //获取物资的相关捐助或求助信息
            $this->getSuppliesAllInfo($id, $isown,$supplies);
            $this->assign('address', $this->getAddress($id));
        }
        $this->display("Widget/Concur:concurInfo");
    }
    /**
     * 还需要或还能捐出的物资
     */
    private function surplus($sup,$preparedSupplies) {
        $need = array();
        foreach($preparedSupplies as $k=>$v){
            $als[$v['id']]['num'] += $v['num'];
        }
        foreach ($sup as $v) {
            $need[$v['id']]['id'] = $v['id'];
            $need[$v['id']]['num'] = $v['num'] - $als[$v['id']]['num'];
            $need[$v['id']]['name'] = $v['name'];
        }
        return $need;
    }

    /**
     * 还需要或还能捐出的物资的显示
     */
    private function surplusShow($sup, $preparedSupplies) {
        $needSupplies = '';
        if ($preparedSupplies) {
            $als = array();
            foreach($preparedSupplies as $k=>$v){
                $als[$v['id']]['num'] += $v['num'];
                $als[$v['id']]['name'] = $v['name'];
                $als[$v['id']]['id'] = $v['id'];
            }
            foreach ($sup as $v) {
                $number = $v['num'] - $als[$v['id']]['num'];
                if($number>0){
                    $needSupplies .= $number . ' X ' . $v['name'] . '、';
                }
            }
        } else {
            foreach ($sup as $v) {
                $needSupplies .= $v['num'] . ' X ' . $v['name'] . '、';
            }
        }
        $needSupplies = rtrim($needSupplies, '、');
        return $needSupplies;
    }

    /**
     * 已经捐出或已经募捐到的物资 页面直接显示格式
     * @param array $preparedSupplies   已经捐出或已经募捐到的物资
     */
    private function also($preparedSupplies) {
        //整理好的已筹集的物资数据
        $als = array();
        foreach ($preparedSupplies as $k => $v) {
            $als[$v['id']]['num'] += $v['num'];
            $als[$v['id']]['name'] = $v['name'];
            $als[$v['id']]['id'] = $v['id'];
        }
        $alShow = '';   //已筹集的物资在页面的显示数据
        foreach ($als as $v) {
            $alShow .= $v['num'] . ' x ' . $v['name'] . '、';
        }
        $alShow = rtrim($alShow, '、');
        return $alShow;
    }

    /**
     * 查询服务
     */
    private function getService($id) {
        return M('ConcurService')->where("concur_id=%d", $id)->find();
    }

    /**
     * 查询地址
     */
    private function getAddress($id) {
        $_w['concur_id'] = $id;
        return M('ConcurSuppliesAddress')->where($_w)->find();
    }

    /**
     * 爱心认证
     * 只有求助 才有爱心认证
     */
    public function iLove($type){
        if($type!=0){
            return;
        }
        $concur_id=$_GET['id'];
        $this->concur_id=$concur_id;
        //检查我是否认证了这个信息
        if(UserSession::isLogin()){
            $c=M('concur_verify')->where('concur_id=%d and userid=%d',$concur_id,UserSession::getUserId())->count();
            $this->is_curr_user=$c>0;
        }
        $this->display('Widget:Concur/ilove');
    }

    /**
     * 取出反馈信息
     */
    public function feedback($creator){
        $id=$_GET['id'];
        $show_form=ConcurFeedbackService::exists_user($creator,$id);
        //取得反馈信息列表
        $this->uid=UserSession::getUserId();
        $this->concur_id=$id;
        $this->show_form=$show_form;
        $this->display('Widget:Concur/feedback');
    }

    /**
     * 互助 求助 发布资源者的信息
     */
    public function creator($uid){
    	$user=json_decode(api("/User/getById",array('uid'=>$uid)));
    	if($user->type==11){
    		//个人getById($uid)
    		$user_obj=json_decode(api("/Volunteer/getById",array('uid'=>$uid)));
    		$real_name=$user_obj->real_name;
    		$phone=$user_obj->vol_phone;
    	}else{
    		//组织
    		$user_obj=json_decode(api("/Organization/getInfo",array('orgid'=>$uid)));
    		$real_name=$user_obj->contact;
    		$phone=$user_obj->phone;
    	}
    	$uid=$user->uid;
    	$nickname=$user->nickname;
        $gender=$user->gender;
        $mail=$user->email;
        $type=$user->type;
        $address=City::getName($user->provinceid).'-'.City::getName($user->cityid);
        $photo=\Lib\Image::getUrl($user->photo,array(60),$gender==2?'user_girl':'user');
        //取得认证信息
        $this->data=[
            'photo'=>$photo,
            'nickname'=>$nickname,
            'uid'=>$uid,
            'address'=>$address,
            'type'=>$type,
            'phone'=>$phone,
            'mail'=>$mail,
            'real_name'=>$real_name
        ];
        $this->display('Widget:Concur/creator');
    }
}
