<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/9/9
 * Time: 14:27
 */

namespace Uc\Controller;


use Lib\NationData;
use Lib\EducationData;
use Lib\Sms;
use Lib\UserSession;
use Think\Controller;
use Uc\Business\WxBusiness;
use Lib\User;
use Lib\Idcard;
use Lib\EIdcardTypeData;

class AccountajaxController extends Controller
{
    public function _initialize()
    {
        tag('user_login');
        layout(false);
    }
    /**
     * 编辑基本信息
     */
    public function edit_base()
    {
        layout(false);
        $userinfo = D('User')->find(UserSession::getUserId());
        $userinfo['gender'] = $userinfo['gender'] == 2 ? '2' : '1';
        $volunteer = D('T/Volunteer')->find(UserSession::getUserId());
        $this->vol = $volunteer;
        $this->userinfo = $userinfo;
        $this->ethnic = NationData::getNationData();
        $this->education = EducationData::getData();
        $this->province = D('ProvinceCity')->getChildrenCity(0);
        $this->city = D('ProvinceCity')->getChildrenCity($userinfo['provinceid']);
        $county = D('ProvinceCity')->getChildrenCity($userinfo['cityid']);
        if (empty($county)) {
            $city = D('ProvinceCity')->getCity($userinfo['cityid']);
            $county = D('ProvinceCity')->getChildrenCity($city['class_parent_id']);
        }
        $this->county = $county;
        $content = $this->fetch('edit_base');
        $this->return_success($content);
    }

    /*** 提交编辑的基本信息
     * 昵称 民族 性别 最高学历  常住地址 QQ
     * @param $nickname 昵称
     * @param $ethnic 民族
     * @param $gender 性别
     * @param $education 最高学历
     * @param $provinceid 省份
     * @param $cityid 城市
     * @param $countyid 区县
     * @param $address 地址
     * @param $qq qq号
     */
    public function edit_base_do($nickname, $ethnic, $gender, $education, $provinceid, $cityid, $countyid, $address, $qq)
    {
        layout(false);
        if (!verify_name($nickname)) {
            $this->return_error('昵称为2到10为合法字符');
        }
        $r =NationData::getCheckNation($ethnic);
        if (empty($r)) {
            $this->return_error('民族错误');
        }
        if ($gender != 1 && $gender != 2) {
            $this->return_error('性别错误');
        }
        $r = EducationData::getCheckEducation($education);
        if (empty($r)) {
            $this->return_error('学历错误');
        }
        $chren = D('ProvinceCity')->getChildrenCity($provinceid);
        if (empty($chren) || !$this->exits_city($chren, $cityid)) {
            $this->return_error('省份错误');
        }
        $chren = D('ProvinceCity')->getChildrenCity($cityid);
        if (empty($chren) || !$this->exits_city($chren, $countyid)) {
            $this->return_error('市错误');
        }
        if (!preg_match('/^\d{4,17}$/', $qq)) {
            $this->return_error('qq号错误');
        }
        //入库
        M()->startTrans();
        $data = array(
            'nickname' => $nickname,
            'ethnic' => $ethnic,
            'gender' => $gender,
            'provinceid' => $provinceid,
            'cityid' => $cityid,
            'countyid' => $countyid,
            'address' => $address,
            'qq' => $qq,
            'uid' => UserSession::getUserId(),
            'update_date' => time()
        );
        $result = D('User')->save($data);
        if (!$result) {
            M()->rollback();
            $this->return_error('更新用户信息失败');
        }
        //更新最高学历信息
        $data = array(
            'update_date' => time(),
            'uid' => UserSession::getUserId(),
            'education' => $education
        );
        $vol = D('T/Volunteer')->find(UserSession::getUserId());
        if (!$vol) {
            //没有认证信息 新增一个未认证的信息
            $result = D('T/Volunteer')->add($data);
            if (!$result) {
                M()->rollback();
                $this->return_error('更新用户信息失败');
            }
        } else {
            //更新
            $result = D('T/Volunteer')->save($data);
            if (!$result) {
                M()->rollback();
                $this->return_error('更新用户信息失败');
            }
        }
        M()->commit();
        $this->gender = $gender == 1 ? '男' : '女';
        $province = D('ProvinceCity')->getCity($provinceid);
        $city = D('ProvinceCity')->getCity($cityid);
        $county = D('ProvinceCity')->getCity($countyid);
        $this->address = $province['class_name'] . '&nbsp;&nbsp;' . $city['class_name'] . '&nbsp;&nbsp;' . $county['class_name'] . '&nbsp;&nbsp;' . $address;
        $content = $this->fetch('info_base');
        $this->return_success($content);

    }

    /**
     * @param $real_name 真实姓名
     * @param $idcard_code 身份证
     */
    public function edit_vol_do($real_name,$idcard_code,$idcard_type)
    {
        layout(false);
        if (User::isVipUser(UserSession::getUserId())) {
            $this->return_error('您的认证已经通过，不能修改认证信息');
        }
        //提取志愿者信息
        if(!verify_name($real_name)){
            $this->return_error('真实姓名和合法');
        }
        if(!Idcard::idcard_checksum($idcard_code)){
            $this->return_error('身份证错误');
        }
        //检查身份证是否存在
        $c=D('T/Volunteer')->where(" uid<>%d and idcard_type=%d and idcard_code='%s' ",UserSession::getUserId(),$idcard_type,$idcard_code)->count();
        if($c>0){
            $this->return_error('身份证已经被使用');
        }
        $volunteer = User::getVolunteer(UserSession::getUserId());
        if (!$volunteer) {
            $this->status = '未申请';
        } else {
            if ($volunteer['status'] == 0) {
                $this->status = '未申请';
            } else if ($volunteer['status'] == 10) {
                $this->status = '正在审核';
            } else if ($volunteer['status'] == 1) {
                $this->status = '已认证';
            } else if ($volunteer['status'] == -1) {
                $this->status = '审核失败';
            } else {
                $this->status = '未知';
            }
        }
        //更新数据
        $data=array(
            'idcard_type'=>$idcard_type,
            'idcard_code'=>$idcard_code,
            'real_name'=>$real_name,
            'update_date'=>time()
        );
        if(!$volunteer){
            //不存在认证信息 新增一条
            $result=D('T/Volunteer')->add($data);
        }else{
            $data['uid']=UserSession::getUserId();
            //更新
            $result=D('T/Volunteer')->save($data);
        }
        if($result===false){
            $this->return_error('更新错误');
        }else{
            $this->idcard_type=EIdcardTypeData::getCheckEIdcardType($idcard_type);
            $this->age=Idcard::idcard_age($idcard_code);
            $content = $this->fetch('edit_vol_do');
            $this->return_success($content);
        }
    }
    /**
     * 编辑认证信息
     */
    public function edit_vol()
    {
        layout(false);
        //检测用户 认证是否已经通过
        if (User::isVipUser(UserSession::getUserId())) {
            $this->return_error('您的认证已经通过，不能修改认证信息');
        }
        //提取志愿者信息
        $volunteer = User::getVolunteer(UserSession::getUserId());
        if (!$volunteer) {
            $volunteer['status'] = '未申请';
        } else {
            if ($volunteer['status'] == 0) {
                $volunteer['status'] = '未申请';
            } else if ($volunteer['status'] == 10) {
                $volunteer['status'] = '正在审核';
            } else if ($volunteer['status'] == 1) {
                $volunteer['status'] = '已认证';
            } else if ($volunteer['status'] == -1) {
                $volunteer['status'] = '审核失败';
            } else {
                $volunteer['status'] = '未知';
            }
        }
        $card = $volunteer['idcard_code'];
        //出生日期
        $age = Idcard::idcard_age($card);
        $volunteer['age'] = $age;
        $this->vol = $volunteer;
        $this->idcard = EIdcardTypeData::getData();
        $content = $this->fetch('edit_vol');
        $this->return_success($content);
    }
    /**检测是否存在
     * @param array $arr
     * @param int $key_v
     */
    private function exits_city($arr = array(), $v = 0)
    {
        $result = false;
        foreach ($arr as $key => $value) {
            if ($value['id'] == $v) {
                $result = true;
                break;
            }
        }
        return $result;
    }
    private function return_error($msg = '')
    {
        $this->ajaxReturn(array('status' => -1, 'msg' => $msg));
        exit;
    }

    private function return_success($msg = '')
    {
        $this->ajaxReturn(array('status' => 1, 'msg' => $msg));
        exit;
    }

    /**
     * 编辑邮箱
     */
    public function edit_mail(){
        $mail=UserSession::getMail();
        $this->mail=$mail;
        $this->display('edit_mail');
    }

    /**
     * 设置编辑的邮箱
     */
    public function edit_mail_do($mail,$code){
        if(!check_email($mail)){
            $this->return_error('邮箱错误');
        }
        //验证邮箱是否存在
        $count=D('User')->where("email='%s'",$mail)->count();
        if($count>0){
            $this->return_error('邮箱已经存在');
        }
        $result=MailController::verify($mail,$code,1,1);
        if(!empty($result)){
            $this->return_error('邮箱验证码错误');
        }else{

            //更新邮箱
            $data = array(
                'email'=>$mail,
                'status'=>1,
                'uid' => UserSession::getUserId(),
                'update_date' => time()
            );
            $result = D('User')->save($data);
            if($result===false){
                $this->return_error('更新邮箱错误');
            }else{
                $mails=explode('@',$mail);
                $mail_name=$mails[0];
                $mail_server=$mails[1];
                $this->mail=substr($mail_name,0,1).'***'.substr($mail_name,-1,1).'@'.$mail_server;
                $content = $this->fetch('edit_mail_do');
                $this->return_success($content);
            }

        }
    }

    public function edit_phone(){
        $this->display('edit_phone');
    }

    /**修改手机号
     * @param $phone 新的手机号
     * @param $code 手机验证码
     */
    public function edit_phone_do($phone,$smscode,$code){
        //1.验证验证码是否正确
        $result=Sms::verify($phone,$smscode,$code);
        if($result['status']<0){
            $this->ajaxReturn($result);
        }
        //验证手机号是否存在
        $c=D('User')->where(['phone'=>$phone])->count();
        if($c>0){
            $this->return_error('手机号已经存在');
        }else{
            //修改手机
            //更新邮箱
            $data = array(
                'phone'=>$phone,
                'phone_status'=>1,
                'uid' => UserSession::getUserId(),
                'update_date' => time()
            );
            $result = D('User')->save($data);
            if($result===false){
                $this->return_error('更新邮箱错误');
            }else{
                $this->phone=substr($phone,0,3).'***'.substr($phone,8,3);
                $content = $this->fetch('edit_phone_do');
                $this->return_success($content);
            }
        }
    }
    
    /**
     * ajax设置用户的关注领域和技能标签
     */
    public function setUserLabel(){
        $ly_label  = rtrim(I('ly_label'),',');  //获取领域标签
        //关注领域验证
        $lyArr = array(1,2,3,4,5,6,7,8,9,10,11);
        $ly = explode(',',$ly_label);
        foreach($ly as $val){
            if(!in_array($val,$lyArr))  $this->return_error("关注领域含有非法字符");
        }
        //技能标签验证
        $jn_label  = rtrim(I('jn_label'),',');  //获取技能标签
        $jn =  explode(',',$jn_label);
        foreach($jn as $v){
            if(!preg_match("/^[^~!@#\<\>\$\%\^&\*\(\)\+]{1,}$/",$v))    $this->return_error("技能标签含有非法字符");
        }
        $lable = D('UserAbilityTag');
        $lable->startTrans();
        //删除用户已存在的关注领域 
        if(!D('UserCategoryServerTagList')->delLabel()){
            $lable->rollback();
            $this->return_error("初始化用户关注领域失败");
        }
        //重新添加用户的关注领域
        if(!D('UserCategoryServerTagList')->addLabel($ly)){
            $lable->rollback();
            $this->return_error("添加用户关注领域失败");
        }        
        //检测技能标签是否存在,存在则返回技能标签id，不存在则添加并返回id
        $jnList = D('CategoryTag')->checkLabel($jn);
        if(!$jnList){
            $lable->rollback();
            $this->return_error("技能标签处理失败");
        }
     
        
        //删除用户已有的技能标签
        if(!D('UserAbilityTag')->delLabel()){
            $lable->rollback();
            $this->return_error("用户技能标签初始化失败");
        }
	
	
        //重新添加用户的技能标签
        if(!D('UserAbilityTag')->addLabel($jnList)){
            $lable->rollback();
            $this->return_error("用户技能标签添加失败");
        }
	
			
        $lable->commit();
        $this->return_success('success');  
    }

    const wx_bind_code_session='wx_bind_code_session';
    /**
     * 获取用户绑定微信的二维码
     */
    public function get_bind_weixin_qrcode(){
        //判断用户是否已经绑定了微信
        $c=D('WxUser')->where('uid=%d',UserSession::getUserId())->count();
        if($c>0){
            $this->return_error('您已经绑定过了微信号');
        }
        //获取绑定标识码，等待用户扫描
        $code=WxBusiness::bind_code(UserSession::getUserId());
        if($code===false){
            $this->return_error('生成标识失败，稍后重试');
        }
        //通过微信接口 获取微信参数二维码
        $result=api(WX_API_URL.'/qrcode/bind/code/'.$code);
        session(self::wx_bind_code_session,$code);
        echo $result;
        exit;
    }

    /**
     * 检查是否已经绑定了
     */
    public function check_wx_bind(){
        $code=session(self::wx_bind_code_session);
        $wx_login=D('WxLogin')->where(array('login_code'=>$code,'uid'=>UserSession::getUserId(),'status'=>1))->find();
        if(empty($wx_login['openid'])){
            $this->return_error('未绑定');
        }else{
            //将状态修改成-1
            D('WxLogin')->save(array(
                'status'=>-1,
                'id'=>$wx_login['id']
            ));
            WxBusiness::clear_code();
            $this->return_success('绑定成功');
        }
    }
}