<?php
/**
 * Created by PhpStorm.
 * User: GangGe
 * Date: 2015/6/15
 * Time: 9:44
 */

namespace Admin\Model;


use Think\Model;

class UserModel extends Model
{

    protected $connection = "USER_CONTER";

    /** 搜索用户列表
     * @param int $uid 用户编号
     * @param string $nickname 昵称
     * @param string $real_name 真实姓名
     * @param string $phone 手机号
     * @param string $idcard_code 身份证号
     * @param string $type 类型 认证用户 普通用户 已注销用户
     * @param string $gender 性别
     * @param string $provinceid 省份
     * @param string $cityid 城市
     * @param string $countyid 地区
     * @param string $iscard 是否有身份证号码 1表示有
     * @param string $isphone 是否有手机号 1表示有
     * @param string $tags 服务类型 关注领域
     * @param int $pageIndex 查询页数
     * @param int $pageSize 每页条数
     */
    public function userlist($uid = "", $nickname = "", $real_name = "", $phone = "", $idcard_code = "", $type = "", $gender = "", $provinceid = ""
        , $cityid = '', $countyid = "", $iscard = "", $isphone = "", $tags = "", $pageIndex = 0, $pageSize = 25)
    {
        $sql = "SELECT u.uid,u.nickname,v.real_name,u.gender,u.email,u.qq,u.phone,v.idcard_code,u.type,u.`status`, v.`status` as  volunteer_status, u.create_date,cstl.tag  from tb_user as u  LEFT JOIN tb_volunteer as v on u.uid=v.uid  LEFT JOIN ( select uid,group_concat(server_tag_id) as tag from tb_user_category_server_tag_list GROUP BY uid ) as cstl  on u.uid=cstl.uid where   ";
        $where = " 1=1  ";
        if (!empty($uid)) {
            $where .= " and u.uid =$uid";
        }
        if ($nickname != "") {
            $where .= " and u.nickname like '%$nickname%'";
        }
        if ($real_name != "") {
            $where .= " and v.real_name like '%$real_name%'";
        }
        if ($phone != "") {
            $where .= " and u.phone like '%$phone%'";
        }
        if ($idcard_code != "") {
            $where .= " and v.idcard_code like '%$idcard_code%'";
        }
        if ($type == 10 || $type == 11) {
            $where .= " and u.type =$type";
        } else if ($type == -1) {
            $where .= " and u.`status`=-1";
        }
        if ($gender != "") {
            $where .= " and u.gender=$gender";
        }
        if (!empty($provinceid )) {
            $where .= " and u.provinceid=$provinceid";
        }
        if (!empty($cityid)) {
            $where .= " and u.cityid=$cityid";
        }
        if (!empty($countyid)) {
            $where .= " and u.countyid=$countyid";
        }
        if ($iscard == 1) {
            $where .= " and v.idcard_code <> '' ";
        }
        if ($isphone == 1) {
            $where .= " and u.phone <> '' ";
        }
        if ($tags != '') {
            $where .= "  and concat(',',tag,',') like '%,$tags,%' ";
        }
        $count_sql = " select count(0) as c from  tb_user as u  LEFT JOIN tb_volunteer as v on u.uid=v.uid  LEFT JOIN ( select uid,group_concat(server_tag_id) as tag from tb_user_category_server_tag_list GROUP BY uid ) as cstl  on u.uid=cstl.uid where " . $where;

        $count_result = $this->query($count_sql);
 
        $where .= " order by u.uid desc limit " . (($pageIndex - 1) * $pageSize) . "," . $pageSize."";

        $all=$this->query($sql.$where);
 
        return array("count"=>$count_result[0]['c'],"data"=>$all);
    }
}