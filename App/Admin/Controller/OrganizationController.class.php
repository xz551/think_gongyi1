<?php
/**
 * Created by PhpStorm.
 * User: zhangzhigang
 * Date: 15-1-28
 * Time: 下午2:37
 */

namespace Admin\Controller;


use Think\Controller;

/**组织用户
 * Class OrganizationController
 * @package Admin\Controller
 */
class OrganizationController extends Controller{

    /**分页查询所有组织信息
     * @param int $p
     * @param int $pagesize
     */
    public function all($p=1,$pagesize=30,$uid='',$org_name='',$type='',$user_email='',$org_phone='',$contact=''){
        $post=array(
            'uid'=>$uid,
            'org_name'=>$org_name,
            'org_type'=>$type,
            'user_email'=>$user_email,
            'org_phone'=>$org_phone,
            'contact'=>$contact,
            'p'=>$p,
            'pagesize'=>$pagesize
        );
        $data=api('/organization/getall',$post);
        $data=json_decode($data); 
        $this->data=$data->data; 
        $page=$data->page;
        $this->pageHtml=pageHtml($page->p,$page->pageCount);
        $this->display();
    }

    /**
     * 待审核数
     */
    public function wait_count(){
        $result=api('/organization/wait_review_count');
        echo $result;

    }
}