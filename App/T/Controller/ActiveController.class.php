<?php
namespace T\Controller;
use Think\Controller;
use Lib\UserSession;
use Lib\Image;

class ActiveController extends Controller {

    /**
     * 加载活动图片上传页
     */
    public function uploadImg() {
        tag('user_login');
        $aid = I('get.id');
        //检测是否有权限
        if (!$this->checkUserIsjoinActive($aid)) {
            $this->error("禁止操作");
        }
        //获取活动名称
        $act = M('Active')->find($aid);
        $this->assign('active', $act);
        //查询当前账户已经上传的照片
        //$this->assign('info', D('Discuse')->getActiveImg($aid));
        $this->assign('id', $aid);
        $this->display('uploadImg');
    }

    /**
     * 活动图片上传功能
     */
    public function upload() {
        tag('user_login');
        tag('db_begin');
        //检测是否有权限
        if (!$this->checkUserIsjoinActive(I('get.id'))) {
            echo json_encode(array('status' => 0, 'info' => '没有权限'));
            die();
        }
        //上传图片，并在数据库中添加记录
        $result = Image::upload('active', array(258, 190));
        echo json_encode($result);
    }

    /**
     * 删除活动照片
     */
    public function delActImg() {
        tag('user_login');
        $id = I('id');
        $dis = M('Discuse');
        $disInfo = $dis->find($id);
        //验证是否删除的自己发布的图片
        if($disInfo['user_id'] != UserSession::getUser('uid') || $disInfo['is_creator'] !=1){
            echo 0;
            die();
        }
        $actImg = M('ProjectBackExt')->where("did=%d", $id)->find();
        $img = $actImg['image'];
        $dis->startTrans();
        if ($dis->delete($id) && M('ProjectBackExt')->where("did=%d", $id)->delete()) {
            if (Image::delImage($img)) {
                $dis->commit();
                echo 1;
            } else {
                $dis->rollback();
                echo 0;
            }
        } else {
            $dis->rollback();
            echo 0;
        }
    }

    /**
     * 添加上传的照片说明
     */
    public function addActiveImg() {
        tag('user_login');
        tag('db_begin');
        foreach($_POST as $k=>$val){
            if(is_numeric($k)){
                $save[$k] = $val;   //更新的图片
            }elseif(substr($k,0,6)=='photo-'){    //新添加的图片
                $data['relation_id'] = I('aid');
                $data['is_creator'] = 1;
                $data['content'] = $val;
                $newid = $this->addD($data,substr($k,6));
            }
        }
        $id = D('Discuse')->addContent($save);
        $id = $newid?$newid:$id;
        $this->redirect('T/Active/photo', array('id' => $id, 'aid' => I('aid')));
    }
    
    /**
     * 添加信息
     */
    private function addD($data,$img){
        $discuse = D('Discuse');
        if($discuse->create($data)){
            $discuse->startTrans(); //开启事务
            $id = $discuse->add(); 
            $map['did'] = $id;
            $map['image'] = $img;
            $result['did'] = $id;
            if($id && M('ProjectBackExt')->add($map)){
                $discuse->commit();
            }else{
                $discuse->rollback();
            }
            return $id;
        }else{
            $this->error($discuse->getError());
        }
        
    }
    
    

    /**
     * 加载图片详情页
     */
    public function photo($id = 0, $aid = 0) {
        if (!(is_numeric($id) && is_numeric($aid) && $aid != 0)) {
            $this->error("禁止操作");
        }
        $act = M('Active')->find($aid); //获取活动的信息   
        if(!$act){
            $this->error("活动不存在");
        }
        $this->assign('act', $act);
        $r = D('Discuse')->getAllImg($aid);
        $this->assign('aid', $aid);
        if(!$r){
            $this->display('empty');
            return;
        }
        //查看图片是否存在
        if ($id) {
            $_t = true;
            foreach ($r as $v) {
                if ($v['id'] == $id) {
                    $_t = false;
                    break;
                }
            }
            if ($_t) {
                $this->error("查看的图片不存在");
            }
        }
        $user = UserSession::getUser();
        if ($user) {
            $this->assign('islogin', 1);
        }
        $user['photo'] = Image::getUrl($user['photo'], array(60, 60), $user['gender'] == 2 ? 'user_girl' : 'user');
        $did=$id?$id:$r[0]['id'];
        $this->assign('user', $user);        
        $this->assign('did', $did);
        $this->assign('list', $r);
        $this->display('photo');
    }

    /**
     * 获取讨论
     */
    public function discuss($p = 1, $pagesize = 10) {
        layout(false);
        $aid = I('relation_id');
        $did = I('reply_to');
        $uid = intval(I('reply_user_id', 0));
        $content = I('content');
        if (!(is_numeric($did) && is_numeric($aid))) {
            echo '禁止操作';
            die();
        }
        //添加评论 
        if (I('tag') == 1) {
            if (mb_strlen($content, 'utf8') < 2 || mb_strlen($content, 'utf8') > 140 || $content == '我来说两句...' || $content == "回复:我来说两句") {
                echo 0;
                return;
            }
            if ($this->addComment($did, $aid, $content, $uid) != 1) {
                echo -1;
                return;
            }
        }
        $map['reply_to'] = $did;
        $map['status'] = 1;
        $limit = ($p - 1) * $pagesize . ' , ' . $pagesize;
        $result = M('Discuse')->where($map)->order('create_date desc')->limit($limit)->select();
        $num = M('Discuse')->where($map)->count();
        $this->assign("page", ajax_page($num, $pagesize));
        // 获取并整理话题信息   
        if ($result) {
            $this->assign('info', $this->getDiscuss($result));
        }
        $this->assign('did', $did);
        $this->display('discuss');
    }

    /**
     * 添加评论
     * @param type $did    图片的ID
     * @param type $aid     活动ID
     * @param type $content     回复的内容
     * @param type $uid 回复用户的ID
     * @return type
     */
    private function addComment($did, $aid, $content, $uid = 0) {
        tag('user_login');
        tag('db_begin');
        $d = M('Discuse')->find($did);
        if ($d['relation_id'] != $aid) {
            return;
        }
        $uid = $uid ? $uid : $d['user_id'];
        $_w = array('reply_to' => $did, 'relation_id' => $aid, 'reply_user_id' => $uid, 'content' => $content);
        $discuse = D('Discuse');
        if (!$discuse->create($_w)) {
            return;
        } else {
            $id = $discuse->add();
            return $id?1:0;
        }
    }

    private function getDiscuss($result) {
        //获取所有参与话题的用户ID列表
        foreach ($result as $v) {
            $userList[] = $v['user_id'];
            $userList[] = $v['reply_user_id'];
        }
        $userList = array_unique($userList);
        //获取用户列表信息并整理
        $user = D('User')->getUserInfo($userList);
        foreach ($user as $v) {
            $r[$v['uid']] = $v;
        }
        //将用户信息添加进讨论中
        foreach ($result as $k => $v) {
            $result[$k]['user_id'] = $v['user_id'];
            $result[$k]['user_image'] = Image::getUrl($r[$v['user_id']]['photo'], array(60, 60), $r[$v['user_id']]['gender'] == 2 ? 'user_girl' : 'user');
            $result[$k]['username'] = $r[$v['user_id']]['nickname'];
            $result[$k]['reply_user_id'] = $v['reply_user_id'];
            $result[$k]['reply_username'] = $r[$v['reply_user_id']]['nickname'];
        }
        return $result;
    }

    /**
     * 获取图片信息
     */
    public function describe($did) {
        layout(false);
        $r = M('Discuse')->find($did);
        $user = D('User')->find($r['user_id']);
        $user['photo'] = Image::getUrl($user['photo'],array(60,60),$user['gender']==2?'user_girl' : 'user');
        $ismine = $r['user_id'] == UserSession::getUser('uid') ? 1 : 0;
        $this->assign('ismine', $ismine);
        $this->assign('user', $user);
        $this->assign('info', $r);
        $this->display('describe');
    }

    /**
     * 修改图片信息
     */
    public function editImgInfo($id, $content) {
        tag('user_login');
        $did = intval($id);
        if (!$content) {
            echo 0;
            die();
        }
        //查询修改的是否为当前用户自己的图片
        $_w = array('id' => $did, 'user_id' => UserSession::getUser('uid'), 'status' => 1);
        if (!M("Discuse")->where($_w)->count()) {
            echo -1;
            die();
        }
        $data['content'] = $content;
        M('Discuse')->where($_w)->save($data);
        echo 1;
    }

    /**
     * 删除图片
     */
    public function delImg() {
        tag('user_login');
        $did = intval(I('id'));
        //查询修改的是否为当前用户自己的图片
        $_w = array('id' => $did, 'user_id' => UserSession::getUser('uid'), 'status' => 1);
        if (!M("Discuse")->where($_w)->count()) {
            echo -1;
            die();
        }
        $data['status'] = 0;
        M('Discuse')->where($_w)->save($data);
        echo 1;
    }

    /**
     * ajax返回用户是否有发活动照片的权限
     */
    public function ajaxCheckPower() {
        $aid = intval(I('get.id'));
        $result['status'] = $this->checkUserIsjoinActive($aid) ? 1 : 0;
        $result = json_encode($result);
        $callback = $_GET['callback'];
        if ($callback) {
            echo $callback . "($result)";
        } else {
            echo $result;
        }
    }

    /**
     * 检测用户是否有发活动照片的权限
     */
    private function checkUserIsjoinActive($aid) {
            $r = M('Active')->where('id=%d && uid=%d',$aid,  UserSession::getUser('uid'))->count();
            if($r){
                return true;
            }else{
                return M('ActiveJoin')->where('active_id=%d && uid=%d && status=%d', $aid, UserSession::getUser('uid'), 1)->count();
            }
            
    }

}
