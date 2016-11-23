<?php
namespace M\Controller;
use Think\Controller;
use M\Session\MUserSession;
use Lib\Image\UploadedFile;
use Lib\Image;
class ConcurController  extends Controller{
    /**
     * 获取互助列表信息
     * @param int $type 互助的类型 0-求助资源， 1-提供资源
     * @param string $label    求助标签
     * @param int $province 地域
     * @param int $status 项目状态 
     * @param int $p 查询的页数
     * @param int $pagesize 每页查询的记录数
     */
    public function index($type=0,$label=0,$province=0,$status=0,$searchname='',$p=1,$pagesize=10){
    	if($_GET['actname']){
		$searchname = $_GET['actname'];
	}
     	//获取列表标签
        $list = D("CategoryServer")->order('`order`')->select();
        $this->assign('list',$list);
        //获取地域列表标签
        $_areaw['class_type'] = 1; 
        $area = M('ProvinceCity')->where($_areaw)->select();
        $this->assign('area',$area);
        $_w['type'] = $type;
        //获取标签中有1的数值
        if($label){
            $tag = 'FIND_IN_SET('.$label.',label)';
            $_w[$tag] = array('gt',0);
        }
        if($province){
            $_w['provinceid'] = $province;
        }
        if($status == 2){
            $_w['status'] = 100;
        }elseif($status == 3){
            $_w['status'] = 888;     
        }else{
            $_w['status'] = array('in','100,888');
        }
        if($searchname){
            $_w['title'] = array('like',"%$searchname%");
        }
        //求助名称、发起人、求助类型，求助状态（进行中，已结束），求助图片
        //获取互助列表信息
        $result = M('Concur')->where($_w)->limit(10)->select();
        foreach($result as $key=>$val){
            $user = D('User')->find($val['create']);
            $result[$key]['username'] = $user['nickname'];
            $result[$key]['image'] = Image::getUrl($val['image']);
            $typename = '';
            if($type){
                $ftype = "捐助";
            }else{
                $ftype = "求助";
            }
            if($val['money']>0){
                $typename .= $ftype."款项/";
            }
            if($val['is_supplies']){
                $typename .= $ftype."物资/";
            }
            
            if($val['is_service']){
               $typename .= $ftype."服务/"; 
            }
            $typename = rtrim($typename,'/');
            $result[$key]['typename'] = $typename;
        }
        $this->assign('listinfo',$result);
        $this->assign('type',$type);
        $this->display(); 
    }
    
    
}
