<?php
namespace Uc\Bll;
use Lib\Image;
use Lib\User;
use Lib\UserSession;
class userDynamic {
    
     //整理数据，将一对多关系，整理到一条记录中
    public static function handleInfo($data,$uid,$p,$pageSize){
	$typeArr = self::getTypeArray();
        $arr = array();
        $cacheArr = array();
        $i=0;
        foreach($data as $k=>$v){
            $v['data'] = json_decode($v['data']);
            if(array_key_exists($v['type'],$typeArr)){
                $arr[$i] = $v;  //如果处理的是单对单的关系，则直接把数据复制到结果数组中
            }else{//处理多对多的情况
                if(empty($cacheArr)){
                    $arr[$i] = $v;
                    $cacheArr['key'] = $k;
                    $cacheArr['data'] = $v;
                }else{//处理同一个任务的情况
                    if($v['pid']==$cacheArr['data']['pid'] && $v['data']->title == $cacheArr['data']['data']->title && $v['type'] == $cacheArr['data']['type']){
                        if($v['type'] == 'Primaries'){   //处理项目入选名单
                            $arr[$i]['data']->primaries_uid .= ','.$v['data']->primaries_uid;
                        }elseif($v['type'] == 'SuccessRaise'){//处理接受捐助名单
                            $arr[$i]['data']->join_id .= ','.$v['data']->join_id;
                        }
                        continue;
                    }else{//处理不同任务的情况
                        $arr[$i] = $v;
                        $cacheArr['key'] = $k;
                        $cacheArr['data'] = $v;
                    }                   
                }
            }
            $i++;
        }

        $newArr = self::getPage($arr,$p,$pageSize);
        $data['data'] = self::getOtherInto($newArr['data'],$uid);
        $data['num'] = $newArr['num'];
        return $data;
    }
    
    /**
     * 分页处理
     */
    private static function getPage($arr,$p,$pageSize){
        $count=count($arr);
	$start = ($p-1)*$pageSize;
        $end = $start+$pageSize;
	//确定最后一页的数据数目
        if($count%$pageSize != 0){
            if($p == intval($count/$pageSize)+1){
                $end = $start+($count%$pageSize);
            }
        }
        if($count == 0){
            $start = 0;
            $end = 0;
        }
        
        $r = array();
        for($i=$start;$i<$end;$i++){
            $r[$i] = $arr[$i];
            //判断用户是组织还是用户
            $r[$i]['isorg'] =  User::isOrg($arr[$i]['uid'])?1:0;
        }
        $result['num'] = $count;
        $result['data'] = $r;
        return $result;
    }
    
     /**
     * 补充数据，将数据填补为最终显示需要的格式
     * $page 显示第几页
     * $num 每页显示的条数
     */
    public static function getOtherInto($data,$uid){
        $arr = array();
        $userPhoto=self::getUserPhoto($uid);
        $k = 0;
        foreach($data as $v){
            $arr[$k]['pid'] = $v['pid'];        //获取操作对象的id（如项目ID，活动ID等）
            $arr[$k]['type'] = $v['type'];      //获取操作的类型
            $arr[$k]['time'] = date("Y-m-d H:i:s",$v['create_date']);
            $arr[$k]['userPhoto'] = $userPhoto['photo'];
            $arr[$k]['nickname'] = $userPhoto['nickname']; 
            $arr[$k]['uid'] = $userPhoto['uid'];
            $info = $v['data'];
            //获取项目，活动，小组，话题等的信息 
            if(array_key_exists($v['type'],self::getProArray())){  //项目信息   	
                self::getInfo($arr[$k], 'project','getProUrl',$v,$info,'Project',array('name','show_image'));
                //如果是反馈，获取反馈内容
                if($v['type'] == 'ProjectBack'){
                    $map['user_id'] = $v['uid'];
                    $map['relation_id'] = $v['pid'];
                    $map['type'] = 3;
                    $map['create_date'] = $v['create_date'];
                    $r = M('Discuse')->where($map)->find();
                    $arr[$k]['introduce'] = $r['content'];
                }
            }elseif(array_key_exists($v['type'],self::getActArray())){ //活动信息
                self::getInfo($arr[$k], 'active','getActUrl',$v,$info,'Active');
                if($v['type'] == 'ActiveEdit'){ //修改活动时不显示活动信息
                    $arr[$k]['introduce'] = '';
                }
            }elseif(array_key_exists($v['type'],self::getGroupArray())){//小组信息
                self::getInfo($arr[$k], 'user','getGroupUrl',$v,$info,'Group');
            }elseif(array_key_exists($v['type'],self::getSubjectArray())){//话题信息
                self::getInfo($arr[$k], 'user','getSubjectUrl',$v,$info,'Subject',array('title','image'));
                if($v['data']->id){
                    $u = D('T/User')->find($v['data']->id);
                    $username = $u['nickname'];  
                    $arr[$k]['introduce'] = "回复".$username."： ".$v['data']->content;	  
                }elseif($v['type'] == 'SubjectBack'){                  
                    $arr[$k]['introduce'] = "回复： ".$v['data']->content;
                }
            }
            if($v['type'] == 'Primaries'){
                $user = D('T/User')->getUserInfo($v['data']->primaries_uid);
                $arr[$k]['user'] = self::getUserImageInfo($user);
                $type = self::getOneToMayArray();
                $arr[$k]['actionName'] = $type[$v['type']];
            }elseif($v['type'] == 'SuccessRaise'){
                $user = D('T/User')->getUserInfo($v['data']->join_id);
                $arr[$k]['user'] = self::getUserImageInfo($user);
                $type = self::getOneToMayArray();
                $arr[$k]['actionName'] = $type[$v['type']];
            }else{
            	$type = self::getTypeArray();
            	$arr[$k]['actionName'] = $type[$v['type']];
            }
            $k++;
        }
	return $arr;
    }
    
    /**
     * 
     * @param type $arr     需要修改的数组引用
     * @param type $defaultImg 默认图片
     * @param type $fun  获取路径的函数名称
     * @param type $v  
     * @param type $info    
     * @param type $tab 表名称
     */
    private static function getInfo(&$arr,$defaultImg,$fun,$v,$info,$tab,$w=array('name','image')){
        $arr['image'] = Image::getUrlThumbCenter($info->image, array(122),$defaultImg);
        $arr['title'] = $info->title;
        $arr['introduce'] = $info->introduce;
        $arr['url'] = self::$fun($v['pid']);
        if(!($info->image && $info->title)){
            $r = D('T/'.$tab)->find($v['pid']);
            $arr['image'] = Image::getUrlThumbCenter($r[$w[1]], array(122),$defaultImg);
            $arr['title'] = $r[$w[0]];
        }
    }
    
    
    private static function getUserImageInfo($user){
        $userArr = array();    
        foreach($user as $key=>$val){
            $default = ( $val['gender']==2)?'user_girl':'user';
            $userArr[$val['uid']]['image'] = Image::getUrlThumbCenter($val['photo'], array(50),$default);
            $userArr[$val['uid']]['url'] = self::getUserUrl($val['id']);
            $userArr[$val['uid']]['nickname']=$val['nickname'];
        }
        return $userArr;
    }
    
    //根据用户id获取用户信息
    private static function getUserPhoto($uid){
    	$uid = array($uid);
        $result = D('T/User')->getUserInfo($uid);
        $default = ( $result[0]['gender']==2)?'user_girl':'user';
        $result[0]['photo'] =Image::getUrlThumbCenter($result[0]['photo'], array(62),$default);
        return $result[0];
    }
    
  
    //一对一的关系
    public static function getTypeArray(){
        return array(
            'ProjectCreate' =>  '创建了项目',
            'ProjectEdit'   =>  '修改了项目',
            'EvaluationProject'    =>  '评价了项目',
            'ProjectBack'   =>  '在项目中发布了反馈',
            'EnjoyRecruit'  =>  '报名参加招募',
            'ActiveJoin'    =>  '报名参加活动',
            'ActiveCreate'  =>  '发起了活动',
            'ActiveEdit'    =>  '修改活动信息',
            'ApplyRaise'    =>  '希望认捐求助',
            
            'GroupCreate'     =>  '创建了小组',
            'GroupEdit'     =>  '修改了小组信息',
            
            'SubjectCreate' =>  '发布了话题',
            'SubjectEdit'   =>  '修改了话题',
            'SubjectBack'   =>  '回复了话题',
            'CommentBack'   =>  '回复了话题',
        );
    }
    //一对多的关系
    public static function getOneToMayArray(){
        return array(
            'Primaries'     =>  '发布了入选名单',
            'SuccessRaise'  =>  '接受了捐助',
        );
    }
    
 
    
    
    //获取活动的信息的类型
    public static function getActArray(){
        return array(
            'ActiveJoin'    =>  '报名参加活动',
            'ActiveCreate'  =>  '发起了活动',
            'ActiveEdit'    =>  '修改活动信息'
        );   
    }
    
    //用户获取项目信息的类型
    public static function getProArray(){
        return array(
            'ProjectCreate' =>  '创建了项目',
            'ProjectEdit'   =>  '修改了项目',
            'EvaluationProject'    =>  '评价了项目',
            'EnjoyRecruit'  =>  '报名参加招募',
            'ApplyRaise'    =>  '希望认捐求助',
            'ProjectBack'   =>  '在项目中发布了反馈',
            'Primaries'     =>  '发布了入选名单',
            'SuccessRaise'  =>  '接受了捐助'
        );
    }
    
    //用于获取小组信息的类型
    public static function getGroupArray(){
        return array(
            'GroupCreate'     =>  '创建了小组',
            'GroupEdit'     =>  '修改了小组信息',    
        );
    }
    //用户获取话题信息的类型
    public static function getSubjectArray(){
        return array(
            'SubjectCreate' =>  '发布了话题',
            'SubjectEdit'   =>  '修改了话题',
            'SubjectBack'   =>  '回复了话题',
            'CommentBack'   =>  '回复了话题',
        );
        
    }
       
    //获取项目的URL地址
    public static function getProUrl($id){
        return YI_JUAN."/project/view/id/".$id.".html";
    }
    //获取活动的URL地址
    public static function getActUrl($id){
        return YI_JUAN."/active/view/id/".$id.".html";
    }
    //获取小组的URL
    public static function getGroupUrl($id){
        return SERVER_VISIT_URL.'/t/group/selgroup/id/'.$id.'.html';
    }
    //获取话题的URL
    public static function getSubjectUrl($id){
        return SERVER_VISIT_URL.'/t/subject/subinfo/id/'.$id.'.html';
    }
    //获取用户的URL
    public static function getUserUrl($id){
        return UCENTER."/user/index/uid/".$id.".html";
    }
    
    
    
    
    
}
