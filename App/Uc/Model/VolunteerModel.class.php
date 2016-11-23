<?php 
namespace Uc\Model;
use Think\Model;
class VolunteerModel extends Model{
	protected $connection =  'USER_CONTER'; //数据库连接
        
        //名字，电话，证件类型，证件号码，证件图片
        protected $_map = array(         
            'smrz_name' =>'real_name',  
            'smrz_phone'=>'phone',
            'add_zhen'  => 'idcard_type',     //证件类型
            'smrz_zjnum'=>'idcard_code',
            'show_image'=>'idcard_file_name',
        );
        //自动验证
        protected $_validate = array(     
                                   array('real_name','require','真实姓名不能为空'),
                                   array('phone','require','电话号码不能为空'),
                                   array('idcard_type','require','证件类型不能为空'),                          
                                   array('idcard_code','require','证件号码不能为空'),
                                   array('idcard_file_name','require','证件图片不能为空'),
                                );
        //自动完成
        protected $_auto = array (
            array('status',10), //新增数据时，设置创建者ID
            array('apply_date','time',1,'function') , // 新增时候，设置创建时间
            array('update_date','time',3,'function'), // 新增或者修改时，都更改 更新时间
        );
        //自动完成时获取用户id
        protected function getCreator(){
            return UserSession::getUser('uid');
        }
                
        
	/**
	 * 判断身份证是否被认证使用
	 */
	public function checkCard($card){
		return $this->where('idcard_code="%s"',$card)->find();
	}
}