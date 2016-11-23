<?php
/**
 * Created by PhpStorm.
 * User: liuzm
 * Date: 2015/5/12
 */

namespace T\Model;
use Lib\UserSession;
use Org\Util\CMail;
use Think\Model;

/**
 * 消息通知的类
 * Class NotificationModel
 * @package Home\Model
 */
class NotificationModel extends Model{
    protected $connection =  'USER_CONTER';   //数据库连接

    /**
     * 组织用户创建一个小组，该组织下的所有志愿者用户，收到消息通知，并收到邮件
     */
    const invite_users='invite_users';
    
    /**
     * 用户在话题讨论区发起讨论，话题发起者收到消息通知
     */
    const user_reply_sub = 'user_reply_sub';
    
    /**
     * 用户A回复了用户B在用户C的某话题的讨论区中的某评论，用户B收到消息通知
     */
    const user_reply_user='user_reply_user';
    
    /**
     * 组长将用户A移除小组，被移除的用户A收到消息通知
     */
    const deluser_from_group='deluser_from_group';
    /**
     * 用户发起求助||发布资源，通过审核后，用户收到审核结果的站内消息通知：
     */
    const donate_success='donate_success';
    /**
     * 用户发起求助||发布资源，没有通过审核后，用户收到审核结果的站内消息通知：
     */
    const donate_fail ='donate_fail';
    /**
     * 用户提交捐物资请求，发起人收到站内消息通知
     */
    const supplies_request = 'supplies_request';
    /**
     * 用户提交捐服务请求，发起人收到站内消息通知
     */
    const service_request = 'service_request';
    /**
     * 用户提交物资捐助||物资申请请求，发起人接受其捐助请求，该捐助人收到处理结果的站内消息通知
     * 用户提交服务捐助||服务申请请求，发起人接受其捐助请求，该捐助人收到处理结果的站内消息通知
     */
    const supplies_request_success = 'supplies_request_success';
    /**
     * 用户提交物资捐助||物资申请请求，发起人拒绝其捐助请求，该捐助人收到处理结果的站内消息通知
     * 用户提交服务捐助||服务申请请求，发起人拒绝其捐助请求，该捐助人收到处理结果的站内消息通知
     */
    const supplies_request_fail = 'supplies_request_fail';
    /**
     * 用户提交物资捐助请求，发起人接受其捐助请求，该捐助人已完成线上物流的登记，发起人收到处理结果的站内消息通知：
     */
    const donate_logistics_complete = 'donate_logistics_complete';
    /**
     * 用户提交物资申请请求，发起人接受其申请请求，发起人已完成线上物流的登记，申请人人收到处理结果的站内消息通知：
     */
    const apply_logistics_complete = 'apply_logistics_complete';
    /**
     * 用户认证某求助的真实性，该求助发起人收到认证结果的站内消息通知
     */
    const ilove_success = 'ilove_success';
    /**
     * 用户在求助项目/资源项目的讨论区发起讨论，发起人收到消息通知
     */
    const discuss_success = 'discuss_success';
    /**
     * 用户A回复了用户B在用户C的某求助项目/资源项目的讨论区中的某评论，用户B收到消息通知
     */
    const apply_success = 'apply_success';
    
    /**
     * 系统消息的消息类型
     * TODO 需要定义具体详细的类型 0 普通 30以前在志愿家项目中定义，此处没有列出
     * @var INT
     */
    
    /**
     * 用户在话题讨论区发起讨论，话题发起者收到消息通知
     */
    const NOTIFICATION_TYPE_REPLY_SUBJECT=30;
    
    /**
     * 用户A回复了用户B在用户C的某话题的讨论区中的某评论，用户B收到消息通知
     */
    const NOTIFICATION_TYPE_REPLAY_COMMENT=31;
    
    /**
     * 组长将用户A移除小组，被移除的用户A收到消息通知
     */
    const NOTIFICATION_TYPE_DELUSER_INGROUP = 32;
    
    /**
     * 组织用户创建一个小组，该组织下的所有志愿者用户，收到消息通知，并收到邮件
     */
    const  NOTIFICATION_TYPE_ORG_ADDGROUP = 33;
    /**
     * 用户发起求助||发布资源，通过审核后，用户收到审核结果的站内消息通知：
     */
	const NOTIFICATION_TYPE_DONATE_PASS =34;
	/**
	 * 用户发起求助||发布资源，没有通过审核后，用户收到审核结果的站内消息通知
	 */
	const NOTIFICATION_TYPE_DONATE_NPASS =35;
	/**
	 * 用户提交捐物资||申请物资请求，发起人收到站内消息通知
	 */
	const NOTIFICATION_TYPE_SUPPLIES_REQUEST = 36;
	/**
	 * 用户提交捐服务||申请服务请求，发起人收到站内消息通知
	 */
	const NOTIFICATION_TYPE_SERVICE_REQUEST = 37;
	/**
	 * 用户提交物资捐助||物资申请请求，发起人接受其捐助请求，该捐助人收到处理结果的站内消息通知
	 * 用户提交服务捐助||服务申请请求，发起人接受其捐助请求，该捐助人收到处理结果的站内消息通知
	 */
	const NOTIFICATION_TYPE_SUPPLIES_REQUEST_PASS =38;
	/**
	 * 用户提交物资捐助||物资申请请求，发起人拒绝其捐助请求，该捐助人收到处理结果的站内消息通知
	 * 用户提交服务捐助||服务申请请求，发起人拒绝其捐助请求，该捐助人收到处理结果的站内消息通知
	 */
	const NOTIFICATION_TYPE_SUPPLIES_REQUEST_NPASS =39;
	/**
	 * 用户提交物资捐助请求，发起人接受其捐助请求，该捐助人已完成线上物流的登记，发起人收到处理结果的站内消息通知
	 */
	const NOTIFICATION_TYPE_DONATE_LOGISTICS_COMPLETE = 40;
	/**
	 * 用户提交物资申请请求，发起人接受其申请请求，发起人已完成线上物流的登记，申请人人收到处理结果的站内消息通知
	 */
	const NOTIFICATION_TYPE_APPLY_LOGISTICS_COMPLETE = 41;
	/**
	 * 用户认证某求助的真实性，该求助发起人收到认证结果的站内消息通知
	 */
	const NOTIFICATION_TYPE_ILOVE_SUCCESS = 42;
	/**
	 * 用户在求助项目/资源项目的讨论区发起讨论，发起人收到消息通知
	 */
	const NOTIFICATION_TYPE_DISCUSS_SUCCESS = 43;
	/**
	 * 用户A回复了用户B在用户C的某求助项目/资源项目的讨论区中的某评论，用户B收到消息通知
	 */
	const NOTIFICATION_TYPE_APPLY_SUCCESS = 44;
	
    /**
     * 系统消息状态 未读
     * @var INT
     */
    const NOTIFICATION_STATUS_UNREAD = 0;
    /**
     * 系统消息状态 已读
     * @var INT
     */
    const NOTIFICATION_STATUS_READ = 1;

    /**发送消息
     * @param $uid 接受信息的用户编号，可以是数组
     * @param $template 邮件消息模版
     * @param $arr 模版中的参数
     * @param $type 0-只发消息通知 1-发消息通知和邮件 
     */
    public function sendMsg($uid,$template,$arr=array(),$type=0){
    	//return;
        if(is_array($uid)){
            foreach ($uid as $key=>$value) {
                $this->sendMsg($value,$template,$arr,$type);
            }
            return;
        }
        $_data_fun='_data_'.$template;
        $user=D('User')->find($uid);
        if(!$user){
            return false;
        }
      
        if( method_exists( __CLASS__,$_data_fun))
        {
            $arr=$this->$_data_fun($user,$arr);
        }else{
            $arr=$this->_data_default($user,$arr);
        }

        //获取要保存的数据
        $_data=$this->_template_data($template,$arr);
        //获取要保存的类型
       $_type=$this->_template_type($template);

        $this->add(array(
            'to_uid'=>$uid,
            'type'=>$_type,
            'data'=>$_data,
            'status'=>0,
            'create_date'=>time()
        ));
        //发送邮件提醒
        //取得用户邮箱
        if($type==1 && $user['email']){
         	CMail::send($user['email'],$template,$arr);
        }
    }
    private function _template_type($template){
        $sub=array(
            self::invite_users=>self::NOTIFICATION_TYPE_ORG_ADDGROUP,
            self::user_reply_sub=>self::NOTIFICATION_TYPE_REPLY_SUBJECT,
            self::user_reply_user=>self::NOTIFICATION_TYPE_REPLAY_COMMENT,
            self::deluser_from_group=>self::NOTIFICATION_TYPE_DELUSER_INGROUP,
        	self::supplies_request=>self::NOTIFICATION_TYPE_SUPPLIES_REQUEST,	
        	self::service_request=>self::NOTIFICATION_TYPE_SERVICE_REQUEST,
        	self::supplies_request_success=>self::NOTIFICATION_TYPE_SUPPLIES_REQUEST_PASS,
        	self::supplies_request_fail=>self::NOTIFICATION_TYPE_SUPPLIES_REQUEST_NPASS,
        	self::donate_logistics_complete=>self::NOTIFICATION_TYPE_DONATE_LOGISTICS_COMPLETE,
        	self::apply_logistics_complete=>self::NOTIFICATION_TYPE_APPLY_LOGISTICS_COMPLETE,
        	self::ilove_success=>self::NOTIFICATION_TYPE_ILOVE_SUCCESS,
        	self::discuss_success=>self::NOTIFICATION_TYPE_DISCUSS_SUCCESS,
        	self::apply_success=>self::NOTIFICATION_TYPE_APPLY_SUCCESS
        );
        return $sub[$template];
    }
    /**
     * 模版对应的保存数据
     */
    private function _template_data($template,$arr){
        $orgname='<a href="'.SERVER_VISIT_URL.'/uc/%orgid%.html">%orgname%</a>';
        $groupname = '<a href="'.SERVER_VISIT_URL.'/t/group/selgroup/id/%groupid%.html">%groupname%</a>';
        $content = '<span>%content%</span>';      //评论内容
        $username = '<a href="'.SERVER_VISIT_URL.'/uc/%userid%.html">%username%</a>';
        $subname = '<a href="'.SERVER_VISIT_URL.'/t/subject/subinfo/id/%subjectid%.html">%subjectname%</a>';
        $title = '<a href="'.SERVER_VISIT_URL.'/T/Concurinfo/index/id/%id%.html">%title%</a>';
		$supplies_request = $arr['type']?"希望向您的资源项目 $title 申请物资。":"希望为您的求助项目 $title 捐助物资。";
		$service_request = $arr['type']?"希望向您的资源项目 $title 申请服务。":"希望为您的求助项目 $title 提供服务。";
		$supplies_request_success = $arr['tag']?($arr['type']?"已经接受了您对 $title 的物资申请请求。请督促发起人及时完成线上物流信息的登记":"已经接受了您对  $title 物资捐助请求。请及时完成线上物流信息的登记"):($arr['type']?"已经接受了您对 $title 的服务申请请求。请督促发起人保质保量的提供线下服务，并珍惜对方的劳动成果":"已经接受了您对  $title 服务捐助请求。请您保质保量的提供线下服务，感谢您的无私奉献。");
		$supplies_request_fail = $arr['tag']?($arr['type']?"已经拒绝了您对 $title 的物资申请请求。<div>原因：%text% </div>":"已经拒绝了您对 $title 的物资捐助请求。<div>原因：%text%</div>"):($arr['type']?"已经拒绝了您对 $title 的服务申请请求。<div>原因：%text% </div>":"已经拒绝了您对 $title 的服务捐助请求。<div>原因：%text%</div>");	
		
        $sub=array(
            self::invite_users=>"你所在的公益组织 $orgname 创建了一个公益小组 $groupname,邀请您的加入",
            self::user_reply_sub=>"$username 在你的话题 $subname 的讨论区中发起了一个讨论<br> $content",
            self::user_reply_user=>"$username  在话题  $subname 的讨论区中回复了你,说：<br>$content",
            self::deluser_from_group=>"您已经被 $username 从小组 $groupname 中移除",
        	self::supplies_request=>$username.$supplies_request	."<a href='".SERVER_VISIT_URL."/T/Donate/suppliesmanager/id/%id%.html'>马上查看</a>",	
        	self::service_request=>$username.$service_request ."<a href='".SERVER_VISIT_URL."/T/Donate/servicemanager/id/%id%.html'>马上查看</a>",
			self::supplies_request_success=>$username.$supplies_request_success,
        	self::supplies_request_fail=>$username.$supplies_request_fail,
        	self::apply_logistics_complete=>"$username 在资源项目 $title 中完成了物资的线下发货和线上的物流登记。请及时查看。",
        	self::donate_logistics_complete=>"$username 对您的求助项目 $title 完成了物资的线下发货和线上的物流登记。请及时查看。",
        	self::ilove_success=>"$username 认证了您的求助项目 $title 是真实可靠的。请及时查看。",
        	self::discuss_success=>"$username 在 $title 的讨论区中发起了一个讨论。<br/>$content",
        	self::apply_success=>"$username 在 $title 的讨论区中回复了你，说：<br/>$content"	
        						
        );
        $temp=$sub[$template];
        if(!$temp){
            return false;
        }
        foreach ($arr as $key => $value) {
            $temp=str_replace('%'.$key.'%',$value,$temp);
        }
        return $temp;

    }
   
    /**
     *  用户在话题的讨论区中发起讨论或回复某用户
     */
  
    private function _data_default($user,$arr){
        return array(
            'username'=>  $this->getName(UserSession::getUser()),
            'userid'=>  UserSession::getUser('uid'),
            'subjectid'=>$arr['subjectid'],
            'subjectname'=>$arr['subjectname'],
            'content'=>$arr['content']
        );  
    }
    
    /**
     * 组织创建小组邀请成员加入
     */
    private function _data_invite_users($user,$arr){
        return array(
            'username'=>$this->getName($user),
            'userid'=>$user['uid'],
            'orgname'=>  $this->getName(UserSession::getUser()),
            'orgid'=>UserSession::getUser('uid'),
            'groupname'=>$arr['groupname'],
            'groupid'=>$arr['groupid'],
            'userurl'=>SERVER_VISIT_URL."/uc/".$user['uid'].".html",
            'orgurl'=>SERVER_VISIT_URL."/uc/".UserSession::getUser('uid').".html",
            'groupurl'=>SERVER_VISIT_URL."/t/group/selgroup/id/".$arr['groupid'].".html"
        ); 
    }
    
    /**
     * 小组删除成员
     */
    private function _data_deluser_from_group($user,$arr){
        
        return array(
            'username'=>$this->getName(UserSession::getUser()),
            'userid'=>  UserSession::getUser('uid'),
            'groupname'=>$arr['groupname'],
            'groupid'=>$arr['groupid']        
        );    
    }
   /**
    * $arr['tag']=1用户提交物资捐助||物资申请请求，发起人接受其捐助||申请请求
    * $arr['tag']=0用户提交服务捐助||物资申请请求，发起人接受其捐助||申请请求
    * @param unknown $user 捐助人||申请人的信息
    * @param unknown $arr
    * @return multitype:string unknown NULL
    */
    private function _data_supplies_request_success($user,$arr){
    	return array(
    		'id'=>$arr['id'],
    		'title'=>$arr['title'],
    		'type'=>$arr['type'],
    		'tag'=>$arr['tag'],	
    		'username'=>$this->getName(UserSession::getUser()),//发起人昵称
    		'userid'=>UserSession::getUser('uid'),
    		'userurl'=>SERVER_VISIT_URL."/uc/".UserSession::getUser('uid').".html",
    		'uname'=>$this->getName($user),//捐助人||申请人昵称
    		'uurl'=>SERVER_VISIT_URL."/uc/".$user['uid'].".html",
    		'titleurl'=>SERVER_VISIT_URL."/T/Concurinfo/index/id/".$arr['id'].".html",	
    		'info'=>$arr['tag']?($arr['type']?"物资申请请求。请督促发起人及时完成线上物流信息的登记。":"物资捐助请求，请及时完成线上物流信息的登记。"):($arr['type']?"服务申请请求。请您督促发起人保质保量的提供线下服务，并珍惜对方的劳动成果。":"服务捐助请求。请您保质保量的提供线下服务，感谢您的无私奉献。")	
    	);
    }
    /**
     * $arr['tag']=0 用户提交物资捐助||物资申请请求，发起人拒绝其捐助||申请请求
     * $arr['tag']=1 用户提交物资捐助||物资申请请求，发起人拒绝其捐助||申请请求
     */
    private function _data_supplies_request_fail($user,$arr){
    	$info =  array(
    		'id'=>$arr['id'],
    		'title'=>$arr['title'],
    		'type'=>$arr['type'],
    		'text'=>$arr['text'],
    		'tag'=>$arr['tag'],	
    		'username'=>$this->getName(UserSession::getUser()),//发起人昵称
    		'userid'=>  UserSession::getUser('uid'),
    		'userurl'=>SERVER_VISIT_URL."/uc/".UserSession::getUser('uid').".html",
    		'uname'=>$this->getName($user),//捐助人||申请人昵称
    		'uurl'=>SERVER_VISIT_URL."/uc/".$user['uid'].".html",
    		'titleurl'=>SERVER_VISIT_URL."/T/Concurinfo/index/id/".$arr['id'].".html",
    		'info'=>$arr['tag']?($arr['type']?"物资申请请求。<div>原因：".$arr['text']."</div>":"物资捐助请求。<div>原因：".$arr['text']."</div>"):($arr['type']?"服务申请请求。<div>原因：".$arr['text']."</div>":"服务捐助请求。<div>原因：".$arr['text']."</div>"),
    	);
        if($arr['uid']){
            $r = D('User')->where('uid=%d',$arr['uid'])->find();
            $info['username'] = $this->getName($r);
            $info['userid'] = $arr['uid'];
        }
	return $info;
    }
 	/**
 	 * 用户提交捐物资||申请物资请求
 	 */
    private function _data_supplies_request($user,$arr){
    	return array(
    		'id'=>$arr['id'],
    		'title'=>$arr['title'],
    		'type'=>$arr['type'],	
    		'username'=>$this->getName(UserSession::getUser()),//捐助人||申请人昵称
    		'userid'=>  UserSession::getUser('uid'),
    		'userurl'=>SERVER_VISIT_URL."/uc/".UserSession::getUser('uid').".html",
    		'uname'=>$this->getName($user),//发起人昵称
    		'uurl'=>SERVER_VISIT_URL."/uc/".$user['uid'].".html",
    		'infourl'=>SERVER_VISIT_URL."/T/Concurinfo/index/id/".$arr['id'].".html",
    		'info1'=>$arr['type']?"希望向您的资源":"希望为您的求助",
    		'info2'=>$arr['type']?"申请":"捐助"		
    	);
    }
  	/**
	 * 用户提交捐服务请求，发起人收到站内消息通知
	 */
    private function _data_service_request($user,$arr){
    	return array(
    		'id'=>$arr['id'],
    		'title'=>$arr['title'],
    		'type'=>$arr['type'],
    		'username'=>$this->getName(UserSession::getUser()),//捐助人||申请人昵称
    		'userid'=>  UserSession::getUser('uid'),
    		'userurl'=>SERVER_VISIT_URL."/uc/".UserSession::getUser('uid').".html",
    		'uname'=>$this->getName($user),//发起人昵称
    		'uurl'=>SERVER_VISIT_URL."/uc/".$user['uid'].".html",
    		'infourl'=>SERVER_VISIT_URL."/T/Concurinfo/index/id/".$arr['id'].".html",
    		'info1'=>$arr['type']?"希望向您的资源":"希望为您的求助",
    		'info2'=>$arr['type']?"申请":"捐助"
    	);
    }
    /**
     * 用户提交物资捐助请求，发起人接受其捐助请求，该捐助人已完成线上物流的登记
     */
    private function _data_donate_logistics_complete($user,$arr){
    	return array(
    			'id'=>$arr['id'],
    			'title'=>$arr['title'],
    			'username'=>$this->getName(UserSession::getUser()),
    			'userid'=>UserSession::getUser('uid')
    	);
    }
    /**
     * 用户提交物资申请请求，发起人接受其申请请求，发起人已完成线上物流的登记
     */
    private function _data_apply_logistics_complete($user,$arr){
    	return array(
    			'id'=>$arr['id'],
    			'title'=>$arr['title'],
    			'username'=>$this->getName(UserSession::getUser()),
    			'userid'=>  UserSession::getUser('uid')
    	);
    }
    /**
     * 用户认证某求助的真实性，该求助发起人收到认证结果的站内消息通知
     */
    private function _data_ilove_success($user,$arr){
    	return array(
    			'id'=>$arr['id'],
    			'title'=>$arr['title'],
    			'username'=>$this->getName(UserSession::getUser()),
    			'userid'=>  UserSession::getUser('uid')
    	);
    }
    /**
     * 用户在求助项目/资源项目的讨论区发起讨论，发起人收到消息通知：
     */
    private function _data_discuss_success($user,$arr){
    	return array(
    			'id'=>$arr['id'],
    			'title'=>$arr['title'],
    			'content'=>$arr['content'],
    			'username'=>$this->getName(UserSession::getUser()),
    			'userid'=>  UserSession::getUser('uid')
    	);
    }
    /**
     * 用户A回复了用户B在用户C的某求助项目/资源项目的讨论区中的某评论，用户B收到消息通知
     */
    private function _data_apply_success($user,$arr){
    	return array(
    			'id'=>$arr['id'],
    			'title'=>$arr['title'],
    			'content'=>$arr['content'],
    			'username'=>$this->getName(UserSession::getUser()),
    			'userid'=>  UserSession::getUser('uid')
    	);
    }
    /**
     * 获取用户的名称
     */
    private function getName($user){
        /*
        if(is_array($user)){
            $u_type = $user['type'];
        }else{
            $user = D('User')->find($user);
            $u_type = $u['type'];
        }       
        if($u_type==10 || $u_type==11){
            $real_name=D('Volunteer')->where('uid='.$user['uid'])->getField('real_name');
        }else if($u_type==20 || $u_type==21){
            $real_name=D('Organization')->where('uid='.$user['uid'])->getField('org_name');
        }
        if(!$real_name){
            $real_name=$user['nickname'];
        }
         */
        $real_name=$user['nickname'];
        return $real_name;
    }
    
    
}