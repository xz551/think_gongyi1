<?php
namespace UC\Controller;
use Think\Controller;
use T\Common\Api\CityApi as CityApi;
use Lib\UserSession;
use Lib\City;
use Lib\User;
class VolresController extends Controller {
	public function index(){
		$uid = intval(I('get.uid'));
		if(User::isUser($uid)){
			$this->title=User::getNickname().' 的主页 | 用户中心';
			$this->assign('userId',UserSession::getUser('uid'));
			$this->assign('uid',$uid);
			$this->display("index");
		}else{
			//查看的页面不存在
			$this->title = ' 系统消息 | 用户中心';
			$this->error('查看的页面不存在');
		}
	} 
	/**
     * 获得城市列表
     */
    private function getCityList() {
        return CityApi::getDataList();
    }
	
    /**
     * ajax 获取城市数据
     */
    public function getCity() {
        $cid = I('cid');
        echo json_encode(CityApi::getDataList($cid));
    }
    //添加资源履历
    public function addResume(){
        tag('db_begin');
        $uid = UserSession::getUser('uid');
		if(empty($uid)){
			echo 0;
		}else{
			if($uid !=$_POST['userId']){
				echo -1;
			}else{
				$_POST['start_time'] = strtotime($_POST['start_time']);
				$_POST['end_time'] = strtotime($_POST['end_time']);
				$resume = D('VolunteerResume');
				$resume->create();
				if($_POST['id']){
					echo $resume->save();
				}else{
					echo $resume->add();
				}
			}
		}
    }
   
    /**
     * ajax添加修改志愿履历
     */
    public function editResume(){        
        //获取对应的城市列表
        $area = $this->getCityList();
        $this->assign('cityList', $area);
        
        //获取提交类型 1为修改，0为提交
        $type = I('type')?I('type'):0;
        $this->assign('type',$type);
        $uid = I('get.uid');

        
        //获取志愿履历
        if(I('type') == 1){
            $id = intval(I('id'));
            $resume = D('VolunteerResume')->getResume($id);
            $resume['start_time'] = date('Y-m-d',$resume['start_time']);
            $resume['end_time'] = date('Y-m-d',$resume['end_time']);        
        }
        $this->assign('resume',$resume);
        $this->display("editResume");
    }
    
    
    /**
     * ajax获取用户志愿履历
     */
    public function getResume(){       
    	$uid = I('uid');
	$p = I('get.p');
        if(empty($p)){
            $p = 1;
        }
        $pagesize = 5;
        $start = ($p-1)*$pagesize;
        $limit = $start.' , '.$pagesize;	
		$result = D('VolunteerResume')->resume($uid,$limit);
		if($result){
			foreach($result as $k=>$v){
				$result[$k]['address'] = City::getName($v['provinceid']).'&nbsp;'.City::getName($v['cityid']).'&nbsp;'.$v['address'];
				$result[$k]['start_time'] = date('Y-m-d',$v['start_time']);
				$result[$k]['end_time'] = date('Y-m-d',$v['end_time']);
			}
			$this->assign('resume',$result);
			$num = D('VolunteerResume')->getNum($uid);
			$this->assign("page",ajax_page($num,$pagesize));
			
			//判断访问的是否自己的页面
			if($uid == UserSession::getUser('uid')){
				$ismine = 1;
			}else{
				$ismine = 0;
			}
			$this->assign('ismine',$ismine);
			
			
			$this->display("getResume");
		}else{
			return 0;
		}
	
    }
    
    
}