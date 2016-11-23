<?php
namespace T\Controller;
use Lib\LetvCloudV1;
use Think\Controller;
class VideoController extends Controller{
	public function index($pageSize=12,$p=1,$id=1){
        $this->title="益视频-所有视频";
		$data=D("CourseVideo")->allVideo($pageSize,$p,$id);
		$Type=M("Dict")->limit(5)->select();
		$Type[0]['dict_name']="全部";
		$this->assign("allVideo",$data['video']);
		$this->assign("page",$data['page']);
		$this->assign("Type",$Type);
		$this->assign("id",$id);
		$this->display();
	}
	//获取每一类视频；
	public function getVideo($id){
		$data=D("CourseVideo")->allVideo($pageSize=12,$p=1,$id);
		$this->ajaxReturn($data);
	}

    /**
     * 保存视频信息
     * @param $video_id 乐视视频编号
     */
    public function save($video_id){
        $letv=new LetvCloudV1();
        //取得视频信息
        $info=$letv->videoGet($video_id);
        $obj=json_decode($info);
        $video_data=array(
            'video_id'=>$video_id,
            'status'=>1,
            'duration'=>0,
            'image'=>$obj->data->img,
            'create_date'=>date('Y-m-d h:i:s',time()),
            'update_date'=>date('Y-m-d h:i:s',time()),
            'video_unique'=>$obj->data->video_unique
        );
        $video=D('Video')->where('video_id=%d',$video_id)->find();
        if($video){
            $id=$video['id'];
            //更新
            D('Video')->where('video_id=%d',$video_id)->save($video_data);
        }else{
            //保存媒体信息 新增
            $id=D('Video')->add($video_data);
        }
        if($id)
            $this->ajaxReturn(array('status'=>true,'message'=>$id));
        else
            $this->ajaxReturn(array('status'=>false,'message'=>0));
    }
	
}