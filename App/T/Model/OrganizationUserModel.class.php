<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/2/6
 * Time: 12:31
 */

namespace T\Model;
use Think\Model;
use Lib\UserSession;

class OrganizationUserModel extends Model
{
    protected $connection =  'USER_CONTER'; //数据库连接

    /**
     * 等待审核
     */
    const STATUS_WEIT = 0;
    /**
     * 审核失败
     */
    const STATUS_FAIL = -1;
    /**
     * 审核成功
     */
    const STATUS_SUCCESS = 1;

    /**
     * 组织与组织的关系
     */
    const TYPE_ORG = 1;
    /**
     * 组织雨用户的关系
     */
    const TYPE_USER = 0;

    public function joinOrg($org, $uid)
    {
        $status = array();
        //取得是否已经存在 关系
        $orgUser = $this->where(array(
            "uid" => $uid,
            "org_id" => $org
        ))->find();
        if ($orgUser) {
            $status = $orgUser['status'];
            if ($status == self::STATUS_WEIT) {
                $resutl['status'] = "0";
                $resutl['msg'] = "您已经提交了申请，请等待组织的审核";
            } elseif ($status == self::STATUS_SUCCESS) {
                $resutl['status'] = "0";
                $resutl['msg'] = "您已经是该组织下的志愿者了，不要重复提交";
            } else {
                //更新用户请求
                $db_result = $this->data(array(
                    'status' => self::STATUS_WEIT,
                    'update_date' => time(),
                    'type' => 0
                ))->where('id=' . $orgUser['id'])->save();
            }
        } else {
            //执行加入操作
            $db_result = $this->data(array(
                    'uid' => $uid,
                    'org_id' => $org,
                    'create_date' => time(),
                    'update_date' => time(),
                    'status' => self::STATUS_WEIT,
                    'type' => self::TYPE_USER,
                    'create'=>$uid
                )
            )->add();
        }
        if ($db_result) {
            $resutl['status'] = "1";
            $resutl['msg'] = "您的请求已经提交，请等待组织的审核";
        } else if (!isset($resutl['status'])) {
            $resutl['status'] = "0";
            $resutl['msg'] = "申请失败，请稍后重试";
        }
        return $resutl;
    }
    
    /**
     * 获取用户参加的组织的信息
     * 返回组织数据和组织的数目
     */
    public function getOrgInfo($p,$pagenum,$uid){
        $start = ($p-1)*$pagenum;
        $prefix =  C('DB_PREFIX');
        $tb1 = $prefix.'organization_user';
        $tb2 = $prefix.'organization';
        $tb3 = $prefix.'user';
        $sql = "select $tb2.uid,$tb2.org_name,$tb2.summary,$tb3.photo,$tb3.provinceid,$tb3.cityid "
                ." from $tb1 left join $tb2 on $tb1.org_id=$tb2.uid "
                . "left join $tb3 on $tb1.org_id=$tb3.uid "
                ." where $tb1.uid=$uid && $tb1.status=1 && $tb1.type=0 order by $tb1.create_date desc "
                ." limit $start,$pagenum ";
        $result = $this->query($sql);
        return $result;
    }
    
    /**
     * 获取当前用户所属组织的个数
     */
    public function getOrgNum($uid){
        $map['status'] = self::STATUS_SUCCESS;
        $map['type'] = self::TYPE_USER;
        $map['uid'] = $uid;
        return $this->where($map)->count();
    }
    
    public function getDynamic($uid){
            $tag = 'dynamic'.$uid;
            if(S($tag)){
                return S($tag);
            } 
            $sql = "select * from tb_feed where uid=$uid order by id desc";
            $r = $this->query($sql);
            S($tag,$r,50);
            return $r;
    }
}