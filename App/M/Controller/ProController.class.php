<?php

namespace M\Controller;

use Lib\Image;
use Think\Controller;
use Lib\Image\UploadedFile;
use Common\Api\CityApi;

class ProController extends Controller {

    //项目专题信息
    public function index($id) {
        $event = M("event")->find($id);
        if ($event) {
            $this->title = $event['name'];
            $event['show_image'] = Image::getUrl($event['show_image'], array(600, 0)); // UploadedFile::getFileUrl($event['show_image'],array(600,400),'event');
            $event['event_begin_time'] = date("Y-m-d", $event['event_begin_time']);
            $event['event_end_time'] = date("Y-m-d", $event['event_end_time']);
            $event['vote_begin_time'] = date("Y-m-d", $event['vote_begin_time']);
            $event['vote_end_time'] = date("Y-m-d", $event['vote_end_time']);
            $pro = D("Project")->pro($id);
            if ($pro) {
                foreach ($pro as $key => $val) {
                    $pro[$key]['address'] = CityApi::getName($val['provinceid']) . "&nbsp;&nbsp;" . CityApi::getName($val['cityid']);
                    $pro[$key]['show_image'] = Image::getUrl($val['show_image'], array(600, 400)); // UploadedFile::getFileUrl($val['show_image'],array(600,400),'project');
                }
            }
            //项目专题下项目的个数
            $count = D("Project")->proCount($id);
            $this->assign("event", $event);
            $this->assign("pro", $pro);
            $this->assign("count", $count);
            $this->assign('pc_url', YI_JUAN . '/project/alleventlist/id/' . $id . '.html');
            $this->display("index", array("id" => $id));
        } else {
            //项目专题不存在
            redirect(U('/m/Tpl/Index', 'msg=' . urlSafeBase64_encode('项目专题不存在')));
        }
    }

    //项目专题下指定项目的信息
    public function view($id) {
        $pro = D("Project")->proDetail($id);
        if ($pro) {
            $this->title = $pro['name'];
            $user = json_decode(api("/User/getById",array('uid'=>$pro['creator'])));
            $pro['nickname'] = $user->nickname;
            $pro['address'] = CityApi::getName($pro['provinceid']) . "&nbsp;-&nbsp;" . CityApi::getName($pro['cityid']);
            $pro['show_image'] = Image::getUrl($pro['show_image'], array(600, 400)); // UploadedFile::getFileUrl($pro['show_image'],array(600,400),'project');
            $feedback = D("Discuse")->feedback($id);
            if ($feedback) {
                foreach ($feedback as $key => $val) {
                    $feedback[$key]['nickname'] = $user->nickname;
                    $feedback[$key]['userphoto'] = Image::getUrl($user->photo, array(52), $user->gender==2?'user_girl':'user');
                    $feedback[$key]['create_date'] = date("Y年m月d H:i", $val['create_date']);
                    if ($val['image']) {
                        $feedback[$key]['image'] = Image::getUrl($val['image'], array(500, 300)); // UploadedFile::getFileUrl($val['image'],array(500,300),'project');
                    }
                }
            }
            $this->assign("pro", $pro);
            $this->assign("feedback", $feedback);
            $this->assign('pc_url', YI_JUAN . '/project/view/id/' . $id . '.html');
            $this->display("view", array("id" => $id));
        } else {
            //项目不存在或正在审核中(统一一个提示页面)
            redirect(U('/m/Tpl/Index', 'msg=' . urlSafeBase64_encode('项目不存在或正在审核中')));
        }
    }

    /**
     * 招募列表
     */
    public function proList() {
        $this->assign("title", '招募列表');
        $this->display('proList');
    }

    public function getProList($p = 1, $pagesize = 10, $area = 0, $label = 0) {
        layout(false);
        $name = 'prolist-' . $p . '-' . $area . '-' . $label;
        if (S($name)) {
            $prolist = S($name);
        } else {
            $proTab = C('DB_PREFIX') . 'project';
            $proJoinTab = C('DB_PREFIX') . 'project_category_server_tag_list';


            $_w["type"] = 1;
            $_w["status"] = array('in', array('100', '888'));
            if ($area) {
                $_w["$proTab.provinceid"] = $area;
            }
            $limit = ($p - 1) * $pagesize . ' , ' . $pagesize;
            if ($label) {
                $_w["$proJoinTab.server_tag_id"] = $label;
                $result = M('Project')->where($_w)->join("$proJoinTab ON $proTab.id = $proJoinTab.project_id")->limit($limit)->select();
                $num = M('Project')->where($_w)->join("$proJoinTab ON $proTab.id = $proJoinTab.project_id")->count();
            } else {
                $result = M('Project')->where($_w)->limit($limit)->select();
                $num = M('Project')->where($_w)->count();
            }
            foreach ($result as $k => $v) {
                $result[$k]['image'] = Image::getUrl($v['show_image'], array(188, 143), 'project');
                $result[$k]['ads'] = \Lib\City::getName($v['provinceid']) . ' ' . \Lib\City::getName($v['cityid']);
                if ($v['project_id']) {
                    $result[$k]['pid'] = $v['project_id'];
                } else {
                    $result[$k]['pid'] = $v['id'];
                }
            }
            $prolist['data'] = $result;
            $prolist['num'] = $num;
            S($name,$prolist,300);
        }
        $this->assign('proInfo', $prolist['data']);
        $this->assign("page", photo_page($prolist['num'], $pagesize));
        $this->display();
    }
    
    /**
     * 项目征集列表
     */
    public function collect(){
    	if(S('pro-collect')){
            $r = S('pro-collect');
        }else{
            $r = M('Event')->select();
            foreach($r as $k=>$v){
                $r[$k]['image'] = Image::getUrl($v['show_image'], array(620, 120), 'project');
            }
            S('pro-collect',$r,86400);
        }
        $this->assign('colinfo',$r);
        $this->display('collect');
    }
        
}
