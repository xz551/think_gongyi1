<?php
namespace T\Controller;
use Think\Controller;
use Lib\Image;
use Think\Upload;

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
		if($_GET['key']==''){
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_project` WHERE (status=100 OR status=888)';
	        $query = mysqli_query($con,$sql);
	        $count = mysqli_fetch_assoc($query);

	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

	        $sql='SELECT id,creator,name,provinceid,cityid,countyid,show_image,status FROM `tb_project` WHERE (status=100 OR status=888) LIMIT '. $offset . ', ' . $num;
			$query=mysqli_query($con,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				//标题
				if(mb_strlen($fetch['name'],'utf-8')>=30){
					$name=mb_substr($fetch['name'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['name'];
				}
				//地址
				$sql1='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query1=mysqli_query($con,$sql1);
				$a=mysqli_fetch_assoc($query1);
				$fetch['address']=$a['pname'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$a['cname'];
				//发起者
				$sql2='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['creator'];
				$query2=mysqli_query($con1,$sql2);
				$b=mysqli_fetch_assoc($query2);
				$fetch['creat']=$b['nickname'];
				//图片
	            $image=Image::getUrl($fetch['show_image']); 
	            $fetch['image']=$image;
	            $projectData[$fetch['id']]=$fetch;
			}
			//分页1
			if($page>2){
				$str='<a href="/T/search/search/page/1" ><<</a><a href="/T/search/search/page/'.($page-1).'"><</a>
					<a href="/T/search/search/page/'.($page-2).'">'.($page-2).'</a>
			        <a href="/T/search/search/page/'.($page-1).'">'.($page-1).'</a>
			        <a href="/T/search/search/page/'.$page.'">'.$page.'</a>';
		        if($page+1 <= $pages){
		    	   $str.='<a href="/T/search/search/page/'.($page+1).'">'.($page+1).'</a>';
		   		}
		   		if($page+2 <= $pages){
		        	$str.='<a href="/T/search/search/page/'.($page+2).'">'.($page+2).'</a>';
		        }
		        $str.='<a href="/T/search/search/page/'.($page+1).'">></a><a href="/T/search/search/page/'.$pages.'">>></a>';
			}else if($page==2){
				$str='<a href="/T/search/search/page/1" ><<</a><a href="/T/search/search/page/'.($page-1).'"><</a>
					<a href="/T/search/search/page/'.($page-1).'">'.($page-1).'</a>
					<a href="/T/search/search/page/'.$page.'">'.$page.'</a>';
				if($page+1 <= $pages){
		    	   $str.='<a href="/T/search/search/page/'.($page+1).'">'.($page+1).'</a>';
		   		}
		   		if($page+2 <= $pages){
		        	$str.='<a href="/T/search/search/page/'.($page+2).'">'.($page+2).'</a>';
		        }
		        if($page+3 <= $pages){
		        	$str.='<a href="/T/search/search/page/'.($page+3).'">'.($page+3).'</a>';
		        }
		        $str.='<a href="/T/search/search/page/'.($page+1).'">></a><a href="/T/search/search/page/'.$pages.'">>></a>';

			}else if($page==1){
				$str='<a href="/T/search/search/page/'.$page.'">'.$page.'</a>';
				if($page+1 <= $pages){
		    	   $str.='<a href="/T/search/search/page/'.($page+1).'">'.($page+1).'</a>';
		   		}
		   		if($page+2 <= $pages){
		        	$str.='<a href="/T/search/search/page/'.($page+2).'">'.($page+2).'</a>';
		        }
		        if($page+3 <= $pages){
		        	$str.='<a href="/T/search/search/page/'.($page+3).'">'.($page+3).'</a>';
		        }
		        if($page+4 <= $pages){
		        	$str.='<a href="/T/search/search/page/'.($page+4).'">'.($page+4).'</a>';
		        }
		        $str.='<a href="/T/search/search/page/'.($page+1).'">></a><a href="/T/search/search/page/'.$pages.'">>></a>';
			}
			//分页2
			/*if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a href="/T/search/search/page/1">1</a>
					  <a href="/T/search/search/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/search/page/1">1</a>
					  <a href="/T/search/seafch/page/2">2</a>
					  <a href="/T/search/search/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/search/page/1">1</a>
					  <a href="/T/search/search/page/2">2</a>
					  <a href="/T/search/search/page/3">3</a>
				      <a href="/T/search/search/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/search/page/1">1</a>
					  <a href="/T/search/search/page/2">2</a>
					  <a href="/T/search/search/page/3">3</a>
				      <a href="/T/search/search/page/4">4</a>
				      <a href="/T/search/search/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/search/page/1">1</a><a href="/T/search/search/page/2">2</a><a href="/T/search/search/page/3">3</a><a href="/T/search/search/page/4">4</a><a href="/T/search/search/page/5">5</a><a href="/T/search/search/page/'.($page+1).'">></a><a href="/T/search/search/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/search/page/1" ><<</a><a href="/T/search/search/page/'.($page-1).'"><</a>
					      <a href="/T/search/search/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/search/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/search/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/search/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/search/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/search/page/1" ><<</a><a href="/T/search/search/page/'.($page-1).'"><</a><a href="/T/search/search/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/search/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/search/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/search/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/search/page/'.$page.'">'.$page.'</a><a href="/T/search/search/page/'.($page+1).'">></a><a href="/T/search/search/page/'.$pages.'">>></a>';
				}
			}*/
		}else{
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_project` WHERE (status=100 OR status=888) AND name LIKE \'%'.$_GET['key'].'%\'';
	        $query = mysqli_query($con,$sql);
	        $count = mysqli_fetch_assoc($query);

	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

			$sql='SELECT id,creator,name,provinceid,cityid,countyid,show_image,status FROM `tb_project` WHERE (status=100 OR status=888) AND name LIKE \'%'.$_GET['key'].'%\' LIMIT '. $offset . ', ' . $num;
			$query=mysqli_query($con,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				//标题
				if(mb_strlen($fetch['name'],'utf-8')>=30){
					$name=mb_substr($fetch['name'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['name'];
				}
				//地址
				$sql1='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query1=mysqli_query($con,$sql1);
				$a=mysqli_fetch_assoc($query1);
				$fetch['address']=$a['pname'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$a['cname'];
				//发起者
				$sql2='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['creator'];
				$query2=mysqli_query($con1,$sql2);
				$b=mysqli_fetch_assoc($query2);
				$fetch['creat']=$b['nickname'];
				//图片
	            $image=Image::getUrl($fetch['show_image']); 
	            $fetch['image']=$image;
	            $projectData[$fetch['id']]=$fetch;
			}
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a id="1" href="/T/search/search/key/'.$_GET['key'].'/page/1">1</a>
					  <a id="2" href="/T/search/search/key/'.$_GET['key'].'/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/search/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/search/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/search/key/'.$_GET['key'].'/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/search/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/search/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/search/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/search/key/'.$_GET['key'].'/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/search/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/search/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/search/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/search/key/'.$_GET['key'].'/page/4">4</a>
				      <a href="/T/search/search/key/'.$_GET['key'].'/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/search/key/'.$_GET['key'].'/page/1">1</a><a href="/T/search/search/key/'.$_GET['key'].'/page/2">2</a><a href="/T/search/search/key/'.$_GET['key'].'/page/3">3</a><a href="/T/search/search/key/'.$_GET['key'].'/page/4">4</a><a href="/T/search/search/key/'.$_GET['key'].'/page/5">5</a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/search/page/1" ><<</a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.($page-1).'"><</a>
					      <a href="/T/search/search/key/'.$_GET['key'].'/key/'.$_GET['key'].'/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/search/key/'.$_GET['key'].'/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/search/key/'.$_GET['key'].'/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/search/key/'.$_GET['key'].'/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/search/key/'.$_GET['key'].'/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/search/key/'.$_GET['key'].'/page/1" ><<</a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.($page-1).'"><</a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.$page.'">'.$page.'</a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/search/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}
			}
		}
		$this->assign('str',$str);
		$this->assign('key',$_GET['key']);
		$this->assign('page',$page);
		$this->assign('pages',$pages);
		$this->assign('project',$projectData);
		$this->display();
	}
	public function sea_user(){	
		//yijuan数据库
			$con=mysqli_connect("202.43.152.140","yijuan","yijuan_admin");
			mysqli_select_db($con,"yijuan");
	        mysqli_query($con,"set names utf8");
			//ucenter数据库
	        $con1=mysqli_connect("202.43.152.140","ucenter_admin","ucenter_admin_pass_!#@");    
	        mysqli_select_db($con1,"ucenter");
	        mysqli_query($con1,"set names utf8");
		if($_GET['key']==''){
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_user` WHERE status != -1';
	        $query = mysqli_query($con1,$sql);
	        $count = mysqli_fetch_assoc($query);

	        $num = 50;
	        $pages = intval($count['c'] / $num);
	        if ($count['c'] % $num ){
	        	$pages ++;
	        }
	        $offset = ($page - 1) * $num;

	       	$sql='SELECT uid,nickname,photo,type,provinceid,cityid FROM `tb_user` WHERE status != -1 LIMIT '. $offset . ', ' . $num;
			$query=mysqli_query($con1,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				if($fetch['type']==21){
					$fetch['is_ren']='认证组织';
				}else if($fetch['type']){
					$fetch['is_ren']='认证个人';
				}
				//地址
				$sql1='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query1=mysqli_query($con,$sql1);
				$a=mysqli_fetch_assoc($query1);
				$fetch['address']=$a['pname'].'&nbsp;&nbsp;'.$a['cname'];
				//类型
				$image=Image::getUrl($fetch['photo']); 
				$fetch['photo']=$image;
				$userData[$fetch['uid']]=$fetch;	
			}
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a href="/T/search/sea_user/page/1">1</a>
					  <a href="/T/search/sea_user/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_user/page/1">1</a>
					  <a href="/T/search/sea_user/page/2">2</a>
					  <a href="/T/search/sea_user/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_user/page/1">1</a>
					  <a href="/T/search/sea_user/page/2">2</a>
					  <a href="/T/search/sea_user/page/3">3</a>
				      <a href="/T/search/sea_user/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_user/page/1">1</a>
					  <a href="/T/search/sea_user/page/2">2</a>
					  <a href="/T/search/sea_user/page/3">3</a>
				      <a href="/T/search/sea_user/page/4">4</a>
				      <a href="/T/search/sea_user/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_user/page/1">1</a><a href="/T/search/sea_user/page/2">2</a><a href="/T/search/sea_user/page/3">3</a><a href="/T/search/sea_user/page/4">4</a><a href="/T/search/sea_user/page/5">5</a><a href="/T/search/sea_user/page/'.($page+1).'">></a><a href="/T/search/sea_user/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_user/page/1" ><<</a><a href="/T/search/sea_user/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_user/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_user/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_user/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_user/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_user/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_user/page/1" ><<</a><a href="/T/search/sea_user/page/'.($page-1).'"><</a><a href="/T/search/sea_user/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_user/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_user/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_user/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_user/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_user/page/'.($page+1).'">></a><a href="/T/search/sea_user/page/'.$pages.'">>></a>';
				}
			}
		}else{
	        $page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_user` WHERE nickname LIKE \'%'.$_GET['key'].'%\'';
	        $query = mysqli_query($con1,$sql);
	        $count = mysqli_fetch_assoc($query);

	        $num = 50;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

	       	$sql='SELECT uid,nickname,photo,type,provinceid,cityid FROM `tb_user` WHERE nickname LIKE \'%'.$_GET['key'].'%\' LIMIT '. $offset . ', ' . $num;
			$query=mysqli_query($con1,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				//地址
				$sql1='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query1=mysqli_query($con,$sql1);
				$a=mysqli_fetch_assoc($query1);
				$fetch['address']=$a['pname'].'&nbsp;&nbsp;'.$a['cname'];
				//类型
				$image=Image::getUrl($fetch['photo']); 
				$fetch['photo']=$image;
				$userData[$fetch['uid']]=$fetch;	
			}
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a id="1" href="/T/search/sea_user/key/'.$_GET['key'].'/page/1">1</a>
					  <a id="2" href="/T/search/sea_user/key/'.$_GET['key'].'/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_user/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_user/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_user/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/4">4</a>
				      <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_user/key/'.$_GET['key'].'/page/1">1</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/2">2</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/3">3</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/4">4</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/5">5</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_user/page/1" ><<</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_user/key/'.$_GET['key'].'/key/'.$_GET['key'].'/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_user/key/'.$_GET['key'].'/page/1" ><<</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($page-1).'"><</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_user/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}
			}
		}
		$this->assign('str',$str);
		$this->assign('key',$_GET['key']);
		$this->assign('page',$page);
		$this->assign('pages',$pages);
		$this->assign('user',$userData);
		$this->display();
	}
	public function sea_act(){
		//yijuan数据库
			$con=mysqli_connect("202.43.152.140","yijuan","yijuan_admin");
			mysqli_select_db($con,"yijuan");
	        mysqli_query($con,"set names utf8");
			//ucenter数据库
	        $con1=mysqli_connect("202.43.152.140","ucenter_admin","ucenter_admin_pass_!#@");    
	        mysqli_select_db($con1,"ucenter");
	        mysqli_query($con1,"set names utf8");
		if($_GET['key']==''){
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_active` WHERE status=1 ';
	        $query = mysqli_query($con,$sql);
	        $count = mysqli_fetch_assoc($query);
	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

	        $sql='SELECT id,uid,name,type,image,start_time,end_time,cityid FROM `tb_active` WHERE status=1 LIMIT '. $offset . ', ' . $num;
	        $query=mysqli_query($con,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				if(mb_strlen($fetch['name'],'utf-8')>=30){
					$name=mb_substr($fetch['name'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['name'];
				}
				//地址
				$sql1='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query1=mysqli_query($con,$sql1);
				$a=mysqli_fetch_assoc($query1);
				$fetch['address']=$a['pname'].'&nbsp;&nbsp;'.$a['cname'];
				//类型
				if($fetch['type']==1){
					$fetch['type']='线上活动';
					$fetch['address']='全国';
				}else{
					$fetch['type']='线下活动';
				}
				//图片
				$image=Image::getUrl($fetch['image']);
				$fetch['image']=$image;
				//发起者
				$sql2='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['uid'];
				$query2=mysqli_query($con1,$sql2);
				$b=mysqli_fetch_assoc($query2);
				$fetch['creat']=$b['nickname'];
				//状态
				if($fetch['start_time']>time()){
					$fetch['status']=0;
				}else if($fetch['end_time']<time()){
					$fetch['status']=-1;
				}else{
					$fetch['status']=1;
				}

				$activeData[$fetch['id']]=$fetch;
			}
			//分页
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a href="/T/search/sea_act/page/1">1</a>
					  <a href="/T/search/sea_act/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_act/page/1">1</a>
					  <a href="/T/search/sea_act/page/2">2</a>
					  <a href="/T/search/sea_act/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_act/page/1">1</a>
					  <a href="/T/search/sea_act/page/2">2</a>
					  <a href="/T/search/sea_act/page/3">3</a>
				      <a href="/T/search/sea_act/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_act/page/1">1</a>
					  <a href="/T/search/sea_act/page/2">2</a>
					  <a href="/T/search/sea_act/page/3">3</a>
				      <a href="/T/search/sea_act/page/4">4</a>
				      <a href="/T/search/sea_act/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_act/page/1">1</a><a href="/T/search/sea_act/page/2">2</a><a href="/T/search/sea_act/page/3">3</a><a href="/T/search/sea_act/page/4">4</a><a href="/T/search/sea_act/page/5">5</a><a href="/T/search/sea_act/page/'.($page+1).'">></a><a href="/T/search/sea_act/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_act/page/1" ><<</a><a href="/T/search/sea_act/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_act/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_act/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_act/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_act/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_act/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_act/page/1" ><<</a><a href="/T/search/sea_act/page/'.($page-1).'"><</a><a href="/T/search/sea_act/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_act/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_act/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_act/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_act/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_act/page/'.($page+1).'">></a><a href="/T/search/sea_act/page/'.$pages.'">>></a>';
				}
			}
		}else{	
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_active` WHERE status=1 AND name LIKE \'%'.$_GET['key'].'%\'';
	        $query = mysqli_query($con,$sql);
	        $count = mysqli_fetch_assoc($query);
	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

	        $sql='SELECT id,uid,name,type,image,status,cityid FROM `tb_active` WHERE status=1 AND name LIKE \'%'.$_GET['key'].'%\' LIMIT '. $offset . ', ' . $num;
	        $query=mysqli_query($con,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				//标题
				if(mb_strlen($fetch['name'],'utf-8')>=30){
					$name=mb_substr($fetch['name'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['name'];
				}
				//地址
				$sql1='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query1=mysqli_query($con,$sql1);
				$a=mysqli_fetch_assoc($query1);
				$fetch['address']=$a['pname'].'&nbsp;&nbsp;'.$a['cname'];
				//类型
				if($fetch['type']==1){
					$fetch['type']='线上活动';
					$fetch['address']='全国';
				}else{
					$fetch['type']='线下活动';
				}
				//图片
				$image=Image::getUrl($fetch['image']);
				$fetch['image']=$image;
				//发起者
				$sql2='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['uid'];
				$query2=mysqli_query($con1,$sql2);
				$b=mysqli_fetch_assoc($query2);
				$fetch['creat']=$b['nickname'];

				$activeData[$fetch['id']]=$fetch;
			}
			//分页
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a id="1" href="/T/search/sea_act/key/'.$_GET['key'].'/page/1">1</a>
					  <a id="2" href="/T/search/sea_act/key/'.$_GET['key'].'/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_act/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_act/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_act/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/4">4</a>
				      <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_act/key/'.$_GET['key'].'/page/1">1</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/2">2</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/3">3</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/4">4</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/5">5</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_act/page/1" ><<</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_act/key/'.$_GET['key'].'/key/'.$_GET['key'].'/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_act/key/'.$_GET['key'].'/page/1" ><<</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($page-1).'"><</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_act/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}
			}
		}
		$this->assign('str',$str);
		$this->assign('key',$_GET['key']);
		$this->assign('page',$page);
		$this->assign('pages',$pages);
		$this->assign('active',$activeData);
		$this->display();
	}
	public function sea_gro(){
		//yijuan数据库
			$con=mysqli_connect("202.43.152.140","yijuan","yijuan_admin");
			mysqli_select_db($con,"yijuan");
	        mysqli_query($con,"set names utf8");
			//ucenter数据库
	        $con1=mysqli_connect("202.43.152.140","ucenter_admin","ucenter_admin_pass_!#@");    
	        mysqli_select_db($con1,"ucenter");
	        mysqli_query($con1,"set names utf8");
		if($_GET['key']==''){
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_group`';
	        $query = mysqli_query($con1,$sql);
	        $count = mysqli_fetch_assoc($query);
	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

			$sql='SELECT id,name,image,creator FROM `tb_group` LIMIT '. $offset . ', ' . $num;
			$query=mysqli_query($con1,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				if(mb_strlen($fetch['name'],'utf-8')>=30){
					$name=mb_substr($fetch['name'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['name'];
				}
				//话题数量
				$sql1='SELECT count(*) as num FROM `tb_subject` WHERE gid='.$fetch['id'];
				$query1=mysqli_query($con,$sql1);
				$count=mysqli_fetch_assoc($query1);
				$fetch['count']=$count['num'];
				//成员
				$sql2='SELECT count(*) as member FROM `tb_group_user` WHERE gid='.$fetch['id'];
				$query2=mysqli_query($con1,$sql2);
				$member=mysqli_fetch_assoc($query2);
				$fetch['member']=$member['member'];
				//组长
				$sql3='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['creator'];
				$query3=mysqli_query($con1,$sql3);
				$creat=mysqli_fetch_assoc($query3);
				$fetch['creat']=$creat['nickname'];	
				//图片
				$image=Image::getUrl($fetch['image']);
				$fetch['image']=$image;

				$groupData[$fetch['id']]=$fetch;
			}
			//分页
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a href="/T/search/sea_gro/page/1">1</a>
					  <a href="/T/search/sea_gro/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_gro/page/1">1</a>
					  <a href="/T/search/sea_gro/page/2">2</a>
					  <a href="/T/search/sea_gro/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_gro/page/1">1</a>
					  <a href="/T/search/sea_gro/page/2">2</a>
					  <a href="/T/search/sea_gro/page/3">3</a>
				      <a href="/T/search/sea_gro/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_gro/page/1">1</a>
					  <a href="/T/search/sea_gro/page/2">2</a>
					  <a href="/T/search/sea_gro/page/3">3</a>
				      <a href="/T/search/sea_gro/page/4">4</a>
				      <a href="/T/search/sea_gro/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_gro/page/1">1</a><a href="/T/search/sea_gro/page/2">2</a><a href="/T/search/sea_gro/page/3">3</a><a href="/T/search/sea_gro/page/4">4</a><a href="/T/search/sea_gro/page/5">5</a><a href="/T/search/sea_gro/page/'.($page+1).'">></a><a href="/T/search/sea_gro/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_gro/page/1" ><<</a><a href="/T/search/sea_gro/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_gro/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_gro/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_gro/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_gro/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_gro/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_gro/page/1" ><<</a><a href="/T/search/sea_gro/page/'.($page-1).'"><</a><a href="/T/search/sea_gro/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_gro/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_gro/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_gro/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_gro/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_gro/page/'.($page+1).'">></a><a href="/T/search/sea_gro/page/'.$pages.'">>></a>';
				}
			}
		}else{
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_group` WHERE name LIKE \'%'.$_GET['key'].'%\'';
	        $query = mysqli_query($con1,$sql);
	        $count = mysqli_fetch_assoc($query);
	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

			$sql='SELECT id,name,image,creator FROM `tb_group` WHERE name LIKE \'%'.$_GET['key'].'%\' LIMIT '. $offset . ', ' . $num;
			$query=mysqli_query($con1,$sql);
			while($fetch=mysqli_fetch_assoc($query)){
				if(mb_strlen($fetch['name'],'utf-8')>=30){
					$name=mb_substr($fetch['name'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['name'];
				}
				//话题数量
				$sql1='SELECT count(*) as num FROM `tb_subject` WHERE gid='.$fetch['id'];
				$query1=mysqli_query($con,$sql1);
				$count=mysqli_fetch_assoc($query1);
				$fetch['count']=$count['num'];
				//成员
				$sql2='SELECT count(*) as member FROM `tb_group_user` WHERE gid='.$fetch['id'];
				$query2=mysqli_query($con1,$sql2);
				$member=mysqli_fetch_assoc($query2);
				$fetch['member']=$member['member'];
				//组长
				$sql3='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['creator'];
				$query3=mysqli_query($con1,$sql3);
				$creat=mysqli_fetch_assoc($query3);
				$fetch['creat']=$creat['nickname'];	
				//图片
				$image=Image::getUrl($fetch['image']);
				$fetch['image']=$image;

				$groupData[$fetch['id']]=$fetch;
			}
			//分页
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a id="1" href="/T/search/sea_gro/key/'.$_GET['key'].'/page/1">1</a>
					  <a id="2" href="/T/search/sea_gro/key/'.$_GET['key'].'/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/4">4</a>
				      <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/1">1</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/2">2</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/3">3</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/4">4</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/5">5</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_gro/page/1" ><<</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_gro/key/'.$_GET['key'].'/key/'.$_GET['key'].'/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/1" ><<</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($page-1).'"><</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_gro/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}
			}
		}
		$this->assign('str',$str);
		$this->assign('key',$_GET['key']);
		$this->assign('page',$page);
		$this->assign('pages',$pages);
		$this->assign('group',$groupData);
		$this->display();
	}

	public function sea_sub(){
		//yijuan数据库
			$con=mysqli_connect("202.43.152.140","yijuan","yijuan_admin");
			mysqli_select_db($con,"yijuan");
	        mysqli_query($con,"set names utf8");
			//ucenter数据库
	        $con1=mysqli_connect("202.43.152.140","ucenter_admin","ucenter_admin_pass_!#@");    
	        mysqli_select_db($con1,"ucenter");
	        mysqli_query($con1,"set names utf8");
		if($_GET['key']==''){
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_group`';
	        $query = mysqli_query($con1,$sql);
	        $count = mysqli_fetch_assoc($query);
	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

	        $sql='SELECT id,gid,uid,title,image FROM `tb_subject` LIMIT '. $offset . ', ' . $num;
	        $query=mysqli_query($con,$sql);
	        while($fetch=mysqli_fetch_assoc($query)){
	        	if(mb_strlen($fetch['title'],'utf-8')>=30){
					$name=mb_substr($fetch['title'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['title'];
				}
	        	//图片
	        	$image=Image::getUrl($fetch['image']);
	        	$fetch['image']=$image;
	        	//创建人
	        	$sql1='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['uid'];
				$query1=mysqli_query($con1,$sql1);
				$creat=mysqli_fetch_assoc($query1);
				$fetch['creat']=$creat['nickname'];
				//所属讨论组
	        	$sql2='SELECT name FROM `tb_group` WHERE id='.$fetch['gid'];
	        	$query2=mysqli_query($con1,$sql2);
	        	$group=mysqli_fetch_assoc($query2);
	        	$fetch['group']=$group['name'];
	        	$subjectData[$fetch['id']]=$fetch;

	        }
			//分页
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a href="/T/search/sea_sub/page/1">1</a>
					  <a href="/T/search/sea_sub/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_sub/page/1">1</a>
					  <a href="/T/search/sea_sub/page/2">2</a>
					  <a href="/T/search/sea_sub/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_sub/page/1">1</a>
					  <a href="/T/search/sea_sub/page/2">2</a>
					  <a href="/T/search/sea_sub/page/3">3</a>
				      <a href="/T/search/sea_sub/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_sub/page/1">1</a>
					  <a href="/T/search/sea_sub/page/2">2</a>
					  <a href="/T/search/sea_sub/page/3">3</a>
				      <a href="/T/search/sea_sub/page/4">4</a>
				      <a href="/T/search/sea_sub/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_sub/page/1">1</a><a href="/T/search/sea_sub/page/2">2</a><a href="/T/search/sea_sub/page/3">3</a><a href="/T/search/sea_sub/page/4">4</a><a href="/T/search/sea_sub/page/5">5</a><a href="/T/search/sea_sub/page/'.($page+1).'">></a><a href="/T/search/sea_sub/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_sub/page/1" ><<</a><a href="/T/search/sea_sub/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_sub/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_sub/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_sub/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_sub/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_sub/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_sub/page/1" ><<</a><a href="/T/search/sea_sub/page/'.($page-1).'"><</a><a href="/T/search/sea_sub/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_sub/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_sub/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_sub/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_sub/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_sub/page/'.($page+1).'">></a><a href="/T/search/sea_sub/page/'.$pages.'">>></a>';
				}
			}
		}else{
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_group` WHERE name LIKE \'%'.$_GET['key'].'%\'';
	        $query = mysqli_query($con1,$sql);
	        $count = mysqli_fetch_assoc($query);
	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

	        $sql='SELECT id,gid,uid,title,image FROM `tb_subject` WHERE title LIKE \'%'.$_GET['key'].'%\' LIMIT '. $offset . ', ' . $num;
	        $query=mysqli_query($con,$sql);
	        while($fetch=mysqli_fetch_assoc($query)){
	        	if(mb_strlen($fetch['title'],'utf-8')>=30){
					$name=mb_substr($fetch['title'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['title'];
				}
	        	//图片
	        	$image=Image::getUrl($fetch['image']);
	        	$fetch['image']=$image;
	        	//创建人
	        	$sql1='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['uid'];
				$query1=mysqli_query($con1,$sql1);
				$creat=mysqli_fetch_assoc($query1);
				$fetch['creat']=$creat['nickname'];
				//所属讨论组
	        	$sql2='SELECT name FROM `tb_group` WHERE id='.$fetch['gid'];
	        	$query2=mysqli_query($con1,$sql2);
	        	$group=mysqli_fetch_assoc($query2);
	        	$fetch['group']=$group['name'];
	        	$subjectData[$fetch['id']]=$fetch;

	        }
			//分页
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a id="1" href="/T/search/sea_sub/key/'.$_GET['key'].'/page/1">1</a>
					  <a id="2" href="/T/search/sea_sub/key/'.$_GET['key'].'/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/4">4</a>
				      <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/1">1</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/2">2</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/3">3</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/4">4</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/5">5</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_sub/page/1" ><<</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_sub/key/'.$_GET['key'].'/key/'.$_GET['key'].'/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/1" ><<</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($page-1).'"><</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_sub/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}
			}
		}
		$this->assign('str',$str);
		$this->assign('key',$_GET['key']);
		$this->assign('page',$page);
		$this->assign('pages',$pages);
		$this->assign('subject',$subjectData);
		$this->display();
	}

	public function sea_res(){
		//yijuan数据库
			$con=mysqli_connect("202.43.152.140","yijuan","yijuan_admin");
			mysqli_select_db($con,"yijuan");
	        mysqli_query($con,"set names utf8");
			//ucenter数据库
	        $con1=mysqli_connect("202.43.152.140","ucenter_admin","ucenter_admin_pass_!#@");    
	        mysqli_select_db($con1,"ucenter");
	        mysqli_query($con1,"set names utf8");
		if($_GET['key']==''){
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_concur` WHERE type=1 AND (status=100 OR status=888) ';
	        $query = mysqli_query($con,$sql);
	        $count = mysqli_fetch_assoc($query);
	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

	        $sql='SELECT id,title,creator,countyid,image,status FROM `tb_concur` WHERE type=1 AND (status=100 OR status=888)  LIMIT '. $offset . ', ' . $num;
	        $query=mysqli_query($con,$sql);
	        while($fetch=mysqli_fetch_assoc($query)){
	        	if(mb_strlen($fetch['title'],'utf-8')>=30){
					$name=mb_substr($fetch['title'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['title'];
				}
	        	//图片
	        	$image=Image::getUrl($fetch['image']);
	        	$fetch['image']=$image;
	        	//创建人
	        	$sql1='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['creator'];
				$query1=mysqli_query($con1,$sql1);
				$creat=mysqli_fetch_assoc($query1);
				$fetch['creat']=$creat['nickname'];
				//地址
				$sql2='SELECT class_name as pname,cname,tname FROM `tb_province_city` inner join(SELECT class_name as cname,tname,class_parent_id as pid FROM `tb_province_city` inner join(SELECT class_name as tname,class_parent_id as cid FROM `tb_province_city` WHERE id='.$fetch['countyid'].') as t1 on id=t1.cid) as t2 on id=t2.pid';
				//$sql2='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query2=mysqli_query($con,$sql2);
				$address=mysqli_fetch_assoc($query2);
				$fetch['address']=$address['pname'].'&nbsp;&nbsp;'.$address['cname'].'&nbsp;&nbsp;&nbsp;'.$address['tname'];
				$resourceData[$fetch['id']]=$fetch;
			}
			//分页
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a href="/T/search/sea_res/page/1">1</a>
					  <a href="/T/search/sea_res/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_res/page/1">1</a>
					  <a href="/T/search/sea_res/page/2">2</a>
					  <a href="/T/search/sea_res/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_res/page/1">1</a>
					  <a href="/T/search/sea_res/page/2">2</a>
					  <a href="/T/search/sea_res/page/3">3</a>
				      <a href="/T/search/sea_res/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_res/page/1">1</a>
					  <a href="/T/search/sea_res/page/2">2</a>
					  <a href="/T/search/sea_res/page/3">3</a>
				      <a href="/T/search/sea_res/page/4">4</a>
				      <a href="/T/search/sea_res/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_res/page/1">1</a><a href="/T/search/sea_res/page/2">2</a><a href="/T/search/sea_res/page/3">3</a><a href="/T/search/sea_res/page/4">4</a><a href="/T/search/sea_res/page/5">5</a><a href="/T/search/sea_res/page/'.($page+1).'">></a><a href="/T/search/sea_res/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_res/page/1" ><<</a><a href="/T/search/sea_res/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_res/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_res/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_res/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_res/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_res/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_res/page/1" ><<</a><a href="/T/search/sea_res/page/'.($page-1).'"><</a><a href="/T/search/sea_res/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_res/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_res/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_res/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_res/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_res/page/'.($page+1).'">></a><a href="/T/search/sea_res/page/'.$pages.'">>></a>';
				}
			}
		}else{
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_concur` WHERE type=1 AND (status=100 OR status=888) AND title LIKE \'%'.$_GET['key'].'%\'';
	        $query = mysqli_query($con,$sql);
	        $count = mysqli_fetch_assoc($query);
	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

	        $sql='SELECT id,title,creator,countyid,image,status FROM `tb_concur` WHERE type=1 AND (status=100 OR status=888) AND title LIKE \'%'.$_GET['key'].'%\' LIMIT '. $offset . ', ' . $num;
	        $query=mysqli_query($con,$sql);
	        while($fetch=mysqli_fetch_assoc($query)){
	        	if(mb_strlen($fetch['title'],'utf-8')>=30){
					$name=mb_substr($fetch['title'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['title'];
				}
	        	//图片
	        	$image=Image::getUrl($fetch['image']);
	        	$fetch['image']=$image;
	        	//创建人
	        	$sql1='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['creator'];
				$query1=mysqli_query($con1,$sql1);
				$creat=mysqli_fetch_assoc($query1);
				$fetch['creat']=$creat['nickname'];
				//地址
				$sql2='SELECT class_name as pname,cname,tname FROM `tb_province_city` inner join(SELECT class_name as cname,tname,class_parent_id as pid FROM `tb_province_city` inner join(SELECT class_name as tname,class_parent_id as cid FROM `tb_province_city` WHERE id='.$fetch['countyid'].') as t1 on id=t1.cid) as t2 on id=t2.pid';
				//$sql2='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query2=mysqli_query($con,$sql2);
				$address=mysqli_fetch_assoc($query2);
				$fetch['address']=$address['pname'].'&nbsp;&nbsp;'.$address['cname'].'&nbsp;&nbsp;'.$address['tname'];
				$resourceData[$fetch['id']]=$fetch;
			}
			//分页
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a id="1" href="/T/search/sea_res/key/'.$_GET['key'].'/page/1">1</a>
					  <a id="2" href="/T/search/sea_res/key/'.$_GET['key'].'/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_res/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_res/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_res/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/4">4</a>
				      <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_res/key/'.$_GET['key'].'/page/1">1</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/2">2</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/3">3</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/4">4</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/5">5</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_res/page/1" ><<</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_res/key/'.$_GET['key'].'/key/'.$_GET['key'].'/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_res/key/'.$_GET['key'].'/page/1" ><<</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($page-1).'"><</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_res/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}
			}
		}
		$this->assign('str',$str);
		$this->assign('key',$_GET['key']);
		$this->assign('page',$page);
		$this->assign('pages',$pages);
		$this->assign('resource',$resourceData);
		$this->display();
	}

	public function sea_help(){
		//yijuan数据库
			$con=mysqli_connect("202.43.152.140","yijuan","yijuan_admin");
			mysqli_select_db($con,"yijuan");
	        mysqli_query($con,"set names utf8");
			//ucenter数据库
	        $con1=mysqli_connect("202.43.152.140","ucenter_admin","ucenter_admin_pass_!#@");    
	        mysqli_select_db($con1,"ucenter");
	        mysqli_query($con1,"set names utf8");
		if($_GET['key']==''){
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_concur` WHERE type=0 AND (status=100 OR status=888)';
	        $query = mysqli_query($con,$sql);
	        $count = mysqli_fetch_assoc($query);
	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

	        $sql='SELECT id,title,creator,countyid,image,status FROM `tb_concur` WHERE type=0 AND (status=100 OR status=888) LIMIT '. $offset . ', ' . $num;
	        $query=mysqli_query($con,$sql);
	        while($fetch=mysqli_fetch_assoc($query)){
	        	if(mb_strlen($fetch['title'],'utf-8')>=30){
					$name=mb_substr($fetch['title'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['title'];
				}
	        	//图片
	        	$image=Image::getUrl($fetch['image']);
	        	$fetch['image']=$image;
	        	//创建人
	        	$sql1='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['creator'];
				$query1=mysqli_query($con1,$sql1);
				$creat=mysqli_fetch_assoc($query1);
				$fetch['creat']=$creat['nickname'];
				//地址
				$sql2='SELECT class_name as pname,cname,tname FROM `tb_province_city` inner join(SELECT class_name as cname,tname,class_parent_id as pid FROM `tb_province_city` inner join(SELECT class_name as tname,class_parent_id as cid FROM `tb_province_city` WHERE id='.$fetch['countyid'].') as t1 on id=t1.cid) as t2 on id=t2.pid';
				//$sql2='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query2=mysqli_query($con,$sql2);
				$address=mysqli_fetch_assoc($query2);
				$fetch['address']=$address['pname'].'&nbsp;&nbsp;'.$address['cname'].'&nbsp;&nbsp;'.$address['tname'];
				
				$helpData[$fetch['id']]=$fetch;
			}
			//分页
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a href="/T/search/sea_help/page/1">1</a>
					  <a href="/T/search/sea_help/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_help/page/1">1</a>
					  <a href="/T/search/sea_help/page/2">2</a>
					  <a href="/T/search/sea_help/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_help/page/1">1</a>
					  <a href="/T/search/sea_help/page/2">2</a>
					  <a href="/T/search/sea_help/page/3">3</a>
				      <a href="/T/search/sea_help/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_help/page/1">1</a>
					  <a href="/T/search/sea_help/page/2">2</a>
					  <a href="/T/search/sea_help/page/3">3</a>
				      <a href="/T/search/sea_help/page/4">4</a>
				      <a href="/T/search/sea_help/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_help/page/1">1</a><a href="/T/search/sea_help/page/2">2</a><a href="/T/search/sea_help/page/3">3</a><a href="/T/search/sea_help/page/4">4</a><a href="/T/search/sea_help/page/5">5</a><a href="/T/search/sea_help/page/'.($page+1).'">></a><a href="/T/search/sea_help/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_help/page/1" ><<</a><a href="/T/search/sea_help/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_help/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_help/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_help/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_help/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_help/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_help/page/1" ><<</a><a href="/T/search/sea_help/page/'.($page-1).'"><</a><a href="/T/search/sea_help/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_help/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_help/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_help/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_help/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_help/page/'.($page+1).'">></a><a href="/T/search/sea_help/page/'.$pages.'">>></a>';
				}
			}
		}else{
			$page = (isset($_GET['page']) && intval($_GET['page'])) ? $_GET['page'] : 1;
	        $sql = 'SELECT COUNT(*) AS c FROM `tb_concur` WHERE type=0 AND status=(100 OR status=888) AND title LIKE \'%'.$_GET['key'].'%\'';
	        $query = mysqli_query($con,$sql);
	        $count = mysqli_fetch_assoc($query);
	        $num = 30;
	        $pages = intval($count['c'] / $num);
	        if ($count % $num)
	        $pages ++;
	        $offset = ($page - 1) * $num;

	        $sql='SELECT id,title,creator,countyid,image,status FROM `tb_concur` WHERE type=0 AND (status=100 OR status=888) AND title LIKE \'%'.$_GET['key'].'%\' LIMIT '. $offset . ', ' . $num;
	        $query=mysqli_query($con,$sql);
	        while($fetch=mysqli_fetch_assoc($query)){
	        	if(mb_strlen($fetch['title'],'utf-8')>=30){
					$name=mb_substr($fetch['title'],0,30,'utf-8').'......';
					$fetch['t_name']=$name;
				}else{
					$fetch['t_name']=$fetch['title'];
				}
	        	//图片
	        	$image=Image::getUrl($fetch['image']);
	        	$fetch['image']=$image;
	        	//创建人
	        	$sql1='SELECT nickname FROM `tb_user` WHERE uid='.$fetch['creator'];
				$query1=mysqli_query($con1,$sql1);
				$creat=mysqli_fetch_assoc($query1);
				$fetch['creat']=$creat['nickname'];
				//地址
				$sql2='SELECT class_name as pname,cname,tname FROM `tb_province_city` inner join(SELECT class_name as cname,tname,class_parent_id as pid FROM `tb_province_city` inner join(SELECT class_name as tname,class_parent_id as cid FROM `tb_province_city` WHERE id='.$fetch['countyid'].') as t1 on id=t1.cid) as t2 on id=t2.pid';
				//$sql2='SELECT class_name as pname,cname FROM `tb_province_city` inner join(SELECT class_name as cname,class_parent_id as pid FROM `tb_province_city` WHERE id='.$fetch['cityid'].') as t1 on id=t1.pid';
				$query2=mysqli_query($con,$sql2);
				$address=mysqli_fetch_assoc($query2);
				$fetch['address']=$address['pname'].'&nbsp;&nbsp;'.$address['cname'].'&nbsp;&nbsp;&nbsp;'.$address['tname'];
				$helpData[$fetch['id']]=$fetch;
			}
			//分页
			if($pages<=1){
				$str='';
			}else if($pages==2){
				$str='<a id="1" href="/T/search/sea_help/key/'.$_GET['key'].'/page/1">1</a>
					  <a id="2" href="/T/search/sea_help/key/'.$_GET['key'].'/page/2">2</a>';
			}else if($pages==3){
				$str='<a href="/T/search/sea_help/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/3">3</a>';
			}else if($pages==4){
				$str='<a href="/T/search/sea_help/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/4">4</a>';
			}else if($pages<=5){
				$str='<a href="/T/search/sea_help/key/'.$_GET['key'].'/page/1">1</a>
					  <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/2">2</a>
					  <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/3">3</a>
				      <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/4">4</a>
				      <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/5">5</a>';
			}else if($pages>5){
				if($page<=5){
					$num=intval($page/2);
					if($page%2==1){$num++;}
				       $str='<a href="/T/search/sea_help/key/'.$_GET['key'].'/page/1">1</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/2">2</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/3">3</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/4">4</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/5">5</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}else if(($page+2)>=$pages){
					$str='<a href="/T/search/sea_help/page/1" ><<</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($page-1).'"><</a>
					      <a href="/T/search/sea_help/key/'.$_GET['key'].'/key/'.$_GET['key'].'/page/'.($pages-4).'">'.($pages-4).'</a>
					      <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($pages-3).'">'.($pages-3).'</a>
					      <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($pages-2).'">'.($pages-2).'</a>
					      <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($pages-1).'">'.($pages-1).'</a>
					      <a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.$pages.'">'.$pages.'</a>';
				}else{
				       $str='<a href="/T/search/sea_help/key/'.$_GET['key'].'/page/1" ><<</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($page-1).'"><</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($page-4).'">'.($page-4).'</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($page-3).'">'.($page-3).'</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($page-2).'" class="selected">'.($page-2).'</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($page-1).'">'.($page-1).'</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.$page.'">'.$page.'</a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.($page+1).'">></a><a href="/T/search/sea_help/key/'.$_GET['key'].'/page/'.$pages.'">>></a>';
				}
			}
		}
		$this->assign('str',$str);
		$this->assign('key',$_GET['key']);
		$this->assign('page',$page);
		$this->assign('pages',$pages);
		$this->assign('help',$helpData);
		$this->display();
	}
	
}