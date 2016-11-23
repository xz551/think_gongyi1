<?php

namespace T\Controller;


use Lib\Ftp;
use Think\Controller;
use Lib\Sms;
use Lib\Idcard;
use Lib\UserSession;
use Lib\Image;

class Project2Controller extends Controller
{

    public function info($id = 0)
    {
        $uid = UserSession::getUser('uid');

        //获取推荐项目信息，登录的用户通过接口获取推荐项目，没有登录的用户获取最新的项目
        if ($uid) {
            //$recommendurl = "http://123.139.159.10:8080/VolunteerRecommender/RecomendServlet?userID=$uid&size=5";
        } else {
            //获取最新发布的项目
        }

        //获取当前页面的url地址
        $text = SERVER_VISIT_URL . '/t/project/info?id=' . $id;
        //获取二维码
        $qrcode = SERVER_VISIT_URL . '/tapi/index/ewm?text=' . $text;
        $this->assign('qrcode', $qrcode);
        //获取招募项目id
        if (!$id) {
            $this->error("项目id不能为空!");
        } else {
            $id = intval($id);
        }
        //获取招募项目的岗位信息
        $job = M('ProjectRecruitJob')->where("project_id=%d", $id)->select();
        //获取岗位名称，获取报名人数，获取岗位需要人数
        foreach ($job as $k => $v) {
            $_w['job_id'] = $v['id'];
            $_w['status'] = array('neq', 101);   //101表示取消项目参与
            $prd = M('ProjectRecruitDetail')->where($_w)->select();
            $job[$k]['registnum'] = count($prd);
            //获取报名的用户列表
            foreach ($prd as $vol) {
                $registList[] = $vol['user_id'];
            }
            $registwhere['uid'] = array('IN', $registList);
            $registUser = D('User')->where($registwhere)->select();

            //获取入选的用户列表
            $_w['status'] = 201;
            $inprd = M('ProjectRecruitDetail')->where($_w)->select();
            foreach ($inprd as $vol) {
                $inputList[] = $vol['user_id'];
            }
            $inputwhere['uid'] = array('IN', $inputList);
            $inputUser[] = D('User')->where($inputwhere)->select();
            //判断报名的状态(已结束，未开始，已报名，报名) 
        }

        //获取招募项目信息
        $project = M('Project')->find($id);
        //判断项目是否失效
        //根据项目的状态进行判断
        if ($project) {
            //根据项目状态前台对应显示页面（未开始，进行中，已结束）
            if ($project['status']) {

            }
            //获取项目图片
            $project['show_image'] = Image::getUrl($project['show_image'], array(430, 315), 'project');
        } else {
            $this->error("查看的项目不存在！");
        }
        $taglist = D('ProjectCategoryServerTagList')->getListByLabel($id);
        $this->assign('taglist', $taglist);
        $volunteer = D('ProjectRecruitVolunteer')->find($id);
        $result = M('ProvinceCity')->find($project['provinceid']);
        $addr['provice'] = $result['class_name'];
        $result = M('ProvinceCity')->find($project['cityid']);
        $addr['city'] = $result['class_name'];
        if ($project['countyid']) {
            $result = M('ProvinceCity')->find($project['countyid']);
            $addr['county'] = $result['class_name'];
        }
        $this->assign('addr', $addr);

        //第一步，获取报名名单
        //第二步，获取讨论信息
        //第三步， 获取反馈信息
        //获取用户讨论信息
        $discussion = $this->getUserDisCussion($id);

        //获取发起人反馈信息
        //获取项目评价

        $ptime = $this->countday($project['begin_time'], $project['end_time']);
        $vtime = $this->countday($volunteer['begin_time'], $volunteer['end_time']);
        $this->assign('ptime', $ptime);
        $this->assign('vtime', $vtime);
        $this->assign('volunteer', $volunteer);
        $this->assign('job', $job);
        $this->assign('project', $project);
        $this->display();
    }

    //获取用户讨论信息
    private function getUserDisCussion($pid)
    {
        return M('Discuse')->where("relation_id=%d", $pid)->select();
    }

    public function index()
    {
        //验证是否为认证用户
        $result = D('User')->checkAuth();
        //返回用户类型,1,认证用户,2,验证过手机的非认证用户,3没有验证过手机的非认证用户
        $phone = '';
        $error = '';
        if ($result) {
            $status = 1;
            $attendProject = $this->attendProject(intval(I('project_id')), intval(I('job_id')), UserSession::getUser('uid'));
            if ($attendProject['status'] < 0) {
                $error = $attendProject['msg'];
            }
            //非认证用户    
        } else {
            //验证手机是否通过验证
            $user = D('User')->getUser(UserSession::getUser('uid'));
            if ($user['phone_status'] && $user['phone']) {
                $status = 2;        //手机通过验证
                $phone = substr_replace($user['phone'], 'xxxx', 3, 4);
            } else {
                $status = 3;        //手机未通过验证
            }
        }
        $r = json_encode(array('status' => $status, 'phone' => $phone, 'error' => $error));
        $callback = $_GET['callback'];
        echo $callback . "($r)";
    }

    //发送手机短信
    public function sedphonemsg()
    {
        $phone = intval(I('phone'));
        $result = json_encode(Sms::sendsms($phone, 'join_project'));
        $callback = $_GET['callback'];
        echo $callback . "($result)";
    }

    //ajax返回信息
    private function sendMsg($result)
    {
        $result = json_encode($result);
        $callback = $_GET['callback'];
        echo $callback . "($result)";
        die();
    }

    //ajax处理用户提交的项目报名信息
    public function handlesub($name, $type, $code, $pid, $jid, $phone = '', $yzcode = '')
    {
        //验证身份证
        if ($type == 1) {
            if (!Idcard::idcard_checksum($code)) {
                $this->sendMsg(array('status' => -1, 'msg' => '身份证号错误'));
            }
            //验证证件号码是否已经存在
            $_vw['uid'] = array('neq', UserSession::getUser('uid'));
            $_vw['idcard_code'] = $code;
            $vol = D('Volunteer')->where($_vw)->count();
            if ($vol > 0) {
                $this->sendMsg(array('status' => -1, 'msg' => "身份证号已经被注册"));
            }
        }
        //验证姓名 
        if (strlen($name) < 2) {
            $this->sendMsg(array('status' => -1, 'msg' => '姓名不能小于两个个字符'));
        }
        //验证类型
        if (!($type == 1 || $type == 2 || $type == 3)) {
            $this->sendMsg(array('status' => -1, 'msg' => "证件类型错误"));
        }
        if (!$phone) {
            //没提交手机号验证手机是否已经通过验证
            $user = D('User')->getUser(UserSession::getUser('uid'));
            if ($user['phone_status'] != 1) {
                $this->sendMsg(array('status' => -1, 'msg' => '请输入手机号'));
            }
        } else {
            //验证手机验证码
            $result = Sms::verify($phone, 'join_project', $yzcode);
            if ($result['status'] < 0) {
                //$this->sendMsg(array('status'=>-1,'msg'=>'手机验证码错误'));
            }
            //查询手机号是否被其他用户绑定
            $_w['uid'] = array('neq', UserSession::getUser('uid'));
            $_w['phone'] = $phone;
            if (D('User')->where($_w)->find()) {
                $this->sendMsg(array('status' => -1, 'msg' => '该手机号码已被其他账户绑定'));
            }
            //添加手机验证成功标识
            $_d['phone'] = $phone;
            $_d['phone_status'] = 1;
            $_d['update_date'] = time();
            if (!D('User')->where("uid=%d", UserSession::getUser('uid'))->save($_d)) {
                $this->sendMsg(array('status' => -1, 'msg' => '验证标识添加失败'));
            }
        }
        //用户数据入库
        $vol = D('Volunteer')->find(UserSession::getUser('uid'));
        $_data['idcard_type'] = $type;
        $_data['idcard_code'] = $code;
        $_data['update_date'] = time();
        if ($vol) {
            D('Volunteer')->where("uid=%d", $vol['uid'])->save($_data);
        } else {
            $_data['uid'] = UserSession::getUser('uid');
            D('Volunteer')->add($_data);
        }
        //报名项目
        $attendProject = $this->attendProject($pid, $jid, UserSession::getUser('uid'));
        if ($attendProject['status'] < 0) {
            $this->sendMsg(array('status' => -1, 'msg' => $attendProject['msg']));
        }
        $this->sendMsg(array('status' => 1, 'msg' => 'success'));
    }

    //用户报名参加项目
    private function attendProject($pid, $jid, $uid)
    {
        //检测项目是否存在
        if (!M('Project')->find($pid)) {
            return array('status' => -1, 'msg' => '项目不存在');
        }
        $_w = array('project_id' => $pid, 'user_id' => $uid);
        $result = M('ProjectRecruitDetail')->where($_w)->find();
        //检测更新数据
        if ($result) {
            if ($result['status'] == 101) {    //取消参与状态下，将状态修改为参与状态
                $_d = $_w;
                $_d['status'] = 100;
                $_d['job_id'] = $jid;
                if (!M('ProjectRecruitDetail')->where("id=%d", $result['id'])->save($_d)) {
                    return array('status' => -1, 'msg' => '项目详情表数据更新失败');
                } else {
                    return $this->addProjectJoin($pid, $uid);
                }
            } else {//ProjectRecruitDetail表中已经参与状态，检测peojectjoin表中的状态
                if ($jid != $result['job_id']) {
                    return array('status' => -1, 'msg' => '一个项目只能申请一个岗位');
                }
                return $this->addProjectJoin($pid, $uid);
            }
            //新添加数据   
        } else {
            $_d = $_w;
            $_d['status'] = 100;
            $_d['job_id'] = $jid;
            $_d['create_date'] = date("Y-m-d H:i:s");
            $_d['update_date'] = date("Y-m-d H:i:s");
            if (M('ProjectRecruitDetail')->add($_d)) { //在ProjectRecruitDetail表中添加报名
                $addProjectJoin = $this->addProjectJoin($pid, $uid);
                if ($addProjectJoin['status'] == -1) {
                    return $addProjectJoin;
                }
            } else {//添加失败
                return array('status' => -1, 'msg' => '项目参与详情表数据添加失败');
            }
        }
        return array('status' => 1, 'msg' => '报名成功');
    }

    //项目报名表数据添加
    private function addProjectJoin($pid, $uid)
    {
        $_w = array('uid' => $uid, 'project_id' => $pid, 'type' => 1);
        $result = M('ProjectJoin')->where($_w)->find();
        if ($result) {
            if (!M("ProjectJoin")->where("id=%d", $result['id'])->save(array('status' => 1))) {
                return array('status' => -1, 'msg' => '项目报名添加失败');
            }
        } else {
            $_d = array(
                'uid' => $uid,
                'project_id' => $pid,
                'type' => 1,
                'status' => 1,
            );
            if (!M("ProjectJoin")->add($_d)) {
                return array('status' => -1, 'msg' => '项目报名添加失败');
            }
        }
        return array('status' => 1, 'msg' => '项目报名成功');
    }

    //取消项目报名
    public function cancelJoin($pid)
    {
        $_w = array('uid' => UserSession::getUser('uid'), 'project_id' => $pid);
        if (!M('ProjectJoin')->where($_w)->save(array('status' => 2))) {
            $result = json_encode(array("status" => -1, 'msg' => '项目取消失败'));
        } else {
            $_w = array('user_id' => UserSession::getUser('uid'), 'project_id' => $pid);
            if (!M('ProjectRecruitDetail')->where($_w)->save(array('status' => 101))) {
                $result = json_encode(array("status" => -1, 'msg' => '项目详情取消失败'));
            } else {
                $result = json_encode(array("status" => 1, 'msg' => '项目取消成功'));
            }
        }
        $callback = $_GET['callback'];
        echo $callback . "($result)";
    }

    /**
     * 根据开始和结束时间计算已经开始的时间比例和还剩余的天数
     * @param mixed $stime 开始的时间
     * @param mixed $etime 结束的时间
     * @param int $status 时间的形式 1-datetime的形式，0-时间戳的形式 默认为datetime的形式
     * return array('剩余的天数','已经开始天数的比例');
     */
    private function countday($stime, $etime, $status = 1)
    {
        if ($status) {
            $stime = strtotime($stime);     //招募开始时间
            $etime = strtotime($etime);       //招募结束时间       
        }
        if ($stime > time()) {
            $surplusday = '暂未开始';
            $ratio = 0;
        } elseif ($etime < time()) {
            $surplusday = '已经结束';
            $ratio = 100;
        } else {
            $totalday = intval(($etime - $stime) / 86400);            //总天数
            $surplusday = intval(($etime - time()) / 86400);              //剩余的天数
            $ratio = intval((($totalday - $surplusday) / $totalday) * 100);             //已经开始天数的比例
            $surplusday = '剩余<b>' . $surplusday . '天';
        }
        return array($surplusday, $ratio);
    }

    /**
     *
     * @param type $codetype 证件类型 1-身份证
     */
    public function certificate($projectid = 0)
    {
        if (empty($projectid)) {
            $this->error("项目错误");
            return;
        }
        //判断用户是否登录
        if (!UserSession::getUserId()) {
            $this->error("没有权限操作");
            return;
        }
        //取得登录用户认证信息
        $vol = D('volunteer')->find(UserSession::getUserId());
        if (!$vol || $vol['status'] == -1) {
            $this->error("您还没有认证成为志愿者");
            return;
        }
        //检查用户身份证信息
        $codenumber = $vol['idcard_code'];
        $codetype = $vol['idcard_type'];
        if (!$codenumber || !$codetype) {
            $this->error("您的个人信息不完整，无法生成证书");
            return;
        }
        //检查项目是否存在
        $project = M('Project')->where(" status in (100,888,889) and id=" . $projectid)->find();
        //检查用户参与了项目并获得了时常
        if (!$project) {
            $this->error("项目不存在");
            return;
        }

        $ftp = new Ftp();
        $number =$projectid . UserSession::getUserId();// $project["creator"] . $projectid . UserSession::getUserId();
        $zs_name = 'zsTemplate/zqgyzs_' . $number . '.png';
        //检查证书是否存在
        $zs_local_path = $ftp->download_file($zs_name);
        if ($zs_local_path) {
            //直接输出
            $img = file_get_contents($zs_local_path, true);
            header("Content-Type: image/jpeg;text/html; charset=utf-8");
            echo $img;
        } else {
            $zs_local_path = IMAGE_SERVER_PATH . '/' . $zs_name;
        }


        $projectname = $project['name'];

        $user_join = D('T/ProjectRecruitDetail')->where(" project_id=" . $projectid . " and user_id=" . UserSession::getUserId() . " and status in (201,203,300) ")->field('server_time')->find();
        if (!$user_join) {
            $this->error("您未参与项目");
            return;
        }

        $servertime = $user_join['server_time'];
        if (!$servertime || intval($servertime) < 0) {
            $this->error("您没有获得服务时常");
            return;
        }
        //取得项目创建者信息 $orgname
        $org = D('organization')->find($project['creator']);
        if (!$org) {
            $this->error("项目创建者信息错误");
            return;
        }
        $orgname = $org['org_name'];
        if ($codetype == 1) {
            $codename = "身份证号";
        } else {
            $codename = "护照号  ";
        }
        $name = $vol['real_name'];
        $zhl = $codename . '' . $codenumber . ',参加了由"' . $orgname . '"发起的志愿服务项目"' . $projectname . '"并获得' . $servertime . '个志愿服务时长';
        $nw = $this->get_name_position($name);
        //计算姓名的位置
        $namewidth = $nw[0];
        $nameheight = $nw[1];


        //获取第一行的内容
        $oneContent = $this->get_one_line($zhl);
        //定义第一行的开始位置
        $oneWidth = 185;
        $oneHeight = 10;
        //获取第二行的内容
        $twoContent = $this->get_two_line($oneContent['num'], $zhl);
        //定义第二行的开始位置
        $twoWidth = 100;
        $twoHeight = 65;

        if ($twoContent['status'] != 1) {
            //获取第三行的内容
            $threeContent = $this->get_three_line($twoContent['status'], $twoContent['num'], $zhl);
            //定义第三行的位置
            $threeWidth = 100;
            $threeHeight = 120;
        }

        if ($twoContent['status'] != 1) {
            if ($threeContent['status'] != 1) {
                //获取第四行的内容
                $fourContent = $this->get_four_line($threeContent['status'], $threeContent['num'], $zhl);
                //定义第四行的位置
                $fourWidth = 100;
                $fourHeight = 175;
            }
        }

        if ($twoContent['status'] != 1) {
            if ($threeContent['status'] != 1) {
                if ($fourContent['status'] != 1) {
                    //获取第四行的内容
                    $fiveContent = $this->get_five_line($fourContent['status'], $fourContent['num'], $zhl);
                    //定义第四行的位置
                    $fiveWidth = 100;
                    $fiveHeight = 230;
                }
            }
        }
        //定义编号的位置
        $codewidth = 915;
        $codeheight = -343;

        //定义年月日的高度
        $dateheight = 293;
        //定义年月日的长度
        $ywidth = 820;
        $mwidth = 925;
        $dwidth = 992;

        //定义组织名称的位置
        if (mb_strlen($orgname, 'UTF8') <= 12) {
            $orgOneName = $orgname;
            $orgwidth = $this->get_org_width($orgname);
            $orgheight = 300;
        } else {
            $orgOneName = mb_substr($orgname, 0, 12, 'utf-8');
            $orgwidth = $this->get_org_width($orgOneName);
            $orgheight = 285;
            $orgtwoName = mb_substr($orgname, 12, 12, 'utf-8');
            $orgtwowidth = $this->get_org_width($orgtwoName);
            $orgtwoheight = 315;
        }

        $image_water = \Think\Image::IMAGE_WATER_WEST;
        $image = new \Think\Image();
        $imgname = $ftp->download_file("zsTemplate/fwzs.png");

        $font = '../../../think_zhiyuanjia/App/Font/STHeiti.ttf';
        $color = '#7b8289';
        $image = $image->open($imgname)->text($name, $font, 50, $color, $image_water, array($namewidth, $nameheight));
        $image = $image->text($oneContent['content'], $font, 32, $color, $image_water, array($oneWidth, $oneHeight));
        $image = $image->text($twoContent['content'], $font, 32, $color, $image_water, array($twoWidth, $twoHeight));
        if ($twoContent['status'] != 1) {
            $image = $image->text($threeContent['content'], $font, 32, $color, $image_water, array($threeWidth, $threeHeight));
            if ($threeContent['status'] != 1) {
                $image = $image->text($fourContent['content'], $font, 32, $color, $image_water, array($fourWidth, $fourHeight));
                if ($fourContent['status'] != 1) {
                    $image = $image->text($fiveContent['content'], $font, 32, $color, $image_water, array($fiveWidth, $fiveHeight));
                }
            }
        }

        $year = date('Y', time());
        $mounth = date('m', time());
        $day = date('d', time());
        $image = $image->text($number, $font, 21, $color, $image_water, array($codewidth, $codeheight));
        $image = $image->text($year, $font, 23, $color, $image_water, array($ywidth, $dateheight));
        $image = $image->text($mounth, $font, 23, $color, $image_water, array($mwidth, $dateheight));
        $image = $image->text($day, $font, 23, $color, $image_water, array($dwidth, $dateheight));
        $image = $image->text($orgOneName, $font, 23, $color, $image_water, array($orgwidth, $orgheight));
        if ($orgtwoName) {
            $image = $image->text($orgtwoName, $font, 23, $color, $image_water, array($orgtwowidth, $orgtwoheight));
        }
        $image->save($zs_local_path);
        //ftp 同步

        $ftp->up_file($zs_local_path, $zs_name);
        $img = file_get_contents($zs_local_path, true);
        header("Content-Type: image/jpeg;text/html; charset=utf-8");
        echo $img;
    }

    //根据字符数，计算组织名称的横向坐标
    private function get_org_width($name)
    {
        $startidth = 135;
        $orglength = mb_strlen($name, 'UTF8');
        $size = (12 - $orglength) / 2;
        $size = 33 * $size + $startidth;
        return $size;
    }


    /*
     * 获取第一行的内容
     * @param $codenumber 证件号码
     * @param $codetype 证件类型 1-身份证 其他-护照
     * @param $orgname 组织名称
     */
    private function get_one_line($zhl)
    {
        //echo $zhl;
        $firstwidth = 966;
        $num = mb_strlen($zhl, 'UTF8'); //获取正文的长度
        $i = 0;
        $cl = 43;   //定义中文字符长度38
        $el = 18;   //英文字符长度20
        $w = 0;
        while ($i < $num) {
            $str = mb_substr($zhl, $i, 1, 'utf-8');
            preg_match_all("/[a-zA-Z0-9]{1}/", $str, $arrAl);
            if (count($arrAl[0])) {
                $w += $el;
            } else {
                $w += $cl;
            }
            $i++;
            if ($w > $firstwidth) {
                break;
            }
        }
        $next = mb_substr($zhl, $i - 1, 1, 'utf-8');
        if ($next == '"' || $next == ',') {
            $i -= 1;
        }
        $content = mb_substr($zhl, 0, $i - 1, 'utf-8');
        //echo $content;exit;	//身份证号410481199610310522,参加了由"测试
        //echo $i;exit;	"测试公益组织"	5
        return array('status' => 0, 'num' => $i, 'content' => $content);

    }

    /*
     * 获取第二行的内容
     * @param string $orgname 组织名称
     * @param int $orgnameisend 组织名称是否已经截取完，0-未截取完 1-截取完
     * @param string $projectname 项目名称
     * @param int $servertime 服务时长
     * @param int $num 组织名称没有截取完时，表示截取了组织名称多少字符，组织名称截取完时，表示"发起的志愿者服务项目"这几个字截取了几个字符
     */
    private function get_two_line($i, $zhl)
    {
        $secondwidth = 1096;
        $num = mb_strlen($zhl, 'UTF8'); //获取正文的长度
        $cl = 43;   //定义中文字符长度38
        $el = 18;   //英文字符长度20
        $w = 0;
        $j = $i;
        while ($j < $num) {
            $str = mb_substr($zhl, $j, 1, 'utf-8');
            preg_match_all("/[a-zA-Z0-9]{1}/", $str, $arrAl);
            if (count($arrAl[0])) {
                $w += $el;
            } else {
                $w += $cl;
            }
            $j++;
            if ($w > $secondwidth) {
                break;
            }
        }
        $next = mb_substr($zhl, $j - 1, 1, 'utf-8');
        if ($next == '"' || $next == ',' || $next == '.') {
            $j -= 1;
        }
        if ($j >= $num) {
            $s = 1;        //正文结束
        } else {
            $s = 0;        //正文为结束
        }
        $k = $j - $i;
        if ($s == 1)
            $content = mb_substr($zhl, $i - 1, $k + 1, 'utf-8');
        else
            $content = mb_substr($zhl, $i - 1, $k, 'utf-8');
        return array('status' => $s, 'content' => $content, 'num' => $j);

    }

    /**
     * 获取第三行内容
     */
    private function get_three_line($status, $i, $zhl)
    {
        $threewidth = 1096;
        $num = mb_strlen($zhl, 'UTF8'); //获取正文的长度
        $cl = 43;   //定义中文字符长度38
        $el = 18;   //英文字符长度20
        $w = 0;
        $j = $i;
        while ($j < $num) {
            $str = mb_substr($zhl, $j, 1, 'utf-8');
            preg_match_all("/[a-zA-Z0-9]{1}/", $str, $arrAl);
            if (count($arrAl[0])) {
                $w += $el;
            } else {
                $w += $cl;
            }
            $j++;
            if ($w > $threewidth) {
                break;
            }
        }
        $next = mb_substr($zhl, $j - 1, 1, 'utf-8');
        if ($next == '"' || $next == ',' || $next == '.') {
            $j -= 1;
        }
        if ($j >= $num) {
            $s = 1;        //正文结束
        } else {
            $s = 0;        //正文为结束
        }
        $k = $j - $i;//echo $k.';';
        if ($s == 1)
            $content = mb_substr($zhl, $i - 1, $k + 1, 'utf-8');
        else
            $content = mb_substr($zhl, $i - 1, $k, 'utf-8');
        return array('status' => $s, 'content' => $content, 'num' => $j);

    }

    /**
     * 获取第四行内容
     */
    private function get_four_line($status, $i, $zhl)
    {
        $fourwidth = 1096;
        $num = mb_strlen($zhl, 'UTF8'); //获取正文的长度
        $cl = 43;   //定义中文字符长度38
        $el = 18;   //英文字符长度20
        $w = 0;
        $j = $i;
        while ($j < $num) {
            $str = mb_substr($zhl, $j, 1, 'utf-8');
            preg_match_all("/[a-zA-Z0-9]{1}/", $str, $arrAl);
            if (count($arrAl[0])) {
                $w += $el;
            } else {
                $w += $cl;
            }
            $j++;
            if ($w > $fourwidth) {
                break;
            }
        }
        $next = mb_substr($zhl, $j - 1, 1, 'utf-8');
        if ($next == '"' || $next == ',' || $next == '.') {
            $j -= 1;
        }
        if ($j >= $num) {
            $s = 1;        //正文结束
        } else {
            $s = 0;        //正文为结束
        }
        $k = $j - $i;//echo $k.';';
        if ($s == 1)
            $content = mb_substr($zhl, $i - 1, $k + 1, 'utf-8');
        else
            $content = mb_substr($zhl, $i - 1, $k, 'utf-8');
        return array('status' => $s, 'content' => $content, 'num' => $j);

    }

    /**
     * 获取第五行内容
     */
    private function get_five_line($status, $i, $zhl)
    {
        $fivewidth = 296;
        $num = mb_strlen($zhl, 'UTF8'); //获取正文的长度
        $cl = 43;   //定义中文字符长度38
        $el = 18;   //英文字符长度20
        $w = 0;
        $j = $i;
        while ($j < $num) {
            $str = mb_substr($zhl, $j, 1, 'utf-8');
            preg_match_all("/[a-zA-Z0-9]{1}/", $str, $arrAl);
            if (count($arrAl[0])) {
                $w += $el;
            } else {
                $w += $cl;
            }
            $j++;
            if ($w > $fivewidth) {
                break;
            }
        }
        if ($j > $num) {
            $s = 1;        //正文结束
        } else {
            $s = 0;        //正文为结束
        }
        $k = $j - $i;
        $content = mb_substr($zhl, $i - 1, $k + 1, 'utf-8');
        return array('status' => $s, 'content' => $content, 'num' => $j);

    }

    //根据证件号获取是护照还是身份证号
    private function get_code_name($codetype)
    {
        //证件类型
        if ($codetype == 1) {
            $codename = "身份证号";
        } else {
            $codename = "护照号  ";
        }
        return $codename;
    }


    //获取姓名的位置
    private function get_name_position($name)
    {
        $nameline = mb_strlen($name, 'UTF8');
        if ($nameline == 2) {
            $namewidth = 540;
            $nameheight = -75;
        } elseif ($nameline == 3) {
            $namewidth = 490;
            $nameheight = -75;
        } else {
            $namewidth = 435;
            $nameheight = -75;
        }
        return array($namewidth, $nameheight);
    }

    //计算字符的长度
    private function countstr($str)
    {
        $cl = 43;   //定义中文字符长度38，
        $el = 20;   //英文字符长度20
        $long = mb_strlen($str, 'UTF8'); //获取字符的长度
        $i = 0;
        $w = 0;
        while ($i < $long) {
            $str = mb_substr($str, $i, 1, 'utf-8');
            preg_match_all("/[a-zA-Z]{1}/", $str, $arrAl);
            if (count($arrAl[0])) {
                $w += $el;
            } else {
                $w += $cl;
            }
            $i++;
        }
        return $w;
    }

}
