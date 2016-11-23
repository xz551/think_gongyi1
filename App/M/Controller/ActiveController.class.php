<?php
namespace M\Controller;
use Lib\Image;
use M\Session\MUserSession;
use T\Model\ActiveModel;
use Think\Controller;
use Common\Api\CityApi;
class ActiveController extends Controller{
    /**
     * 活动详情
     * @param int $id
     *
     */
	public function index($id=0){
		//获取指定活动信息 活动状态         0 待审核 1 审核通过 -1 审核不通过
		$active=D("Active")->getActive($id);
        if(!$active){
            $this->error('查询的活动不存在');
        }else if($active['status']==ActiveModel::ACTIVE_STATUS_WAITING){
            $this->error('活动正在审核');
        }else if($active['status']==ActiveModel::ACTIVE_STATUS_REJECT){
            $this->error('活动审核没有通过');
        }
        $this->title=$active['name'];
        $this->info=$active;
        if(MUserSession::islogin()){
            //检查 当前登录用户是否参与过活动
            $this->join=M('active_join')->where('active_id=%d and uid=%d and status=1',$id,MUserSession::getUserId())->find();
        }
        $this->display('index');
    }

    /**参与活动
     * @param $id 活动编号
     * @param $uname 留的姓名
     * @param $uphone 留的手机号
     */
    public function join($id, $uname='',$uphone='') {
        tag('m_user_login');
        //检查传递的参数
        $active=M('active')->where('id=%d',$id)->find();
        if(!$active){
            $this->return_error('活动编号错误');
        }
        if($active['status']!=ActiveModel::ACTIVE_STATUS_NORMAL){
            $this->return_error('活动状态不允许');
        }
        //判断活动是否需要留下联系方式
        if($active['need_contact']==1){
            if (!preg_match('/^[_a-zA-Z0-9\x{4e00}-\x{9fa5}]{2,10}$/u', $uname)) {
                $this->return_error('姓名错误');
            }
            if (!preg_match('/^1\d{10}$/', $uphone)) {
                $this->return_error('手机号错误');
            }

        }
        //检查用户是否参与过活动
        $active_join=M('active_join')->where('active_id=%d and uid=%d',$id,MUserSession::getUserId())->find();
        if($active_join && $active_join['status']==1){
            $this->return_error('您已经报名过该活动');
        }
        //报名
        $data=array(
            'status'=>1,
            'name'=>$uname,
            'phone'=>$uphone,
            'update_time'=>time(),
            'join_date'=>time()
        );
        if($active_join){
            //更新
            $result=M('active_join')->where('active_id=%d and uid=%d',$id,MUserSession::getUserId())->save($data);
        }else{
            //新增
            $data['active_id']=$id;
            $data['uid']=MUserSession::getUserId();

            $result=M('active_join')->add($data);
        }
        if(!$result){
            $this->return_error('报名失败，稍后重试');
        }else{
            $this->return_success('报名成功');
        }
    }
    private function return_error($msg){
        $this->ajaxReturn(array('status'=>-1,'msg'=>$msg));
        exit;
    }
    private function return_success($msg){
        $this->ajaxReturn(array('status'=>1,'msg'=>$msg));
        exit;
    }

    /**
     * @param type $p   需要查询的页数
     * @param type $pagesize    每页数据的数目
     * @param type $area    地址编号，默认0则为查询全部
     * @param type $label   标签编号，默认0则为查询全部
     * @param type $type    类型  默认0表示查询全部
     */
    public function all($last_id = '', $pagesize = 20, $area = 0, $label = 0, $type = 0,$status='',$actname='') {
        layout(!IS_AJAX);
        $this->title='公益活动';
        $data=D('Active')->getActiveList($last_id,$pagesize,$area,$label,$type,$status,$actname);
        $this->data=$data;
        $this->display(IS_AJAX?'ajax_all':'all');
    }

    /**
     * 调取用户手机号和姓名
     */
    public function get_phone_name(){
        //用户登录拦截
        tag('m_user_login');
        layout(false);
        //取得上次用户参与活动填写的手机号和姓名
        $last_join=M('active_join')->where("uid=%d and ( name <>'' or phone <> '' )",MUserSession::getUserId())->order('id desc')->find();
 
	    $uname='';
        $phone='';
        if(!$last_join){
            //不存在 数据库调取用户填写信息
            $user_info=D('T/User')->getUser(MUserSession::getUserId());
            if($user_info){
                $uname=$user_info['nickname'];
                $phone=$user_info['phone'];
            }
        }else{
            $uname=$last_join['name'];
            $phone=$last_join['phone'];
        }
        $this->uname=$uname;
        $this->phone=$phone;
        $this->display('get_phone_name');
    }

    /**取消报名
     * @param $id
     */
    public function cancel_bm($id){
        tag('m_user_login');
        //检查传递的参数
        $active=M('active')->where('id=%d',$id)->find();
        if(!$active){
            $this->return_error('活动编号错误');
        }
        if($active['status']!=ActiveModel::ACTIVE_STATUS_NORMAL){
            $this->return_error('活动状态不允许');
        }
        //检查用户是否参与过活动
        $active_join=M('active_join')->where('active_id=%d and uid=%d',$id,MUserSession::getUserId())->find();
        if(!$active_join ||  $active_join['status']==-1){
            $this->return_error('您没有报名该活动');
        }
        //取消参与
        $result=M('active_join')->where('id='.$active_join['id'])->save(array(
            'status'=>-1,
            'update_time'=>time()
        ));
        if($result){
            $this->return_success('取消成功');
        }else{
            $this->return_success('取消失败');
        }
    }
}
