<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace T\Model;
use Lib\User;
use Lib\UserSession;
use Lib\Image\Image;
use Think\Model;
use Lib\Image\UploadedFile;
use Lib\City;
use Lib\ArrayHelper;
use Lib\Helper;


/**
 * Description of ActiveModel
 *
 * @author Administrator
 */
class ActiveModel extends Model {

    /**
     * 活动类型
     * 1 线上
     * 2 线下
     * @version 2013-9-16
     * @author wwpeng
     * @var unknown_type
     * ceshi 
     */
    const ACTIVE_TYPE_ONLINE = 1;
    const ACTIVE_TYPE_LINE = 2;

    /**
     * 活动状态
     * 0 待审核
     * 1 审核通过
     * -1 审核不通过
     * @version 2013-9-16
     * @author wwpeng
     * @var unknown_type
     */
    const ACTIVE_STATUS_WAITING = 0;
    const ACTIVE_STATUS_NORMAL = 1;
    const ACTIVE_STATUS_REJECT = -1;

    /**
     * 获得当前活动进行的状态
     * 0 即将开始
     * 1 进行中
     * -1 已结束
     * @version 2013-9-22
     * @author wwpeng
     * @var unknown_type
     */
    const ACTIVE_PROGRESS_WILL = 0;
    const ACTIVE_PROGRESS_ING = 1;
    const ACTIVE_PROGRESS_END = -1;

    /**
     * 报名活动是否需要联系方式
     * 1 需要
     * 0 不需要
     * @version 2013-9-26
     * @author wwpeng
     * @var unknown_type
     */
    const ACTIVE_NEED_CONTACT = 1;
    const ACTIVE_NOT_NEED_CONTACT = 0;

    /**
     * 活动图片 活动名称 发布人 地址 活动时间 活动标签 报名人数
     */
    private $_Active_Hot_Cache_key='Active_Hot_Cache_key';
    public function getHotList() {
        $data=S($this->_Active_Hot_Cache_key);
        if($data){
            return $data;
        }
        $data = M('Active')->where("status = " . self::ACTIVE_STATUS_NORMAL)->order("id desc")->limit(4)->select();
        foreach ($data as $key => $value) {
            //活动图片
            $image =\Lib\Image::getUrlThumbCenter($value['image'], array(177, 124));// UploadedFile::getFileUrl($value['image'], array(177, 124), 'active');
            //活动地址
            if ($value['type'] == self::ACTIVE_TYPE_ONLINE) {
                $area = '全国';
            } else {
                $area = City::getName($value['provinceid']) . ' ' . City::getName($value['cityid']);
            }
            $activeid=$value['id']; 
           
            //报名人数
            $count=D('ActiveJoin')->joinCount($activeid);
             
            $activeCreatorData = getApiContent(ucUrl('/api/user/safeinfo',array('uid'=>$value['uid'])),false,true);
            
            
            //活动时间
            $time = sprintf('%s - %s', date('m月d日',$value['start_time']), date('m月d日',$value['end_time']) );
            //活动标签
            $tags=D('ActiveServerTag')->getTagList($activeid,3);
           
            if(!is_array($tags)){ 
                $tags=array($tags);
            }
            $tags = ArrayHelper::arrayClean($tags);
            $tagData =getApiContent(ucUrl('/api/server/list',array('tags'=>$tags)),false,true);



            $data[$key]['image']=$image;
            $data[$key]['area']=$area;
            $data[$key]['usercount']=$count;
            $data[$key]['allcount']=D('ActiveJoin')->joinCount($activeid);
            $data[$key]['creator']=Helper::Utf8Substr($activeCreatorData['nickname'],0,13);
            $data[$key]['time']=$time;
            $data[$key]['tags']=$this->showTag($tagData);
            $data[$key]['url']=$this->activeUrl($activeid);

            $data[$key]['name']=Helper::Utf8Substr($data[$key]['name'],0,20);
        }
        S($this->_Active_Hot_Cache_key,$data,C('CACHE_TIME'));
        return $data;
    }
    /**
     * 返回格式好的标签
     * @param type $tags
     */
    private function showTag($tags){
        $s='';
        if(is_array($tags) && isset($tags['item'])){
            for ($i=0;$i<count($tags['item']);$i++){
                $s.=$tags['item'][$i]['name'];
                if($i<count($tags['item'])-1){
                    $s.='、';
                }
            }
        }
        return $s;
    }
    /**
     * 活动链接
     * 活动详情页面转以后 用 U函数
     * @param type $activeid
     * @return string
     */
    private function activeUrl($activeid){
        return YI_JUAN."/active/view/id/".$activeid.".html";
    }
    //获取用户参加的、发起的||组织发起的相关活动信息
    public function getActive($uid,$p,$pageSize,$status=self::ACTIVE_STATUS_NORMAL){
    	$start = ($p-1)*$pageSize;
    	$prefix =  C('DB_PREFIX');
    	$tb1=$prefix.'active';
    	$tb2=$prefix.'active_join';
    	$w=$uid == UserSession::getUser('uid')?" ":"&& $tb1.status=$status";
		if(User::isUser($uid)){
    		$where="left join $tb2 on $tb1.id=$tb2.active_id where $tb1.status = $status && $tb2.status=$status && $tb2.uid = $uid or $tb1.uid=$uid $w group by $tb1.id order by $tb1.create_date desc ";
    	}else if(User::isOrg($uid)){
    		$where="where $tb1.uid = $uid $w order by $tb1.create_date desc ";
    	}
    	$sql="select $tb1.id,$tb1.uid,$tb1.name,$tb1.image,$tb1.start_time,$tb1.end_time,$tb1.create_date,$tb1.status "
    		."from $tb1 $where limit $start,$pageSize";
    	$act=$this->query($sql);
    	if($act){
    		foreach ($act as $k=>$v){
		    			if($v['status']==1){
		    				$start_time=$v['start_time'];
		    				$end_time = $v['end_time'];
		    				$now_time = time();
		    				if ($now_time < $start_time) {
		    					//活动即将开始
		    					$statu =2;
		    				} else if ($now_time>=$start_time  && $now_time<=$end_time) {
		    					//活动进行中
		    					$statu =3;
		    				}else if($now_time > $end_time){
		    					//活动已结束
		    					$statu =4;
		    				}
		    				$act[$k]['status']=$statu;
		    			}
		    			$act[$k]['utype']=User::getType();
    		}
    	}
    	return $act;
    }
    public function actCount($uid,$status=self::ACTIVE_STATUS_NORMAL){
    	$prefix =  C('DB_PREFIX');
    	$tb1=$prefix.'active';
    	$tb2=$prefix.'active_join';
    	$h=$uid == UserSession::getUser('uid')?" ":"&& $tb1.status=$status";
    	$w=" $tb1.status=$status and $tb2.status=$status and $tb2.uid=$uid or $tb1.uid=$uid $h";
    	$c= $this->field('0')->join('left join '.$tb2.' on '.$tb1.'.id='.$tb2.'.active_id')
    	->where($w)->group('tb_active.id')->select();
    	return count($c);
    }
}
