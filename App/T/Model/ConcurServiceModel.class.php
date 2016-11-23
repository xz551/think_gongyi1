<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;
class ConcurServiceModel extends Model{
    //字段映射
    protected $_map = array(         
                            'act_btime' =>'start_time',
			    'act_etime' =>'end_time',
                            'id' =>'concur_id',
                            'service_disc'=>'summary',
    );
    
    //自动验证
    protected $_validate = array(                                 
                               array('summary','10,140','服务概述长度必须是2到30个字符',1,'length'),
                               array('start_time','/^\d{4}-\d{1,2}-\d{1,2}$/','开始时间不正确',1),
                               array('end_time','/^\d{4}-\d{1,2}-\d{1,2}$/','结束时间不正确',1),            
    );
    
    //自动完成
    protected $_auto = array (
        array('start_time','getTime',3,'callback'), //新增数据时，设置开始时间
        array('end_time','getTime',3,'callback') , // 新增时候，设置结束时间
        array('update_time','time',3,'function') , // 更新的时候设置更新时间
    );
    
    protected function getTime($t){
        return strtotime($t);
    }
    /**
     * 服务信息
     * @param unknown $id
     */
    public function service($id){
    	$prefix =  C('DB_PREFIX');
    	$tb1=$prefix.'concur';
    	$tb2=$prefix.'concur_service';
    	$w=array(
    		$tb2.'.concur_id'=>$id
    	);
    	$data=$this->field("$tb1.id,$tb1.title,$tb1.provinceid,$tb1.cityid,$tb1.countyid,$tb1.address,$tb1.type,$tb1.money,$tb1.is_service,$tb2.summary,$tb2.start_time,$tb2.end_time")
    	->join("left join $tb1 on $tb1.id=$tb2.concur_id")->where($w)->find();
    	$province = D('Common/ProvinceCity')->getCity($data['provinceid']);
    	$city = D('Common/ProvinceCity')->getCity($data['cityid']);
    	$town = D('Common/ProvinceCity')->getCity($data['countyid']);
    	if($province['id']==1 || $province['id']==2 || $province['id']==3 || $province['id']==4){
    		$data['address']=$city['class_name']."市".$town['class_name'].$data['address'];
    	}else if($province['id']==7 || $province['id']==21 || $province['id']==23 || $province['id']==31 || $province['id']==30){
    		$data['address']=$province['class_name']."自治区".$city['class_name']."市".$town['class_name'].$data['address'];
    	}else if($province['id']==32 || $province['id']==33 || $province['id']==34){
    		$data['address']=$city['class_name'].$town['class_name'].$data['address'];
    	}else{
    		$data['address']=$province['class_name']."省".$city['class_name']."市".$town['class_name'].$data['address'];
    	}
    	$data['time']=date_time($data['start_time'])."-".date_time($data['end_time']);
    	return $data;
    }
    
    
}
