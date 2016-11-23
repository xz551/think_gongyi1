<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/6/30
 * Time: 16:24
 */

namespace T\Service;
use Lib\Image;
use Lib\UserSession;
use Lib\LetvCloudV1;
use T\Model\ConcurModel;
/**
 * 互助反馈操作
 * Class ConcurFeedbackService
 * @package T\Service
 */
class ConcurFeedbackService {

    /**
     * 反馈验证，验证是否允许登陆用户进行反馈
     * @param $id 互助编号
     */
    public static function feedback_verify($id){
//根据编号取得互助信息
        $concur=M('Concur')->find($id);

        if(!$concur){
            return '互助信息不存在';
        }
        //验证状态是否允许
        $status=$concur['status'];
        if($status!=ConcurModel::STATUS_NORMAL && $status!=ConcurModel::STATUS_ENDED){
            return  '该互助不能进行反馈';
        }
        //验证用户是否可以执行反馈操作
        $create_user=$concur['creator'];
        if(!self::exists_user($create_user,$id)){
            return '您没有参与或您的申请没有过，不能进行反馈';
        }
        return true;
    }

    /**
     * 验证当前当前用户是否为参与用户 创建者、申请者
     * @param $creator
     * @param $concur_id
     */
    public static function exists_user($creator,$concur_id){
        if(!UserSession::islogin()){
            return false;
        }
        if($creator!=UserSession::getUserId() ){
            //不是创建者操作验证是否为申请用户
            //验证用户是否申请过
            $c_apply=M('concur_apply')->where('concur_id=%d and userid=%d',$concur_id,UserSession::getUserId())->find();
        if( $c_apply &&
        (
            $c_apply['supplies']==2 || $c_apply['service']==2 || $c_apply['money']==2)
        ){
            return true;
        }
        //检查是否是爱心认证员
           $verify= M('concur_verify')->where(' concur_id=%d and userid=%d ',$concur_id,UserSession::getUserId())->find();
            return $verify!=false;

    }
        return true;
    }

    /**
     * 保存反馈的视频信息
     * 目前采用乐视云视频
     */
    public static function save_video(){
        $video_ids=I('video_id','');
        if(!is_array($video_ids) || empty($video_ids)){
            //没有上传视频，直接通过
            return true;
        }
        $letv=new LetvCloudV1();
        $error='';
        $save_video_id=[];
        foreach ($video_ids as $key => $video_id) {
            //根据视频id取得视频信息
            $result=$letv->videoGet($video_id);
            $obj=json_decode($result);
            $code=$obj->code;
            if($code!==0) {
                $error=$obj->message;
                break;
            }
            //保存乐视视频信息
            $data=array(
                'video_id'=>$video_id,
                'status'=>1,
                'duration'=>$obj->data->video_duration,
                'video_unique'=>$obj->data->video_unique,
                'create_date'=>time_format(),
                'update_date'=>time_format(),
                'image'=>$obj->data->img
            );
            $result=M('video')->add($data);
            if(!$result){
                $error='视频添加失败';
                break;
            }
            array_push($save_video_id,['id'=>$result,'unique'=>$obj->data->video_unique,'img'=>$obj->data->img]);
            //只上传一个视频 上传多个视频 把 break去掉
            break;
        }
        if(!empty($error)){
            return $error;
        }
        return $save_video_id;
    }

    /**
     *  保存反馈的媒体资源
     * 视频和图片
     * @param array|所有保存的视频新增编号 $video_ids 所有保存的视频新增编号
     */
    public static function save_back_ext($videos=array(),$did=0){
        //取得所有图片
        $imgs=I('img','');
        if(( !is_array($videos) &&  !is_array($imgs)) || (  empty($videos) && empty($imgs) ) ){
            return true;
        }
        $video_id_str=[];
        if(!empty($videos)){
            foreach ($videos as $key=>$value) {
                array_push($video_id_str,$value['id']);
            }
            $video_id_str=join(',',$video_id_str);
        }
        $img_str='';
        if(!empty($imgs)){
            $img_str=join(',',$imgs);
        }
        $result=M('project_back_ext')->add(array(
            'did'=>$did,
            'image'=>$img_str,
            'video'=>$video_id_str
        ));
        return $result;
    }

    /**
     * 取得反馈数据，在页面中显示
     * @param array $video_ids ajax提交数据时，返回的数据
     * @param array $data 从数据库中查询出来的数据
     * @return array
     */
    public static function get_back_data($video_ids=array(),$data=array(),$concur=array()){
        //反馈者信息 头像 昵称 编号
        $concur_type=$concur['type'];
        $concur_creator=$concur['creator'];
        if($data){
            $uid=$data['user_id'];
            $photo=$data['userphoto'];
            $nickname=$data['username'];
            $gender=$data['usergender'];
            $photo=Image::getUrl($photo,array(60),$gender==2?'user_girl':'user');

            $supplies=$data['supplies'];
            $money=$data['money'];
            $service=$data['service'];
            //是否参与
            $is_apple=$supplies==2 || $money==2 || $service==2;
            //是否认证
            $verify=$data['verify'];


            //判断反馈者的角色
            if($concur_creator==$uid){
                $role='发起人';
            }else if($is_apple && $verify){
                $role= ($concur_type==0?'捐助人':'申请人').'&认证员';
            }else if($is_apple){
                $role= ($concur_type==0?'捐助人':'申请人');
            }else if($verify){
                $role='认证员';
            }

        }else{
            $uid=UserSession::getUserId();
            $photo=UserSession::getUser('photo');
            $nickname=UserSession::getUser('nickname');
            $gender=UserSession::getUser('gender');
            $photo=Image::getUrl($photo,array(60),$gender==2?'user_girl':'user');
            //判断用户角色
            //当前用户编号
            if($concur_creator==UserSession::getUserId()){
                $role='发起者';
            }else{
                //查询是否认证过
                //SELECT ca.userid as apply,cv.userid as verify from tb_concur_apply as  ca
                //LEFT JOIN tb_concur_verify as cv on ca.userid=cv.userid
                $verify=M('concur_verify')->where('concur_id=%d and userid=%d ',I('relation_id'),UserSession::getUserId())->find();
                $apple=M('concur_apply')->where('concur_id=%d and userid=%d ',I('relation_id'),UserSession::getUserId())->find();

                $is_apple=$apple['service']==2 || $apple['supplies']==2 || $apple['money']==2 ;
                $is_verify= $verify!=false;
                if( $is_apple  && $is_verify){
                    $role=  ($concur_type==0?'捐助人':'申请人').'&认证员';
                }elseif($is_apple){
                    $role=($concur_type==0?'捐助人':'申请人');
                }elseif($is_verify){
                    $role='认证员';
                }
            }
        }
        //反馈内容
        $content=empty($data)? I('content') : $data['content'];
        $feedback_img=[];
        $imgs=[];
        //反馈视频 img unique
        $feedback_video=[];
        if(empty($data)){
            //反馈图片
            $imgs=I('img','');
        }else{
            //取得反馈信息的媒体信息
            $back_ext=M('project_back_ext')->find($data['id']);
            if($back_ext){
                //取得反馈信息中的图片资源
                $back_imgs=$back_ext['image'];
                if($back_imgs){
                    $imgs=explode(',',$back_imgs);
                }
                //取得反馈信息中的视频资源 video_unique 和缩略图
                $back_video=$back_ext['video'];
                if($back_video){
                    $feedback_video=self::_get_video_info($back_video);
                }
            }
        }
        if(!empty($imgs)){
            foreach ($imgs as $img) {
                array_push($feedback_img, array(Image::getUrl($img,array(115)),Image::getUrl($img)));
            }
        }
        if(!empty($video_ids) && is_array($video_ids)){
            foreach ($video_ids as $key => $value) {
                array_push($feedback_video,array('img'=>$value['img'],'unique'=>$value['unique']));
            }
        }
        //[]为php5.4中的写法
        return ['nickname'=>$nickname,'uid'=>$uid,'photo'=>$photo,'content'=>$content,'feedback_img'=>$feedback_img,'feedback_video'=>$feedback_video,'time'=>time_format(),'role'=>$role];
    }

    /**
     * 返回视频资源的video_unique和缩略图
     * @param $video_ids 视频编号集合
     */
    private function _get_video_info($video_ids){
        //http://i3.letvimg.com/img/201203/23/noimg_h.jpg
        //default.jpg
        $feedback_video=M('video')->where('id in ('.$video_ids.')')->field('id,video_id,video_unique,image')->select();

        $return=array();
        if($feedback_video){
            $letv=new LetvCloudV1();
	    
            foreach ($feedback_video as $key => $value) {
                $unique=$value['video_unique'];
                $image=$value['image'];
                if(empty($unique) || strtolower($image)=='default.jpg' || stripos($image,'noimg_h.jpg')!==false ){
                    //没有unique或者是默认缩略图，要从接口中查找最新的。
                    $video_id=$value['video_id'];
                    $video_info=json_decode( $letv->videoGet($video_id));
                    $code=$video_info->code;
                    if($code==0){
                        $data=$video_info->data;
                        $img=$data->img;
                        if(stripos($image,'noimg_h.jpg')!==false ){
                            //不是默认缩略图 更新数据 
			                $up_data=array('video_unique'=>$data->video_unique,'image'=>$img);
			     
                            M('video')->where('id='.$value['id'])->save($up_data);
			     
                            array_push($return,array('img'=>$img,'unique'=>$data->video_unique));
                        }
                    }
                }else{
                    array_push($return,array('img'=>$image,'unique'=>$unique));
                }
            }
            return $return;
        }
    }
}