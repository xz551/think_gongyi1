<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;
use Lib\Image;
class DiscuseModel extends Model {    
    //自动验证
    protected $_validate = array(     
                                array('relation_id','number','必须指定项目ID'),
                            );

    //自动完成
    protected $_auto = array ( 
        array('create_date','time',1,'function') , // 新增时候，设置创建时间 
        array('user_id','getCreator',1,'callback'), //新增数据时，设置创建者ID
        array('type',6),                            //新增的时候把类型设置为6(活动照片类型)
        array('username','getUsername',1,'callback'),
        array('userphoto','getPhoto',1,'callback'),
        array('usergender','getGender',1,'callback')
    );
    
    //自动完成时获取用户id
    protected function getCreator(){
        return UserSession::getUser('uid');
    }
    
    protected function getUsername(){
        return UserSession::getUser('nickname');
    }
    
    protected function getPhoto(){
        return UserSession::getUser('photo');
    }
    
    protected function getGender(){
        return UserSession::getUser('gender');
    }
    
    /**
     * 获取用户已经上传的活动照片
     */
    public function getActiveImg($aid,$uid='',$arr=array(258,190)){
        $uid = $uid?$uid:UserSession::getUser('uid');
        $disTab = C('DB_PREFIX').'discuse';
        $imgTab = C('DB_PREFIX').'project_back_ext';
        $sql = "select $disTab.id,$disTab.content,$imgTab.image from $disTab "
                . " inner join $imgTab on $disTab.id=$imgTab.did"
                . " where $disTab.reply_to=0 && $disTab.relation_id=$aid && $disTab.user_id= ".$uid." && $disTab.type=6 && $disTab.is_creator=1 && $disTab.status=1";
        $result = $this->query($sql);
        foreach($result as $k=>$v){
            $result[$k]['image'] = Image::getUrl($v['image'],$arr);
        }
        return $result;
    }
    
    /**
     * 获取活动所有上传的照片
     */
    public function getAllImg($aid){
        $disTab = C('DB_PREFIX').'discuse';
        $imgTab = C('DB_PREFIX').'project_back_ext';
        $sql = "select $disTab.id,$imgTab.image from $disTab "
                . " inner join $imgTab on $imgTab.did = $disTab.id "
                . " where $disTab.relation_id=$aid && $disTab.status=1 && $disTab.type=6 order by $disTab.create_date desc";
        $result =  $this->query($sql);
         foreach($result as $k=>$v){
            $result[$k]['image'] = Image::getUrl($v['image']);
            $result[$k]['simage'] = Image::getUrl($v['image'],array(90,60));
        }
        return $result;
    }
    
    /**
     * 添加上传的照片说明
     */
    public function addContent($_p){
        foreach ($_p as $k => $v) {
            if (is_int($k)) {
                //添加活动照片介绍
                $map['id'] = $k;
                $map['user_id'] = UserSession::getUser('uid'); //只能修改自己的照片说明
                $map['status'] = 1;
                $data['content'] = $v;
                $r = M('Discuse')->where($map)->save($data);
                $id = $k;
            }
        }
        return $id;
    }
    
    
}
