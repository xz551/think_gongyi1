<?php
namespace UC\Model;
use Think\Model;
use Lib\UserSession;
class DynamicModel extends Model{
    protected $connection = 'USER_CONTER'; //数据库连接
   
    /**
     * 添加动态
     * @param $type 操作类型
     * @param $pid  操作id
     * @param $attach 根据不同的操作类型传递的附加数据,
     *          如：发布了入选名单，则传递一个uid或uid数组
     *              回复评论时传递的额外信息 array('uid'=>'回复的用户id','username'=>'回复的用户的昵称','content'=>'回复的内容')
     *              回复话题时传递的二外信息 array('content'=>'评论的内容')
     *          如果title,image,content 三个消息都可传递，则使用$arrach['title'],$arrach['image'],$arrach['content']进行传递
     *          如果信息不是新添加而是从其他地方导入，则需要传递 $attach['userid'] 和 $attach['time']
     */
    public function addDynamic($type,$pid,$attach=''){
        //项目相关数据入库
        if(array_key_exists($type,$this->getProHandle())){            
             return $this->ProHandle($type,$pid,$attach);
        }
        //活动相关数据入库
        if(array_key_exists($type,$this->getActiveHandle())){       
            return $this->ActiveHandle($type,$pid,$attach);
        }
        //小组相关数据入库
        if(array_key_exists($type,$this->getGroupHandle())){     
            return $this->GroupHandle($type,$pid,$attach);
        }
        //小组话题相关数据入库
        if(array_key_exists($type,$this->getGroupSubjectHandle())){            
            return $this->GroupSubjectHandle($type,$pid,$attach);
        }
        //求助救助相关数据入库
        if(array_key_exists($type,$this->getConcurHandle())){           
            return $this->ConcurHandle($type,$pid,$attach);
        }
        return;
    }
    
    //根据表名及id查询标题，简介，图片
    private function getInfoByTabName($tabName,$id,$url='',$titlename='title',$contentname='content',$imagename='image',$type='',$fun='',$attach){
        $r = D("T/".$tabName)->find($id); 
        $time = $attach['time']?$attach['time']:time();
        $uid = $attach['userid']?$attach['userid']:UserSession::getUser('uid');
        //获取标题，简介，图片的信息
        $result = array(
            'title'     => $r[$titlename],
            'content'   => $r[$contentname],
            'image'     => $r[$imagename],
            'time'      => $time,
            'pid'       => $id,
            'type'      => $type,
            'typename'  => $this->$fun()[$type],
            'uid'       => $uid,
        );
        //获取url地址
        if($tabName==='Ucenter'){
            $result['url'] = UCENTER.$url;
        }else{
            $result['url'] = SERVER_VISIT_URL.$url;
        }
        return $result;
    }
    
    /**
     * 根据传递的信息获取标题，简介，图片等信息
     * @param array $data 必须包含三个键title,content,image 其中根据情况content的值可以为空
     * @param number $id 操作对象的的id，如：组id,互助id等
     * @param string $type 传递的类型标识
     * @param string $fun 所属哪个类型的数组函数名
     * return array;
     */
    private function getInfoByTransfer($data,$id,$url='',$type='',$fun=''){
        if($data['time']) $time = $data['time'];
        else $time = time();
        //获取标题，简介，图片的信息
        $result = array(
            'title'     => $data['title'],
            'content'   => $data['content'],
            'image'     => $data['image'],
            'time'      => time(),
            'pid'       => $id,
            'type'      => $type,
            'typename'  => $this->$fun()[$type],
            'uid'       => UserSession::getUser('uid'),
        );
        //获取url地址
        if($data['tablename']==='Ucenter'){
            $result['url'] = UCENTER.$url;
        }else{
            $result['url'] = SERVER_VISIT_URL.$url;
        }
        return $result;
    }
    //根据用户id或id数组序列获取用户头像信息
    private function getUserPhoto($uid){
        if(is_array($uid))  $_w['uid'] = array('in',$uid);
        else                $_w['uid'] = $uid;    
        $result = D('T/User')->where($_w)->select();
        $img = '';
        foreach($result as $v){
            if(!$v['photo']) $v['photo']=' ';
            $img .= $v['uid'].':'.$v['photo'].',';
        }
        return rtrim($img, ',');
    }
    /**
     * 添加数据
     * @param array $result 要添加的数据
     * @param number $status  表示信息是否为累加信息，如果是，则根据提供的信息查询动态表中是否已经有相关信息，如果有则将信息累加到此数据中，没有则新添加
     */
    private function addData($result,$status=0){
        if($status){
            $_w = array(
                'uid'   => $result['uid'],
                'pid'   => $result['pid'],
                'type'  => $result['type'],
            );
            //根据条件查询动态表中的数据
            $r = $this->where($_w)->find();
            //如果数据存在，则累加修改content中的内容
            if($r){
                if($result['content']){
                    $_d['content'] = $r['content'].','.$result['content'];
                }
               $this->where("id=%d",$r['id'])->save($_d);
            //数据不存在，则新添加一条信息   
            }else{
                if($this->add($result)){
                    return array('status'=>1,'content'=>'success');
                }else{
                    return array('status'=>-1,'content'=>'操作失败');
                } 
            }        
        }else{
            if($this->add($result)){
                return array('status'=>1,'content'=>'success');
            }else{
                return array('status'=>-1,'content'=>'操作失败');
            }
        }
    }
    /**
     * 项目相关数据
     */
    private function ProHandle($type,$pid,$attach=''){
        //获取项目的url地址
        $url = "/project/view/id/".$pid.".html";
        //根据传递的标题，简介，图片等信息获取需要存储的动态的全部信息
        if($attach['title'] && $attach['content'] && $attach['image']){
            $attach['tablename'] = 'Project';
            $result = $this->getInfoByTransfer($attach,$pid,$url,$type,'getProHandle');
        //获取需要存储的动态的全部信息    
        }else{
            $result = $this->getInfoByTabName("Project",$pid,$url,'name','summary','show_image',$type,'getProHandle',$attach); 
        }
        //处理是发布入选名单的情况
        $result['ctype'] = 1;
        if($type === 'Primaries'){
            $img = $this->getUserPhoto($attach['uid']);     //获取发布的名单uid及对应的头像列表
            $result['ctype'] = 2;                           //stauts 1-表示普通简介 2-表示用户头像
            $result['content'] = $img;
            return $this->addData($result,1);
        }elseif($type === 'EvaluationProject'){
            $result['content'] = "评价内容: ".$attach['content'];
        }elseif($type === 'ProjectBack'){
            $result['content'] = "反馈内容: ".$attach['content'];
        }
        return $this->addData($result);
    }
    
    //活动相关数据入库
    private function ActiveHandle($type,$pid,$attach=''){
        $url = "/active/view/id/".$pid.".html";
        //根据是否传递标题，图像来调用不同的方法添加数据
        if($attach['title'] && $attach['image']){
            $attach['tablename'] = 'Active';
            $result = $this->getInfoByTransfer($attach,$pid,$url,$type,'getActiveHandle');
        }else{
            $result = $this->getInfoByTabName("Active",$pid,$url,'name','description','image',$type,'getActiveHandle',$attach);
        }
        $result['ctype'] = 0; 
        $result['content']  ='';
        return $this->addData($result);
    }
    //小组相关数据入库
    private function GroupHandle($type,$pid,$attach=''){
        $url = '/t/group/selgroup/id/'.$pid.'.html';
        if($attach['title'] && $attach['content'] && $attach['image']){
            $attach['tablename'] = 'Group';
            $result = $this->getInfoByTransfer($attach,$pid,$url,$type,'getGroupHandle');
        }else{
            $result = $this->getInfoByTabName("Group",$pid,$url,'name','introduce','image',$type,'getGroupHandle',$attach);
        }
        $result['ctype'] = 1;     
        return $this->addData($result);    
    }
    /**
     * 小组话题相关数据入库
     * @param array $attach  回复时传递的额外信息 array('uid'=>'回复的用户id','username'=>'回复的用户的昵称','content'=>'回复的内容')
     */
    private function GroupSubjectHandle($type,$pid,$attach=''){
        $url = '/t/subject/subinfo/id/'.$pid.'.html';
        if($attach['title'] && $attach['content'] && $attach['image']){
            $attach['tablename'] = 'Subject';
            $result = $this->getInfoByTransfer($attach,$pid,$url,$type,'getGroupSubjectHandle');
        }else{
            $result = $this->getInfoByTabName("Subject",$pid,$url,'title','content','image',$type,'getGroupSubjectHandle',$attach);
        }        
        $result['ctype'] = 1; 
        //评论话题
        if($type === 'SubjectBack'){
            $result['content'] = "评论内容：".$result['content'];     
        //回复话题评论    
        }elseif($type === 'CommentBack'){
            $result['content'] = $attach['uid'].':'.$attach['username'].":".$result['content']; 
            $result['ctype'] = 4;
        }    
        return $this->addData($result);   
    }
    //求助救助相关数据入库
    private function ConcurHandle($type,$pid,$attach=''){
        $url = '/t/concurinfo/index/id/'.$pid.'.html';
        if($attach['title'] && $attach['image'] && $attach['content']){
            $attach['tablename'] = 'Concur';
            $result = $this->getInfoByTransfer($attach,$pid,$url,$type,'getConcurHandle');
        }else{
            $result = $this->getInfoByTabName("Concur",$pid,$url,'title','summary','image',$type,'getConcurHandle',$attach);
        }
        $result['ctype'] = 1;     
        return $this->addData($result); 
    }
   /**
    * 项目相关操作
    */
    private function getProHandle(){
        return array(
            'ProjectCreate' =>  '创建了项目',
            'ProjectEdit'   =>  '修改了项目',
            'EvaluationProject'    =>  '评价了项目',
            'EnjoyRecruit'  =>  '报名参加招募',
            'ProjectBack'   =>  '在项目中发布了反馈',
            'Primaries'     =>  '发布了入选名单',
        );    
    }
    /**
     * 活动相关操作
     */
    private function getActiveHandle(){
        return array(
            'ActiveJoin'    =>  '报名参加活动',
            'ActiveCreate'  =>  '发起了活动',
            'ActiveEdit'    =>  '修改活动信息'
        );
    }
    /**
     * 小组相关操作
     */
    private function getGroupHandle(){
        return array(
            'GroupCreate'   =>  '创建了小组',
            'GroupEdit'     =>  '修改了小组信息',
        );       
    }
    /**
     * 小组话题相关操作
     */
    private function getGroupSubjectHandle(){
        return array(
            'SubjectCreate' =>  '发布了话题',
            'SubjectEdit'   =>  '修改了话题',
            'SubjectBack'   =>  '评论了话题',
            'CommentBack'   =>  '回复了话题评论',
        );  
    }
    /**
     * 求助救助相关操作
     */
    private function getConcurHandle(){
        return array(
            'AppealCreate'  => '创建了求助',
            'AppealEdit'    => '修改了求助',
            'AppealAccept'  => '接受了捐助申请',
            'AppealRefuse'  => '拒绝了捐助申请',
            'AppealApply'   => '提交了捐助申请',
            'AppealApplyEdit'   => '修改了捐助申请',
            'AppealRevoke'      => '撤销了捐助申请',
            'DonationCreate'  => '创建了资源',
            'DonationEdit'    => '修改了资源',
            'DonationAccept'  => '接受了资源申请',
            'DonationRefuse'  => '拒绝了资源申请',
            'DonationApply'   => '提交了资源申请',
            'DonationApplyEdit'   => '修改了资源申请',
            'DonationRevoke'      => '撤销了资源申请',
        );     
    }
    /**
     * 根据用户id获取用户的动态
     * @param number $uid 需要查询的用户的uid 
     * @param number $p 访问的第几页
     * @param number $pagesize 每页的信息的数目
     */
    public function obtainDynamic($uid,$p,$pagesize){
        $start = ($p-1)*$pagesize;
        $result['data'] = $this->where("uid=%d",$uid)->order('time desc')->limit($start,$pagesize)->select();
        $result['num'] = $this->where("uid=%d",$uid)->count();
        return $result;
    }  
}
