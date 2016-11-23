<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2015/9/6
 * Time: 14:24
 */

namespace Uc\Controller;
use Lib\EducationData;
use Lib\EIdcardTypeData;
use Lib\Idcard;
use Lib\Image;
use Lib\NationData;
use Lib\User;
use Lib\UserSession;
use Think\Controller;

/**
 * 用户信息设置
 * Class AccountinfoController
 * @package UC\Controller
 */
class AccountinfoController extends Controller
{
    public function _initialize()
    {
        tag('user_login');
        layout('Layout_Account');
    }

    /**
     * 基本资料设置 我的资料设置
     */
    public function base()
    {
        //取得用户基本信息
        $userinfo = D('User')->find(UserSession::getUserId());
        //整理数据
        //性别
        $userinfo['gender'] = $userinfo['gender'] == 1 ? '男' : ($userinfo['gender'] == 2 ? '女' : '未知');

        $province = D('ProvinceCity')->getCity($userinfo['provinceid']);
        $city = D('ProvinceCity')->getCity($userinfo['cityid']);
        $county = D('ProvinceCity')->getCity($userinfo['countyid']);
        $userinfo['address'] = $province['class_name'] . '&nbsp;&nbsp;' . $city['class_name'] . '&nbsp;&nbsp;' . $county['class_name'] . '&nbsp;&nbsp;' . $userinfo['address'];

        //取得认证信息
        $volunteer = User::getVolunteer(UserSession::getUserId());
        if (!$volunteer) {
            $userinfo['status'] = '未申请';
        } else {
            if ($volunteer['status'] == 0) {
                $userinfo['status'] = '未申请';
            } else if ($volunteer['status'] == 10) {
                $userinfo['status'] = '正在审核';
            } else if ($volunteer['status'] == 1) {
                $userinfo['status'] = '已认证';
            } else if ($volunteer['status'] == -1) {
                $userinfo['status'] = '审核失败';
            } else {
                $userinfo['status'] = '未知';
            }
        }
        $volunteer['idcard_type'] = EIdcardTypeData::getCheckEIdcardType($volunteer['idcard_type']);
        $age = Idcard::idcard_age($volunteer['idcard_code']);
        $volunteer['age'] = $age;
        $data['user'] = $userinfo;
        $data['vol'] = $volunteer;
        $this->data = $data;
        $this->display('base');
    }
    /**
     * 头像设置
     */
    public function photo(){
        $photo=UserSession::getPhoto();
        $this->photo=$photo;
        $this->photo_url=Image::getUrl($photo,array(200));
        $this->display('photo');
    }
    /**
     * @param $imgName 文件名称 xxx_xxxx_xxx_xxx
     * @param $savename 文件名称 xxxx.xx
     */
    public function photo_do($imgName){
        //图片裁剪数据
        $params = I('post.');
        if (!isset($params) && empty($params)) {
            return;
        }
        $temp = explode('_', $imgName);
        list($fileType, $fileServer, $fileName, $fileExt) = $temp;
        $savename=$fileName.'.'.$fileExt;
        //临时图片地址
        $pic_path =  IMAGE_SERVER_PATH.'/'.$fileType.'/'.$savename;

        $Think_img = new \Think\Image();
        //裁剪原
        $info = $Think_img->open($pic_path)->crop($params['w'], $params['h'], $params['x'], $params['y'])->save(IMAGE_SERVER_PATH . "/user/" . $savename);
        unlink($pic_path);
        //更新数据
        $data=array(
            'photo'=>sprintf('user_fs_%s_%s',$fileName,$fileExt),
            'update_date'=>time(),
            'uid'=>UserSession::getUserId()
        );
        D('User')->save($data);
        $this->redirect("Accountinfo/photo");
    }

    /**
     * 设置密码
     */
    public function pwd(){
        $user=UserSession::getUser();
        $this->issetpwd=!empty($user['password']) && ( !empty($user['email']) ||   !empty($user['phone']));
        if(!empty($user['email'])){
            $mails=explode('@',$user['email']);
            $mail_name=$mails[0];
            $mail_server=$mails[1];
            $user['email']=substr($mail_name,0,1).'***'.substr($mail_name,-1,1).'@'.$mail_server;
        }
        if(!empty($user['phone'])){
            $user['phone']=substr($user['phone'],0,3).'***'.substr($user['phone'],8,3);
        }
        $this->user=$user;
        $this->display('pwd');
    }
     /**
     * 修改技能标签和关注领域
     */
    public function label(){
        //获取关注领域列表
        $field = D('CategoryServer')->getAllLabel();
        $this->assign('field',$field);
        //获取用户的关注领域                       
        $interest = json_decode(api("/User/getUserInterest",array('uid'=>  UserSession::getUser('uid'))));
        $interestlist = ',';
        foreach($interest as $k=>$v){
            $interestlist .= $k.','; 
        }
        $interestlist = trim($interestlist,',');
        $this->assign('interestlist',$interestlist);
        //获取用户的技能标签
        $list = D('UserAbilityTag')->getField();
        $this->assign('list',$list);
        $this->display('label');
    }

    /**
     * 设置绑定账号
     */
    public function bind(){
        /**
         * 1.腾讯微博
         * 2.新浪微博
         * 3.人人
         * 4.微信号
         * 5.qq
         */
        //腾讯微博 新浪微博 人人网 账号绑定信息
        $data=D('T/UserOauth2')->getBindingType();
        $this->qq_user=D('QqUser')->where('uid=%d and `status`=1',UserSession::getUserId())->find();
        //检查微信绑定
        $this->wx_user=D('WxUser')->where('uid=%d',UserSession::getUserId())->find();
        $this->returnurl=urlSafeBase64_encode(SERVER_VISIT_URL.'/uc/accountinfo/bind');
        $this->data=$data;
        $this->display();
    }

    /**
     * 取消绑定微信
     */
    public function unbindwx($returnurl=''){
        if(! User::exists_login(UserSession::getUserId())){
            $this->error('请设置登录账号');
        }else{
            $returnurl=empty($returnurl)?urlSafeBase64_encode('/'):$returnurl;
            //解除绑定
            D('WxUser')->unbind();
            $this->redirect(urlSafeBase64_decode($returnurl));
        }
    }
}