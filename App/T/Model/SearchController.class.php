<?php
namespace T\Controller;
use Think\Controller;
class SearchController extends Controller{
	public function search(){
		
		//yijuan数据库
		$con=mysqli_connect("202.43.152.140","yijuan","yijuan_admin");
		mysqli_select_db($con,"yijuan");
        mysqli_query($con,"set names utf8");
		//ucenter数据库
        $con1=mysqli_connect("202.43.152.140","ucenter_admin","ucenter_admin_pass_!#@");    
        mysqli_select_db($con1,"ucenter");
        mysqli_query($con1,"set names utf8");
        if($_GET['type']==6){
	        //用户
	       	$sql='SELECT uid,nickname,photo,type,provinceid,cityid FROM `tb_user` WHERE nickname LIKE \'%'.$_GET['key'].'%\'';
			$query=mysqli_query($con1,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				$userData[$fetch['uid']]=$fetch;	
			}
			//$data['user']=$userData;
		}else if($_GET['type']==5){
			//小组
			$sql='SELECT id,name,image,creator FROM `tb_group` WHERE name LIKE \'%'.$_GET['key'].'%\'';
			$query=mysqli_query($con1,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				$sql1='SELECT count(*) as num FROM `tb_subject` WHERE gid='.$fetch['id'];
				$query=mysqli_query($con,$sql1);
				$count=mysqli_fetch_assoc($query);
				$fetch['count']=$count['num'];

				$sql2='SELECT count(*) as member FROM `tb_group_user` WHERE gid='.$fetch['id'];
				$query=mysqli_query($con1,$sql2);
				$member=mysqli_fetch_assoc($query);
				$fetch['member']=$member['member'];
				$groupData[$fetch['id']]=$fetch;	
			}
			$data['group']=$groupData;
		}else if($_GET['type']==3){
			//话题
			$sql='SELECT id,gid,uid,title,image FROM `tb_subject` WHERE title LIKE \'%'.$_GET['key'].'%\'';
			$query=mysqli_query($con1,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				$subData[$fetch['id']]=$fetch;	
			}
			$data['group']=$subData;
		}else if($_GET['type']==2){	
			//活动
			$sql='SELECT id,uid,name,type,provinceid,cityid FROM `tb_avtive` WHERE name LIKE \'%'.$_GET['key'].'%\'';
			$query=mysqli_query($con,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				$activeData[$fetch['id']]=$fetch;
			}
			$data['active']=$activeData;
		}else if($_GET['type']==1){
			//招募
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
            $sql = 'SELECT COUNT(*) AS c FROM `tb_project` WHERE name LIKE \'%'.$_GET['key'].'%\'';
            $query = mysqli_query($con,$sql);
            $count = mysqli_fetch_assoc($query);

            $num = 2;
            $pages = intval($count['c'] / $num);
            if ($count % $num)
            $pages ++;
            $offset = ($page - 1) * $num;

			$sql='SELECT id,creator,name,provinceid,cityid,countyid,show_image,status FROM `tb_project` WHERE name LIKE \'%'.$_GET['key'].'%\' LIMIT '. $offset . ', ' . $num;
			$query=mysqli_query($con,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				//地址
				$sql1='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query1=mysqli_query($con,$sql1);
				$a=mysqli_fetch_assoc($query1);
				$fetch['address']=$a['pname'].'&nbsp;'.$a['cname'];
				//发起者
				$sql2='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['creator'];
				$query2=mysqli_query($con1,$sql2);
				$b=mysqli_fetch_assoc($query2);
				$fetch['creat']=$b['nickname'];
				//状态
				if($fetch['status']==-1){
					$fetch['status']='项目审核失败';
				}else if($fetch['status']==404){
					$fetch['status']='项目编辑中,未发布';
				}else if($fetch['status']==403){
					$fetch['status']='项目审核状态';
				}else if($fetch['status']==100){
					$fetch['status']='正常状态';
				}else if($fetch['status']==888){
					$fetch['status']='项目完成';
				}else if($fetch['status']==889){
					$fetch['status']='项目关闭';
				}
				$projectData[$fetch['id']]=$fetch;
			}
			if($pages<=1){
				$str='';
			}else if($page==1){
				$str='<a href="/T/search/search/key/'.$_GET['key'].'/type/'.$_GET['type'].'/page/'.($page+1).'">下一页</a>&nbsp;&nbsp;&nbsp;&nbsp;
				      <a href="/T/search/search/key/'.$_GET['key'].'/type/'.$_GET['type'].'/page/'.$pages.'">尾页</a>&nbsp;&nbsp;&nbsp;&nbsp;';
			}else if($page>=$pages){
				$str='<a href="/T/search/search/key/'.$_GET['key'].'/type/'.$_GET['type'].'/page/1">首页</a>&nbsp;&nbsp;&nbsp;&nbsp;
				      <a href="/T/search/search/key/'.$_GET['key'].'/type/'.$_GET['type'].'/page/'.($page-1).'">上一页</a>&nbsp;&nbsp;&nbsp;&nbsp;';
			}else{
				$str='<a href="/T/search/search/key/'.$_GET['key'].'/type/'.$_GET['type'].'/page/1">首页</a>&nbsp;&nbsp;&nbsp;&nbsp;
				      <a href="/T/search/search/key/'.$_GET['key'].'/type/'.$_GET['type'].'/page/'.($page-1).'">上一页</a>&nbsp;&nbsp;&nbsp;&nbsp;
				      <a href="/T/search/search/key/'.$_GET['key'].'/type/'.$_GET['type'].'/page/'.($page+1).'">下一页</a>&nbsp;&nbsp;&nbsp;&nbsp;
				      <a href="/T/search/search/key/'.$_GET['key'].'/type/'.$_GET['type'].'/page/'.$pages.'">尾页</a>&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			$this->assign('str',$str);
			$this->assign('page',$page);
			$this->assign('pages',$pages);
			$this->assign('project',$projectData);
			print_r($projectData);exit;
			//$this->assign(array('project'=>$projectData,'page'=>$page,'pages'=>$pages));
		}
		//print_r($data);exit;
		$this->display();
	}
}