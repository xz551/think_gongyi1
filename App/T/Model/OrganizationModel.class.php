<?php
namespace T\Model;
use Think\Model;
use Lib\UserSession;

class OrganizationModel extends Model
{
    protected $connection =  'USER_CONTER'; //数据库连接
    public function getOrganization($uid){
            $organization=$this->where("uid=%d",$uid)->find();
            return $organization;
    }
    
    //根据id获取组织名称
    public function getOrgName($id){
        $result = $this->getOrgInfo($id);
        return $result['org_name'];
    }
    
    //根据id获取组织信息
    public function getOrgInfo($id){
        return $this->find($id);
    }
    //根据组织名称获取uid
    public function getOrgId($org_name){
    	$result = $this->where("org_name='%s'",$org_name)->find();
    	return $result['uid'];
    }
}