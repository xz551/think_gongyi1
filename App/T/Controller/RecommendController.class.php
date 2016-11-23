<?php
/**
 * Created by PhpStorm.
 * User: ZhangZhiGang
 * Date: 2016/5/11
 * Time: 14:33
 */

namespace T\Controller;

use Lib\UserSession;
use Think\Controller;

/**
 * 推荐
 * 调用课题二
 * Class RecommendController
 * @package T\Controller
 */
class RecommendController extends Controller
{
    const URL='http://172.16.100.69:8080/Recommender';

    /**
     * 获得对志愿者推荐的活动列表
     * @param int $uid 志愿者的id（必须）
     * @param string $type 推荐所使用的方法（取值0-15）：四位二进制数值，从低到高位分别表示
     * 领域推荐、地域推荐、协同过滤推荐、个性特征推荐，如7表示二进制数0111，表示启用地域、领域、协同过滤推荐，不启用个性特征推荐
     * @param int $size
     */
    public function recomend($uid=0,$type='0001',$size=5){
        if(empty($uid)){
            $uid=UserSession::getUserId();
        }
        if(empty($uid)){
            return array();
        }
        $url=self::URL.'/RecomendServlet?uid='.$uid."&type=".$type.'&size='.$size;
        echo $url;exit;
        $result=api($url);
        echo $result;
    }
}