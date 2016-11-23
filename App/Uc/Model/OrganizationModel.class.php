<?php 
namespace Uc\Model;
use Think\Model;
class OrganizationModel extends Model{
	protected $connection = 'USER_CONTER'; //数据库连接
	//添加组织数据
	public function addOrg($uid,$nickname){
		$map=array(
				'uid'=>$uid,
				'org_name'=>$nickname,
				'update_date'=>time()		
			);
		return $this->data($map)->add();
	}
}
	
	