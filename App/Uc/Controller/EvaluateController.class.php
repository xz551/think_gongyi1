<?php
namespace Uc\Controller;
use Think\Controller;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
class EvaluateController extends Controller{
	public function index($uid){
		if(UserSession::getUser('uid') == $uid){
			$this->title = User::getNickname().' 的主页 | 用户中心';
			$this->assign("uid",$uid);
			$this->display("index");
		}else{
			//跳转页面(你所查看的页面不存在)
			$this->title = ' 系统消息 | 用户中心';
			$this->error('查看的页面不存在');
		}
	}
	public function ajaxGetEvaluate($uid,$p=1,$pageSize=10){
		//获取用户参与过的所有项目信息
		$list = D('T/Project')->getAllPro($uid,$p,$pageSize);
		$lists= D('T/Project')->getAllPro($uid,$p,$pageSize,$count=1);
		$listCount=count($lists);
		if($list){
			foreach ($list as $k=>$v){
				$user=json_decode(api("/User/getById",array('uid'=>$v['creator'])));
				$userPhoto=Image::getUrlThumbCenter($user->photo, array(62),$user->gender==2?'user_girl':'user');
				$list[$k]['nickname']=$user->nickname;
				$list[$k]['userPhoto']=$userPhoto;
				$list[$k]['show_image']=Image::getUrlThumbCenter($v['show_image'], array(120),'project');
				$list[$k]['create_date']=nowTime(strtotime($v['create_date']));//date('Y年m月d日 H:i',strtotime($v['create_date']));
				$list[$k]['create_time']=nowTime($v['create_time']);//date('Y年m月d日 H:i',$v['create_time']);
			}	
		}
		$this->assign("list",$list);
		$this->assign("page",ajax_page($listCount,$pageSize));
		$this->display("ajaxGetEvaluate");
	}
  //处理提交的评价内容
    public function doEvaluate($uid,$level,$pid){
    	tag('db_begin');    	  
        $content = strip_tags(I('post.content')); 
        if(mb_strlen($content)>200){
            $content = mb_substr($content,0,199,'utf-8');  
        }
        if(UserSession::getUser('uid') == $uid){
        	if(D('T/ProjectJoin')->checkPro($pid,$uid) && D('Evaluate')->checkR($uid,$pid)==0){    		
                        $r = D('Evaluate')->addEvaluate($uid,$pid,$level,$content);
                        if($r){
                            D('T/Feed')->addFeed($uid,$pid,'EvaluationProject'); 
                        }
                        echo $r;
        	}else{
        		echo 0;
        	}
        }else{
        	echo 0;
        }
    }
    //获取待评价项目数
    public function getEvalNum($uid){
		echo D('T/project')->finishProNum($uid);   	
    }
}