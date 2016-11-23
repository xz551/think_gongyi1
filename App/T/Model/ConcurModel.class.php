<?php
namespace T\Model;
use Lib\User;
use Lib\Image;
use Think\Model;
use Lib\UserSession;
class ConcurModel extends Model{
     /**
     * 定义互助当前状态
     */
    const STATUS_VERIFY_DENY    = -1;       //互助审核失败
    const STATUS_EDITING        = 404;      //项目编辑中,未发布
    const STATUS_WAITFORCHECK   = 403;      //项目审核状态
    const STATUS_NORMAL         = 100;      //正常状态&招募状态
    const STATUS_ENDED          = 888;      //项目完成(已结束)
    const STATUS_CLOSED         = 889;      //项目关闭
    
    //字段映射
    protected $_map = array(         
                            'hp_title' =>'title',
                            'type_lab_require' =>'label',
                            'hp_obj' =>'server_for',
                            's_province' =>'provinceid',
                            's_city' =>'cityid',
                            's_area' =>'countyid',
                            'hp_detaddr' =>'address',
                            'act_btime' =>'start_time',
                            'act_etime' =>'end_time',
                            'imgsrc' =>'image',
    );

    //自动验证
    protected $_validate = array(     
                                array('title','2,30','标题长度必须是2到30个字符',1,'length'),
                                array('title','unlawful','标题不能输入非法字符',1,'function'), 
                                array('label','require','标签不能为空',1),
                                array('server_for','1,20','受助对象长度必须是1到20个字符',1,'length'),
                                array('server_for','unlawful','受助对象不能输入非法字符',1,'function'), 
                                array('provinceid','number','省份编号必须是整数值',1),
                                array('cityid','number','城市编号必须是整数值',1),
                                array('countyid','number','县城编号必须是整数值',1),
                                array('address','require','地址不能为空',1),
                                array('address','unlawful','地址不能输入非法字符',1,'function'),
                                array('start_time','/^\d{4}-\d{1,2}-\d{1,2}$/','开始时间不正确',1),
                                array('end_time','/^\d{4}-\d{1,2}-\d{1,2}$/','结束时间不正确',1),
                                array('image','require','图片不能为空',1),
				array('image','unlawful','图片不能为空不能有非法字符',1,'function'),
                                array('type','number','类型必须为数字',1),
    );

    //自动完成
    protected $_auto = array (
        array('status',self::STATUS_EDITING), //新增数据时，设置状态为编辑状态
        array('creator','getCreator',1,'callback'), //新增数据时，设置创建者ID
        array('label','getLabel',3,'callback'), //新增数据时，设置创建者ID
        array('create_date','time',1,'function') , // 新增时候，设置创建时间
        array('update_date','time',3,'function'), // 新增或者修改时，都更改更新时间
        array('start_time','getTime',3,'callback'), //新增数据时，设置开始时间
        array('end_time','getEndTime',3,'callback') , // 新增时候，设置结束时间
    );
  
    //自动完成时获取用户id
    protected function getCreator(){
        return UserSession::getUser('uid');
    }
    
    //处理提交的时间
    protected function getTime($t){
        return strtotime($t);
    }
    //处理提交的时间
    protected function getEndTime($t){
    	$t = strtotime($t)+24*3600-1;
        return $t;
    }
    
    //获取标签
    protected function getLabel($label){
        return rtrim($label,',');
    }
    
   
    //根据ID获取互助基本信息
    public function getConcurById($id,$iscache=0) {
        if($iscache){
            $name = 'concur_' . $id;
            S($name,null);
            $r = S($name);
            if(!$r){
                $r = $this->find($id);
                S($name, $r, 3600);
            }
        }else{
            $r = $this->find($id);
        }
        return $r;
    }
    /**
     * 根据查询条件获取求助信息
     */
    public function getConcurInfo($p,$pageSize,$status,$type,$label,$pro,$tag){
    	$start = ($p-1)*$pageSize;
    	$limit = "limit $start,$pageSize";
    	$prefix =  C('DB_PREFIX');
    	$tb1=$prefix.'concur';
    	if($tag){
    		$where = "type=1 ";
    	}else{
    		$where = "type=0 ";
    	}
    	if($status==1){
    		//求助进行中
    		$where .=" && status=".self::STATUS_NORMAL;
    	}elseif($status==-1){
    		//求助已完成
    		$where .=" && status=".self::STATUS_ENDED;
    	}else{
    		//进行中和已完成的求助
    		$where .=" && status in(".self::STATUS_ENDED.",".self::STATUS_NORMAL.")";
    	}
    	if($type==1){
    		//求款项
    		$where .=" && money !=0";
    	}elseif($type==-1){
    		//求物资
    		$where .=" && is_supplies !=0";
    	}elseif($type==-2){
    		//求服务
    		$where .=" && is_service !=0";
    	}
    	if($label){
    		$where .=" &&  concat(',',label) like '%,$label,%'";
    	} 
    	if($pro){
    		$where .=" && provinceid=$pro";
    	}
    	$sql="select * from $tb1 where $where order by update_date desc $limit";
    	return $this->query($sql);
    	
    }
    /**
     * 根据查询条件获取求助信息条数
     * 
     */
    public function getConNum($status=0,$type=0,$label=0,$pro=0,$tag){
    	$prefix =  C('DB_PREFIX');
    	$tb1=$prefix.'concur';
    	if($tag){
    		$where= "type=1";
    	}else{
    		$where ="type=0";
    	}
    	if($status==1){
    		//求助进行中
    		$where .=" && status=".self::STATUS_NORMAL;
    	}elseif($status==-1){
    		//求助已完成
    		$where .=" && status=".self::STATUS_ENDED;
    	}elseif($status==0){
    		//进行中和已完成的求助
    		$where .=" && status in(".self::STATUS_ENDED.",".self::STATUS_NORMAL.")";
    	}
    	if($type==1){
    		//求款项
    		$where .=" && money !=0";
    	}elseif($type==-1){
    		//求物资
    		$where .=" && is_supplies !=0";
    	}elseif($type==-2){
    		//求服务
    		$where .=" && is_service !=0";
    	}
    	if($label){
    		$where .=" &&  concat(',',label) like '%,$label,%'";
    	}
    	if($pro){
    		$where .=" && provinceid=$pro";
    	}
    	$sql="select count(*) from $tb1 where $where";
    	return $this->query($sql);
    }
    /**
     * 获取当前登陆用户发起的某一求助物资信息||捐物资信息
     */
    public function getConcur($id){
    	$uid=UserSession::getUser('uid');
    	$w=array(
    		'id'=>$id,
    		'creator'=>$uid
    	);
    	return $this->where($w)->find();
    }
    //更改资源项目状态为已完成
    public function updateConcur($id){
    	$w=array(
    		'id'=>$id
    	);
    	$map['status']=self::STATUS_ENDED;
    	$map['update_date']=time();
    	return $this->where($w)->save($map);
    }
    
    /**
     * 检测是否为当前用户（认证用户）发布的项目
     */ 
    public function checkIsOwnConcur($id) {
        //判断是否为认证用户
        $user = D('User')->checkAuth();
        if(!$user){
            return array('status'=>-1,'message'=>'只有认证的用户可以发布求助或资源');
        }
        //检测互助信息
        $_w = array('id' => $id, 'creator' => UserSession::getUser('uid'));
        $r = M('Concur')->where($_w)->find();
        if (!$r) {
            return array('status'=>-1,'message'=>'禁止操作');  
        }else{
            if($r['status'] == ConcurModel::STATUS_VERIFY_DENY || $r['status']==ConcurModel::STATUS_WAITFORCHECK ||  $r['status']==ConcurModel::STATUS_EDITING){
                return $r;
            }else{
                return array('status'=>-1,'message'=>'项目不可修改'); 
            }
        }
    }
}
