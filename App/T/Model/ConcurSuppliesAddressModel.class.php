<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;
class ConcurSuppliesAddressModel extends Model{
    
    protected $_map = array(
        's_province' =>'provinceid',
        's_city' =>'cityid',
        's_area' =>'countyid',
        'hp_detaddr' =>'address',
        'hp_rec_name' =>'name',
        'hp_rec_phone' =>'phone',
        'hp_rec_address' =>'code',
	'id'=>'concur_id',
    );
    //自动验证
    protected $_validate = array(                                   
                                array('provinceid','number','省份不能为空',1),
                                array('cityid','number','城市不能为空',1),
                                array('countyid','number','县城不能为空',1),
                                array('address','require','地址不能为空',1),
                                array('address','unlawful','地址不能输入非法字符',1,'function'),
                                array('name','2,20','姓名错误',1,'length'),
                                array('name','unlawful','姓名不能有非法字符',1,'function'),
                                array('phone','checkMobile','手机号码不正确',1,'function'),
                                array('code','checkCode','邮编错误',1,'function'),
    );
    
    //自动完成
    protected $_auto = array (
        array('user_id','getCreator',3,'callback'), //新增数据时，设置创建者ID 
        array('update_time','time',3,'function') , // 更新的时候设置更新时间
    );
  
    //自动完成时获取用户id
    protected function getCreator(){
        return UserSession::getUser('uid');
    }
    /**
     * 某一求助人|捐助下某一申请人的收件地址
     */
    public function getAddress($id,$user_id){
    	$w=array(
    		'concur_id'=>$id,
    		'user_id'=>$user_id	
    	);
    	$data=$this->where($w)->find();
    	if($data){
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
    	}
    	return $data;
    }
    
}
