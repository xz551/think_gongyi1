<?php
namespace M\Controller;
use Lib\Image;
use Think\Controller;
use Lib\Image\UploadedFile;
use Common\Api\CityApi;
class RecruitController extends Controller{
	//招募项目信息
	public function index($id){
		$rePro=D("Project")->recruitProject($id);
		$this->title=$rePro[0]['name'];
		if($rePro){
			if($rePro[0]['status']==888||$rePro[0]['status']==100){
				$org=json_decode(api("/User/getById",array('uid'=>$rePro[0]['creator'])));
				$rePro[0]['user']=$org->nickname;
				$rePro[0]['show_image']=Image::getUrl($rePro[0]['show_image'],array(600,400));// UploadedFile::getFileUrl($rePro[0]['show_image'],array(600,400),'project');
				$rePro[0]['begin_time']=date('Y-m-d',strtotime($rePro[0]['begin_time']));
				$rePro[0]['end_time']=date('Y-m-d',strtotime($rePro[0]['end_time']));
				$rePro[0]['bt']=date('Y-m-d',strtotime($rePro[0]['bt']));
				$rePro[0]['et']=date('Y-m-d',strtotime($rePro[0]['et']));
				$rePro[0]['province']=CityApi::getName($rePro[0]['provinceid']);
				$rePro[0]['city']=CityApi::getName($rePro[0]['cityid']);
				//报名项目的志愿者
				$proJoin=D("ProjectRecruitDetail")->proJoin($id);
				//报名项目的志愿者数
				$userCount=D("ProjectRecruitDetail")->getUserCount($id);
				if($userCount){
					foreach ($proJoin as $key=>$vo){
						$user=json_decode(api("/User/getById",array('uid'=>$proJoin[$key]['user_id'])));
						$proJoin[$key]['joinName']=$user->nickname;
						$proJoin[$key]['photo']= Image::getUrl($user->photo, array(52),$user->gender==2?'user_girl':'user');// UploadedFile::getFileUrl($user['photo'], array(52, 52),'user');
					}
				}
				//入选项目志愿者数
				$ruxuanCount=D("ProjectRecruitDetail")->ruxuanCount($id);
				//获取项目岗位下的报名的志愿者数
				foreach ($rePro as $k=>$v){
					$jobUserCount=D("ProjectRecruitDetail")->getJobUserCount($id,$v['jobid']);
					$rePro[$k]['jobUserCount']=$jobUserCount;
				}
				//报名状态
				$status=D("Project")->getStatus();
				//项目反馈
				$feedback=D("Discuse")->feedback($id);
				if($feedback){
					foreach ($feedback as $key=>$val){
						$feedback[$key]['nickname']=$org->nickname;
						$feedback[$key]['userphoto']= Image::getUrl($org->photo, array(50),$org->gender==2?'user_girl':'user');// UploadedFile::getFileUrl($user['photo'], array(50, 50),'user');
						$feedback[$key]['create_date']=date("Y年m月d H:i",$val['create_date']);
						if($val['image']){
							$feedback[$key]['image']=Image::getUrl($val['image'],array(500,300));// UploadedFile::getFileUrl($val['image'],array(500,300),'project');
						}
					}
				}
				$this->assign("rePro",$rePro);
				$this->assign("proJoin",$proJoin);
				$this->assign("userCount",$userCount);
				$this->assign("ruxuanCount",$ruxuanCount);
				$this->assign("status",$status);
				$this->assign("feedback",$feedback);
                $this->assign('pc_url',YI_JUAN. '/project/view/id/'.$id.'.html');
				$this->display("index",array("id"=>$id));
			}elseif($rePro[0]['status']==403){
				//此项目还在审核中，请稍后查看！
				redirect(U('/m/Tpl/Index','msg='.urlSafeBase64_encode('此项目还在审核中，请稍后查看！')));
				
			}elseif($rePro[0]['status']==404){
				//项目还没有发布成功
				redirect(U('/m/Tpl/Index','msg='.urlSafeBase64_encode('您所查看的项目还没有发布成功')));
				
			}elseif($rePro[0]['status']==-1){
				//项目不存在
				redirect(U('/m/Tpl/Index','msg='.urlSafeBase64_encode('您所查看的项目不存在')));
			} 
		}else{
			//项目不存在
			redirect(U('/m/Tpl/Index','msg='.urlSafeBase64_encode('您所查看的项目不存在')));
		}
		
	}
}