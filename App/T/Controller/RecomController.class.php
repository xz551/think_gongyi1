<?php
namespace T\Controller;

use Think\Controller;
use Lib\UserSession;
use Lib\Image;

//获取推荐数据
class RecomController extends Controller
{
    //  const URL='http://172.16.100.69:8080/Recommender';
    private $recomip = "http://172.16.100.69:8080";

    //获取推荐项目
    public function getproject($type = '',$t=0)
    {
        if($t==1){
            $this->recomendProject();
            return;
        }
        $uid = UserSession::getUser('uid');
        $data=array();
        if ($uid) {
            $url = $this->recomip . "/Recommender/RecomendServlet?uid=" . $uid . "&size=4";
            $result = api($url);
            $result = json_decode($result);
            if(!isset($result->errorno)) {
                //没有错误
                $result = reset($result);
                if (isset($result->action_id)) {
                    $arr = isset($result->action_id) ? explode(',',$result->action_id) : ''; 
                    $_w['id'] = array('in', $arr);
                    $data = D('Active')->where($_w)->select();
                }
            }
        }
	 if(empty($active)){
         $data = D('Active')->where(" status=1")->order('id desc')->limit(4)->select();
      }

        $str = "";
        foreach ($data as $v) {
            $imgurl = Image::getUrl($v['image'], array(192, 145),'active');
            $province = D('ProvinceCity')->find($v['provinceid']);
            $province = $province['class_name'];
            $city = D('ProvinceCity')->find($v['cityid']);
            $city = $city['class_name'];
            $address = $province . '-' . $city;
            $url = '/active/view/id/' . $v['id'] . '.html';
            $str .= '<li class="item">';
            $str .= '<figure>';
            $str .= '<a href="' . $url . '" >';
            $str .= '<img class="projectError"   src="' . $imgurl . '" height="145" width="192">';
            $str .= '</a>';
            $str .= '<figcaption><a href="' . $url . '" title="' . $v['name'] . '">' . $v['name'] . '</a></figcaption>';
            $str .= '</figure>';
            $str .= '<div class="info clearfix">';
            $str .= '<p class="address"><span class="fix_icons address_icon"></span><span class="address_string" title="' . $address . '">' . $address . '</span></p>';
            $str .= '<p class="pro_detail">' . $v['name'] . '</p>';
            $str .= '</div>';
            $str .= '</li>';
        }
        echo $str;
    }

    /**
     * 获取推荐志愿者数据
     * @param int $provinceflag 1-按省份匹配 0-不按省份匹配
     * @param int $domainflag 1-按领域匹配标签
     * @param int $authenticationflag 1-按实名认证志愿者匹配
     * @param int $orgnizationfla 1-只匹配组织内部成员
     * @param int $maleflag 1- 按性别匹配
     */
    public function getUser($projectid = '', $provinceflag = 0, $domainflag = 0, $authenticationflag = 0, $orgnizationfla = 0, $maleflag = 0)
    {
        layout(false);
        $url = $this->recomip . "/VolunteerRecommender/VolunteerRecomendServlet?projectid=$projectid&provinceflag=$provinceflag&domainflag=$domainflag&authenticationflag=$authenticationflag&orgnizationfla=$orgnizationfla&maleflag=$maleflag";
        $result = file_get_contents($url);
        $userarr = array();

        //获取用户信息

        //echo "123dfsdfsdf";die();

        $this->display('getuser');

    }
    public function recomendProject(){
        $uid = UserSession::getUser('uid');
        $data=array();
        if($uid){
            $url='/Volunteer5/RecommenderServlet?uid='.$uid;
            $result = api($url);
            $result = json_decode($result);
            if(!isset($result->errorno)) {
                //没有错误
                $result = reset($result);
                if (isset($result->id)) {
                    $arr = isset($result->id) ? explode(',',$result->id) : '';
                    $_w['id'] = array('in', $arr);
                    $data = D('Project')->where($_w)->select();
                }
            }
        }
        if(empty($data)){
            $data = D('Project')->where(" status in ( 100,888) ")->order('id desc')->limit(4)->select();
        }
        $str = "";
        foreach ($data as $v) {
            $imgurl = Image::getUrl($v['show_image'], array(192, 145),'active');
            $province = D('ProvinceCity')->find($v['provinceid']);
            $province = $province['class_name'];
            $city = D('ProvinceCity')->find($v['cityid']);
            $city = $city['class_name'];
            $address = $province . '-' . $city;
            $url = '/project/view/id/' . $v['id'] . '.html';
            $str .= '<li class="item">';
            $str .= '<figure>';
            $str .= '<a href="' . $url . '" >';
            $str .= '<img class="projectError"   src="' . $imgurl . '" height="145" width="192">';
            $str .= '</a>';
            $str .= '<figcaption><a href="' . $url . '" title="' . $v['name'] . '">' . $v['name'] . '</a></figcaption>';
            $str .= '</figure>';
            $str .= '<div class="info clearfix">';
            $str .= '<p class="address"><span class="fix_icons address_icon"></span><span class="address_string" title="' . $address . '">' . $address . '</span></p>';
            $str .= '<p class="pro_detail">' . $v['name'] . '</p>';
            $str .= '</div>';
            $str .= '</li>';
        }
        echo $str;
    }

}