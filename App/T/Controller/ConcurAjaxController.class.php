<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/6/30
 * Time: 9:57
 */

namespace T\Controller;
use Lib\User;
use Lib\UserSession;
use T\Model\ConcurModel;
use T\Service\ConcurFeedbackService;
use T\Model\UserOauth2ShareModel;
use Think\Controller;
use Lib\City;
use Lib\Helper;


use Lib\Image;
/**
 * 处理ajax请求
 * Class ConcurAjaxController
 * @package T\Controller
 */
class ConcurAjaxController extends Controller {
	/**
	 *ajax返回带分页的申请名单
	 */
	public function ajaxPage($id,$p=1,$pageSize=20,$tag=1,$status=1,$mark=0){
		$donName=self::donName($id, $p, $pageSize, $tag,$status,$mark);
    	$this->assign("donName",$donName);
		//捐助总人数
		$donCount=D("ConcurServiceApply")->donName($id,$tag=0,$status);
		$donCount=count($donCount);
		$this->assign("donCount",$donCount);
		$this->assign("page",ajax_page($donCount,$pageSize));
		$this->display("ajaxPage");
	}
	/**
	 * ajax返回带分页的讨论，并局部刷新页面
	 */
	public function ajaxReply($p=1,$pageSize=8,$cid=0){
		$id=I('id');
		//查询求助下所有直接评论
		$commentInfo=D("Comment")->commentInfo($id,$cid,$p,$pageSize);
		foreach ($commentInfo as $k=>$v){
			//查询某一求助下某一条直接评论的回复
			$commentInfo[$k]['reply']=D("Comment")->commentInfo($id,$v['id']);
		}
		//获取所有直接评论个数
		$num =  M('Comment')->where("sid=%d && cid=0 && type=1",$id)->count();
		$this->assign("num",$num);
		$this->assign("page",ajax_page($num,$pageSize));
		$this->assign('id',$id);  //当前id
		$this->assign("commentInfo",$commentInfo);
		$this->display("ajaxReply");
	}
	/**
	 * 	添加详情页评论
	 * @param unknown $sid 求助项目||资源项目id
	 * @param unknown $calluid 给哪个人 回复
	 * @param unknown $cid 给哪条评论回复
	 * @param unknown $content 回复内容
	 */
	public function addComment($sid,$calluid,$cid,$content){
		tag("user_login");
		$res=D("Comment")->addComment($sid,$calluid,$cid,$content);
		//添加站内消息
		//求助项目||资源项目信息
		$info=D("Concur")->getConcurById($sid);
		$arr=array('id'=>$sid,'title'=>$info['title'],'content'=>$content);
		if($cid){
			//回复评论的站内消息
			if($calluid != UserSession::getUser('uid')){
				D("Notification")->sendMsg($calluid,'apply_success',$arr);
			}
			
		}else{
			//直接评论的站内消息
			if($info['creator'] != UserSession::getUser('uid')){
				D("Notification")->sendMsg($info['creator'],'discuss_success',$arr);
			}
			
		}
		
		$this->ajaxReturn($res);
	}
    /**
     * 执行爱心认证
     * @param $id 求助编号
     */
    public function ilove_renzheng($id){
        //判断用户登录
        tag('user_login');
        //取得该求助信息 只有求助 才有爱心认证
        $concur=M('concur')->where('type=0 and id=%d',$id)->find();
        if(!$concur){
            $this->return_error('该求助不存在');
        }
        //自己不可以  认证
        if($concur['creator']==UserSession::getUserId()){
            $this->return_error('您是发起人，可以分享项目邀请他人进行认证');
        }
        $status=$concur['status'];
        if($status!=ConcurModel::STATUS_NORMAL && $status!=ConcurModel::STATUS_ENDED){
            $this->return_error('该求助不能进行认证');
        }
        //判断是否为认证的组织或用户
        if(!User::isVip(UserSession::getUserId())){
            $this->return_error('只有通过认证的用户才可以担任爱心认证员');
        }
        //判断用户是否已经认证过
        $verify_count=M('concur_verify')->where('concur_id=%d and userid=%d',$id,UserSession::getUserId())->count();
        if($verify_count>0){
            $this->return_error('您已经认证过');
        }
        //执行认证
        $loveId=M('concur_verify')->add(
            array(
                'concur_id'=>$id,
                'userid'=>UserSession::getUserId(),
                'datetime'=>time_format()
            )
        );
        if(!$loveId){
            $this->return_error('认证失败，稍后重试');
        }
        //添加站内消息
        $arr=array('id'=>$id,'title'=>$concur['title']);
        D("Notification")->sendMsg($concur['creator'],'ilove_success',$arr);
        $this->return_success('认证成功');
    }

    /**
     * ajax返回错误信息 并结束程序
     * @param $msg
     */
    private function return_error($msg){
        $this->ajaxReturn(array('status'=>-1,'msg'=>$msg));exit;
    }

    /**
     * ajax返回正确信息 并结束程序
     * @param $msg
     */
    private function return_success($msg){
        $this->ajaxReturn(array('status'=>1,'msg'=>$msg));exit;
    }

    /**
     * 取得爱心认证员列表信息
     * 返回局部html
     */
    public function ilove(){
        layout(false);
        $concur_id=$_GET['id'];
        //取得认证信息
        $data=M('concur_verify')->where('concur_id=%d',$concur_id)->order('datetime desc ')->getField('userid',true);
        if(!empty($data))
        {
            $uid=UserSession::getUser('uid');
            //取得认证信息
            $userid_string=join(',',$data);// explode();
            $json=api('/user/getuserlist?uidArr='.$userid_string.'&isorg=1');
            if(Helper::isJson($json)){
                //整理数据
                $users=json_decode($json);
                $user_list=array();
                foreach ($users as $key => $value) {
                    $u['uid']=$value->uid;
                    $u['type']=STATIC_SERVER_URL.($value->type==21||$value->type==20?'/usercenter/org/images/icon_v.png':'/usercenter/tp/images/icon_v.png');
                    if($u['uid']==$uid){
                        //当前登录用户已经参与了认证
                        $this->is_curr_user=1;
                    }
                    $u['nickname']=$value->nickname;
                    $u['photo']=\Lib\Image::getUrl($value->photo,array(60),$value->gender==2?'user_girl':'user');
                    //provinceid,cityid
                    $u['address']=City::getName($value->provinceid).'&nbsp;'.City::getName($value->cityid);
                    $u['org_list']=array();
                    if( $value->type!=21 && $value->type!=20 &&  $value->org_list){
                        foreach ($value->org_list as $_key => $v) {
                            array_push($u['org_list'],$v->org_name);
                        }
                    }
                    array_push($user_list,$u);
                }
                $this->user_list=$user_list;
            }
        }
        $this->display();
    }

    /**
     * 用户反馈
     * 发起人、申请人可执行当前操作
     */
    public function feedback($relation_id){
        tag('user_login');
        tag('db_begin');
        $verify=ConcurFeedbackService::feedback_verify($relation_id);
        if(is_string($verify)){
            $this->return_error($verify);
        }
        //启动事物操作
        M()->startTrans();
        //执行反馈
        $data=D('Discuse')->create();
        if(!$data){
            $this->return_error(D('Discuse')->getError());
        }else{
            $data['type']=7;//标识 为 求助反馈
            $data['is_creator']=UserSession::getUserId()==M('Concur')->where('id=%d',$relation_id)->getField('creator')?1:0;
            $result=D('Discuse')->data($data)->add();
            if($result){
                //新增成功
                //执行保存视频
                $video=ConcurFeedbackService::save_video();
                if(is_string($video)){
                    //保存视频视频
                    M()->rollback();
                    $this->return_error($video);
                }else{
                    //保存媒体
                    $save_result=ConcurFeedbackService::save_back_ext($video,$result);
                    if(!$save_result){
                        //保存媒体失败
                        M()->rollback();
                        $this->return_error('评论失败');
                    }else{
                        //成功
                        M()->commit();
                        //显示评论后的html
                        $concur=M('Concur')->find($relation_id);
                        $this->concur=$concur;
                        $this->data=array(ConcurFeedbackService::get_back_data($video,array(),$concur));
                        layout(false);
                        $this->display('feedback');
                    }
                }
            }else{
                M()->rollback();
                $this->return_error('反馈失败');
            }
        }
    }

    /**
     * 取得求助反馈信息
     * @param $id
     */
    public function getfeedback($id,$p=1,$pagesize=20){
        layout(false);
        $concur=M('concur')->find($id);
        if(!$concur){
            $this->display('feedback_null');
            return;
        }
        $this->concur=$concur;
        $discuse=M('discuse as d')
            ->join("LEFT JOIN tb_concur_apply as ca on d.user_id=ca.userid  and d.relation_id=ca.concur_id")
            ->join("LEFT JOIN tb_concur_verify as cv on d.user_id=cv.userid  and d.relation_id=cv.concur_id")
            ->where('relation_id=%d and type=7 and status=1',$id)
	    ->field('d.*,ca.service,ca.supplies,ca.money ,cv.id as verify ')
            ->page($p.','.$pagesize)
            ->order('id desc')
            ->select();
        if(!$discuse){
            //没有反馈
            $this->display('feedback_null');
            return;
        }
        //取得总反馈数量
        $count=count($discuse);
        if($count==$pagesize){
            //很多可能还有下一页 取出总条数
            $count=M('discuse')->where('relation_id=%d and type=7 and status=1',$id)->count();
        }
        if($discuse){
            $data=[];
            foreach ($discuse as $key => $value) {
                $return=ConcurFeedbackService::get_back_data(array(),$value,$concur);
                array_push($data,$return);
            }
        }
        $this->is_count=$count>=$pagesize;
        $this->data=$data;
        $this->display('feedback');
    }
    /**
     * 取得爱心动态信息
     * @param unknown $id
     * @param number $p
     * @param number $pageSize
     * @param unknown $tag
     */
    public function showLove($id,$p=1,$pageSize=8,$tag=-1,$status=-2,$mark=1){
    	$donName=self::donName($id, $p, $pageSize, $tag,$status,$mark);
    	$this->assign("donName",$donName);
    	$this->display("showLove");
    }
    /**
     * 
     * @param unknown $id
     * @param unknown $p
     * @param unknown $pageSize
     * @param unknown $tag 1代表ajaxPage调用 、-1代表showLove调用、0代表获取个数时调用
     * @param unknown $status
     * @param unknown $mark 0代表ajaxPage调用， 1代表showLove调用
     * @return string
     */
    public function donName($id,$p,$pageSize,$tag,$status,$mark){
    	//捐助||求助名单
    	$donName=D("ConcurServiceApply")->donName($id,$tag,$status,$p,$pageSize);
    	
    	foreach ($donName as $k=>$v){
    		//判断一下服务表是否有记录 
    		$r=D("ConcurServiceApply")->service($id,$v['apply_uid'],$v['time'],$tag);
    		//判断一下物资表中是否有记录
    		$res=D("ConcurSuppliesApply")->supplies($id,$v['apply_uid'],$v['time'],$tag);
    		//判断一下$id是求助项目还是捐助项目
    		$concur=D("Concur")->getConcurById($id);
    		if($concur['type']){
    			if($r){
    				$donName[$k]['service']=$mark?"希望申请服务":"成功申请服务";
    			}
    			if($res){
    				//申请人想要捐助/救助的物资
    				$result=D("ConcurSuppliesApply")->getAlreadySupplies($id,$v['apply_uid'],$status);
    				$a=$mark?"希望申请":"成功申请";
    				if($result){
    					foreach ($result as $key=>$val){
    						$a .=$val['num']." x ".$val['name']."、";
    					}
    					$donName[$k]['supplies']=rtrim($a,"、");
    				}
    				$donName[$k]['anonymous']=$res['anonymous'];
    			}
    		}else{
    			if($r){
    				$donName[$k]['service']=$mark?"希望提供服务":"提供服务";
    			}
    			if($res){
    				//申请人想要捐助/救助的物资
    				$result=D("ConcurSuppliesApply")->getAlreadySupplies($id,$v['apply_uid'],$status);
    				$a=$mark?"希望捐助":"捐助";
    				if($result){
    					foreach ($result as $key=>$val){
    						$a .=$val['num']." x ".$val['name']."、";
    					}
    					$donName[$k]['supplies']=rtrim($a,"、");
    				}
    				$donName[$k]['anonymous']=$res['anonymous'];
    			}
    		}
    		//捐助人的信息
    		$user=json_decode(api("/User/getById",array('uid'=>$v['apply_uid'])));
    		$province=M("ProvinceCity")->where(array("id"=>$user->provinceid))->find();
    		$city=M("ProvinceCity")->where(array("id"=>$user->cityid))->find();
    		$donName[$k]['userAddress']=$province['class_name'].' '.$city['class_name'];
    		$donName[$k]['image']=Image::getUrlThumbCenter($user->photo,array(0),$user->gender==2?'user_girl':'user');
    		$donName[$k]['uid']=$user->uid;
    		$donName[$k]['nickname']= $res['anonymous']?mb_substr($user->nickname,0,1,'utf-8').'***'.mb_substr($user->nickname,-1,1,'utf-8'):$user->nickname;
    		$donName[$k]['type']=$user->type;
    		$donName[$k]['time']=time_day($v['time']);
    	}
    	return $donName;
    }
    /**
     * 分享求助
     */
    public function shareSub(){
    	layout(false);
    	//查看用户是否绑定 1.qq  2.weibo  3.renren
    	$list = D('UserOauth2')->getBindingType();
    	$this->assign('list',$list);
    	//mb_strlen($str,'UTF8');
    	//当前地址“益”起来，更精彩！我在中青公益中发现这个资源 “资源标题” 很不错！@中青公益 聚合青年公益力量。链接地址
    	$sid = I('sid');
    	$url = SERVER_VISIT_URL.U('T/Concurinfo/index',array('id'=>$sid));
    	$info=D("Concur")->getConcurById($sid);
    	if($info['type']){
    		$content = "“益”起来，更精彩！我在中青公益中发现这个资源 “".I('name')."” 很不错！@中青公益 聚合青年公益力量。".$url;
    	}else{
    		$content = "“益”起来，更精彩！我在中青公益中发现这个求助 “".I('name')."” 需要帮助！@中青公益 聚合青年公益力量。".$url;
    	}
    	$this->assign('num',140-mb_strlen($content,'UTF8'));
    	$this->assign('content',$content);
    	$this->assign('relation_type',UserOauth2ShareModel::SHARE_TYPE_DONATE);
    	$this->assign('sid',$sid);
    	$this->display("ConcurAjax:shareSub");
    }

    /**
     * 获取API接口返回的内容
     */
    public function share(){
    	$data = $_POST;
    	$url = UCENTER."/oauth/pushstatuses";
    	$r = getApiContent($url, $data);
    	$this->ajaxReturn($r);
    }
}