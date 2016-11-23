<?php

namespace Wx\Service;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Wx\Enum\WxMsgEnum;
use Wx\Enum\WxEventEnum;
use Wx\Enum\WxMenuEnum;
use Lib\Session\UserSession;

/**
 * 微信消息服务
 *
 * @author Administrator
 */
class WxMessageService extends \Think\Model {

    /**
     * 被动响应
     * @var type 
     */
    var $passive;
    var $postObj;

    function __construct() {
        if (!$this->passive) {
            $this->passive = D('WxPassiveSend', 'Service');
            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
            if (!empty($postStr)) {
                $this->postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $this->postObj = json_decode(json_encode($this->postObj));
                $this->postObj->mpInfo = M('mp')->where("MpOriginalId='%s'", $this->postObj->ToUserName)->find();
                $this->passive->postObj = $this->postObj;
            }
        }
    }

    /**
     * 更新关注
     */
    private function updateSub(){
        $Event = $this->postObj->Event;
        if( !$Event || ( $Event !=WxEventEnum::subscribe && $Event!=WxEventEnum::unsubscribe && $Event!=WxEventEnum::SCAN  ) ){
            //用户已经关注，防止系统出错，数据库更新已经关注
            D('WxUser')->where(array('openid'=>$this->postObj->FromUserName))->setField('subscribe',1);
        }

    }

    /**
     * 获取用户输入的消息
     */
    public function getMessage() {
        if ($this->postObj) {
            $this->updateSub();
            $MsgType = trim($this->postObj->MsgType);
            switch ($MsgType) {
                case WxMsgEnum::event:
                    //推送消息
                    $this->eventMsg();
                    break;
                case WxMsgEnum::text:
                    //文本消息
                    $this->keyword();
                    break;
                case WxMsgEnum::image:
                    //图片信息
                    D('WxMediaMessage')->saveMediaMsg($this->postObj);
                    if (!$this->quickKeyword()) {
                        $this->passive->image($this->postObj->MediaId);
                    }
                    break;
                case WxMsgEnum::voice:
                    //语音信息
                    D('WxMediaMessage')->saveMediaMsg($this->postObj);
                    //$this->passive->text($this->postObj, '您的语音信息，已经收到');
                    $this->passive->voice($this->postObj->MediaId);
                    break;
                case WxMsgEnum::video:
                    //视频信息
                    D('WxMediaMessage')->saveMediaMsg($this->postObj);
                    //$this->passive->text($this->postObj, '您的视频信息，已经收到');
                    $this->passive->video($this->postObj->ThumbMediaId);
                    break;
                case WxMsgEnum::link:
                    //链接消息
                    D('WxMediaMessage')->saveMediaMsg($this->postObj);
                    $this->passive->text('您的链接消息，已经收到');
                    break;
                case WxMsgEnum::location:
                    //地址位置信息
                    D('WxUserLocation')->saveUserLocation($this->postObj);
                    $this->passive->text('您的地理位置信息，已经收到');
                    break;
            }
        } else {
            echo "";
            exit;
        }
    }

    /**
     * 微信服务起推送过来的事件 
     * 非用户发送的信息 
     */
    private function eventMsg() {
        $Event = $this->postObj->Event;
        switch ($Event) {
            case WxEventEnum::subscribe:
            case WxEventEnum::unsubscribe:
            case WxEventEnum::SCAN:
                //关注和非关注事件
                $res = D('WxUser')->Subscribe($this->postObj);
                $text="";
                if($res == 1 || $res == 2){
                    //订阅公共号
                    $result=D('WxTopic')->subKeep($this->postObj->FromUserName);
                    if($result){
                        $_dingyue=C('dingyue');
                        $_dingyue=  str_replace('%topicid', $result['TopicId'], $_dingyue);
                        $_dingyue=  str_replace('%topictitle', $result['TopicTitle'], $_dingyue);
                        $mp = php_encrypt($this->postObj->ToUserName);
                        $_dingyue=  str_replace('%mp', $mp, $_dingyue);
                        $text="感谢您的关注！".$_dingyue;
                    }
                        //关注公众号 检测回掉
                    D('T/WxCallback')->obtain($this->postObj->FromUserName);
                }
                if(!$text){
                    $text= $res == 1?$this->postObj->mpInfo["Subscribe"]:$this->postObj->mpInfo["Subscribe2"];
                    
                }
                $this->passive->text($text);            
                
                break;
            case WxEventEnum::LOCATION:
                //上报地理位置事件
                $this->eventLOCATION();
                break;
            case WxEventEnum::CLICK:
                $this->menuClick();
                break;
        }
    }

    private function menuClick() {
        $EventKey = $this->postObj->EventKey;
        switch ($EventKey) {
            case WxMenuEnum::allTopic:
            case WxMenuEnum::myTopic:
            case WxMenuEnum::takeTopic:
            case WxMenuEnum::Tribe_FaXian:
            case WxMenuEnum::Tribe_My:
            case WxMenuEnum::Tribe_YaoQing:
                $this->topic($EventKey);
                break;
            case WxMenuEnum::weiDuXiaoXi:
                //未读消息
                //查找是否有未读消息
                $w = "MsgRead=0 and MsgStatue=1 and MsgType='wx' and MsgSendUser='" . $this->postObj->FromUserName . "' ";

                $c = M('Message')->where($w)->count();
                if (!$c) {
                    $this->passive->text("暂时还没有未读消息，试试将频道分享出去，邀请更多人参与");
                } else {
                    $formuser = $this->postObj->FromUserName;
                    $key = C('menu_key.' . $EventKey);
                    if(is_array($key)){//
                        $title=  str_replace("%count", $c, $key['title']) ;
                        $img=$key['img'];
                        $desc=$key['desc'];
                        $url=str_replace("%user", $formuser, $key['url']);//$key['url'];//%user
                        $this->passive->news(array(
                           "0"=>array(
                                'title'=>$title,
                                'img'=>$img,
                                'desc'=>$desc,
                                'url'=>$url
                           )
                        ));
                    }else{
                        $this->passive->text("您有" . $c . "条未读消息，<a href='".web_site."/t/message/wx?uu=" . $formuser . "'>点击查看</a>");
                    }
                }
                break;
            default:
                $key = C('menu_key.' . $EventKey);
                if(is_array($key)){
                    //图文消息
                    $title=$key['title'];
                    $img=$key['img'];
                    $desc=$key['desc'];
                    $url=$key['url'];
                    $this->passive->news(array(
                       "0"=>array(
                            'title'=>$title,
                            'img'=>$img,
                            'desc'=>$desc,
                            'url'=>$url
                       )
                    ));
                }else if (is_string($key)) {
                    $mp = php_encrypt($this->postObj->ToUserName);
                    $key = str_replace('%mp', $mp, $key);
                    $this->passive->text($key);
                } else {
                    $this->passive->text("没有找到您要的信息");
                }
                break;
        }
        D('WxMenu')->saveData($this->postObj);
    }

    private function topic($e) {
        if ($e == WxMenuEnum::myTopic) {
            $FromUserName = $this->postObj->FromUserName;
            $UserId = M('WxUser')->where("openid='%s'", $FromUserName . ' and Statue=1')->getField('UserId');
            $w = " OpenId='" . $FromUserName . "'";
            if ($UserId) {
                $w.=" or CreateUser=" . $UserId;
            }
            $topics = D('WxTopic')->topicList($this->postObj, $w);
            if($topics){
                array_unshift($topics,array(
                    'img'=>web_site.'/Public/img/wx/1638707205.jpg',
                    'title'=>'',
                    'desc'=>'',
                    'url'=>''
                ));
            }
        } else if ($e == WxMenuEnum::allTopic) {
            $topics = D('WxTopic')->Preview($this->postObj);
        } else if ($e == WxMenuEnum::takeTopic) {
            $topics = D('WxTopic')->takeTopic($this->postObj);
            if($topics){
                array_unshift($topics,array(
                    'img'=>web_site.'/Public/img/wx/140009359.jpg',
                    'title'=>'',
                    'desc'=>'',
                    'url'=>''
                ));
            }
        }else if($e==WxMenuEnum::Tribe_FaXian){
            //随机推荐一个部落
            //select   *  from wetouch_topic WHERE CreateDate > '2015-03-10' and Kind=1 ORDER BY RAND()
            $data=M('Topic')->where(" Statue=1 and Kind=1 and MpOriginalId='"._mp_."' ")->order(" RAND() ")->find();

            $topics=array(array(
                'img'=>web_site.'/Public/img/wxlogoa.png',
                'title'=>'部落：'.$data['TopicTitle'],
                'desc'=>substr( $data['TopicShort'],0,72),
                'url'=>web_site.'/t/tribe/users/id/'.$data['TopicId'].'/mp/%mp'
            ));
        }else if($e==WxMenuEnum::Tribe_My){
            //我的部落 包含我创建的和我加入的
            $topics = D('WxTopic')->topicList($this->postObj,'');
            foreach ($topics as $key => $value) {
                $topics[$key]['TopicTitle']="部落：".$value['TopicTitle'];
            }

        }else if($e==WxMenuEnum::Tribe_YaoQing){
            //快速生成邀请函
            /***
             *
            回复部落编号邀请伙伴加入：
            【1】靓靓生日惊喜派对密谋团
            【2】徒步北京城
            【3】北马跑团
            【4】萌创意
            【5】互联网思维学院
            你也可以                 创建新部落

             */
            //查询我参与的所有部落
            $data=M('TopicJoin as j')->join(" left join wetouch_topic as t on j.TopicId=t.TopicId")->where(" t.Statue=1 and MpOriginalId='".$this->postObj->ToUserName."' and j.Status=1 and j.OpenId='".$this->postObj->FromUserName."' ")->order(" j.CreateDate desc ")->field('t.TopicTitle,t.TopicId')->select();

            if(!$data){
                $text='你还没有加入任何部落，<a href="'.web_site.'/t/tribe/edit">点击这里</a> 创建新部落';
                //你还没有加入任何部落，点击这里 创建新部落
            }else{
                $text="回复部落编号邀请伙伴加入：\n";
                foreach ($data as $key => $value) {
                    $text.="【".$value['TopicId']."】".$value['TopicTitle']."\n";
                }
                $text.='你也可以<a href="'.web_site.'/t/tribe/edit">点击这里</a>创建频道';
                //记录消息操作进入 快速邀请

            }
            $this->passive->text($text);
        }

        if ($topics) {
            $this->passive->news($topics);
        } else {
            $this->passive->text("没有找到您要的信息");
        }
    }

    /**
     * 用户同意上报地理位置
     */
    private function eventLOCATION() {
        $data = array(
            'ToUserName' => $this->postObj->ToUserName,
            'FromUserName' => $this->postObj->FromUserName,
            'MsgType' => $this->postObj->MsgType,
            'Event' => $this->postObj->Event,
            'CreateTime' => $this->postObj->CreateTime,
            'Location_X' => $this->postObj->Latitude,
            'Location_Y' => $this->postObj->Longitude,
            'Label' => $this->postObj->Precision
        );
        D('WxUserLocation')->saveUserLocation($data);
        $this->passive->text('您的地理位置信息，已经收到');
    }

    private function keyword() {
        $keyword = trim($this->postObj->Content);
        if (!empty($keyword)) {
            if (is_numeric(stripos($keyword, '#')) && stripos($keyword, '#') == 0) {
                //快速参与指令
                $this->quickTopic();
            } else if (!$this->quickKeyword() && !$this->tribe_kaisu_yaoqing($keyword)) {
                //搜索关键词
                $key = C('keyword')[$keyword];
                if ($key) {
                    $key=str_replace("%mp",php_encrypt($this->postObj->ToUserName),$key);
                    $this->passive->text($key);
                } else {
                    //查找萌想
                    $w=" statue=1 and MpOriginalId='" . $this->postObj->ToUserName . "'";
                    if(!in_array($keyword, array("萌想","列表","所有萌想"),true)){
                        $w.=" and  TopicTitle like '%" . $keyword . "%' ";
                    }
                    $topic = M('topic')->where($w)->limit(C('list_page_count'))->select();
                    if ($topic) {
                        $this->passive->news($topic);
                    } else {
                        $none=str_replace("%mp",php_encrypt($this->postObj->ToUserName),C('keyword.none'));
			//test($none);
                        $this->passive->text($none);
                    }
                }
                //文本消息 
            }
            D('WxTextMessage')->saveTxtMsg($this->postObj);
        } else {
            echo "Input something...";
        }
    }

    /***
     * 快速邀请
     */
    private function tribe_kaisu_yaoqing($keyword){
        //检查是否为快速邀请
        $eventKey=M('WxMenu')->where(array(
            'ToUserName'=>$this->postObj->ToUserName,
            'FromUserName'=>$this->postObj->FromUserName,
            'MsgType'=>'event',
            'Event'=>'CLICK'
        ))->order("CreateTime desc ")->getField(" Eventkey ");
        if($eventKey!=WxMenuEnum::Tribe_YaoQing){
            return false;
        }
        $topic=M('Topic')->where(array(
            'OpenId'=>$this->postObj->FromUserName,
            'MpOriginalId'=>$this->postObj->ToUserName,
            'Statue'=>1,
            'TopicId'=>$keyword
        ))->find();
        if(!$topic){
            //输入错误 检查上次输入是否为邀请的错误信息
            $msg_type=M('wx_text_message')->where(array(
                'ToUserName'=>$this->postObj->ToUserName,
                'FromUserName'=>$this->postObj->FromUserName
            ))->order("CreateTime desc ")->getField(" MsgType ");
            if($msg_type=='yaoqing_key_error'){
                //第二次错误 结束快速邀请请求
                return false;
            }else{
                //再给一次机会 继续走邀请 提示要输入正确
                $this->postObj->MsgType = 'yaoqing_key_error';
                $this->passive->text("请回复正确的部落编号");
                return true;
            }
            return false;
        }else{
            //得到数据返回信息
            $this->postObj->MsgType = 'yaoqing_key_success';
            $nickname=M('WxUser')->where('openid="%s"',$this->postObj->FromUserName)->getField('nickname');

            $data=array(
                array(
                    'img'=>web_site.'/Public/img/wxlogoa.png',
                    'title'=>$nickname.'的部落邀请：'.$topic['TopicTitle'],
                    'desc'=>'该部落的邀请函已经生成，长按本消息即可转发给朋友们，还可以点击进去查看详情',
                    'url'=>web_site.'/t/tribe/info/id/'.$topic['TopicId'].'/mp/%mp/u/'.php_encrypt($this->postObj->FromUserName)
                )
            );
            //回复图文消息
            $this->passive->news($data);
            return true;
        }
    }
    /**
     * 参与讨论
     */
    private function quickKeyword() {
        $quick = M('wx_text_message')->where(array(
                    'ToUserName' => $this->postObj->ToUserName,
                    'FromUserName' => $this->postObj->FromUserName,
                    'MsgType' => 'quick',
                    'Statue' => '1'
                ))->find();
        if (!$quick) {
            return false;
        }
        //判断参与时间
        $endtime = $quick['EndTime'];
        if ($endtime <= time()) {
            //更新为关闭状态
            M('wx_text_message')->where('Id=' . $quick['Id'])->save(array('Statue' => 0));
            return false;
        }
        //参与其中
        //取得参与的萌想编号
        $topicId = substr($quick['Content'], 1);
        $topic = M('Topic')->find($topicId);
        //查找编号
        $data = array(
            "TopicId" => $topicId,
            "ArticleContent" => '',
            "ArticleImg" => '',
            'CreateDate' => now_time(),
            'CreateUser' => UserSession::GetUserId(),
            'UpdateDate' => now_time(),
            'UpdateUser' => UserSession::GetUserId(),
            'Statue' => 1,
            'OpenId' => $this->postObj->FromUserName
        );
        if ($this->postObj->Content) {
            $data['ArticleContent'] = $this->postObj->Content;
        }
        if ($this->postObj->PicUrl) {
            $data['ArticleImg'] = $this->postObj->PicUrl;
        }
        $newarticleid = M('topicArticle')->add($data);
        if ($newarticleid) {
            M('Topic')->where('TopicId='.$topicId)->save(array('LastDate'=>  now_time()));
            $tip = C('success.quick');
        } else {
            $tip = C('error.quickserver');
        }
        D('WxTopic')->alter($topicId, $data);
        //结束时间增加五分钟
        M('wx_text_message')->where('Id=' . $quick['Id'])->save(array('EndTime' => (time() + 300)));
        $f = array(
            "%openid" => $this->postObj->FromUserName,
            "%topicid" => $topic['TopicId'],
            "%topictitle" => $topic['TopicTitle'],
        );
        $tip = filterkey($tip, $f);
        $this->passive->text($tip);
        return true;
    }

    /**
     * 快速参与
     * 格式
     * #12 内容
     * #12
     */
    private function quickTopic() {
        //关闭快速的萌想参与
        M('wx_text_message')->where("FromUserName='%s' and ToUserName='%s' and MsgType='quick' and Statue=1", $this->postObj->FromUserName, $this->postObj->ToUserName)->save(array('Statue' => 0));
        $this->postObj->MsgType = 'quick';
        $keyword = trim($this->postObj->Content);
        if (stripos($keyword, "#") != 0) {
            $this->passive->text(C('error.quick'));
            return;
        }
        //截取字符串
        $keyword = substr($keyword, 1);
        //分割字符串
        $a = explode(' ', $keyword, 2);
        if (is_numeric($a[0])) {
            //检测是否存在有效萌想编号
            $topic = M('Topic')->where(" Statue=1 and TopicId=" . $a[0] . " and MpOriginalId='" . $this->postObj->ToUserName . "'")->find();
            if ($topic) {
                //正确、、quicknot
                if (isset($a[1])) {
                    //有内容一次讨论
                    $data = array(
                        "TopicId" => $topic['TopicId'],
                        "ArticleContent" => $a[1],
                        "ArticleImg" => '',
                        'CreateDate' => now_time(),
                        'CreateUser' => UserSession::GetUserId(),
                        'UpdateDate' => now_time(),
                        'UpdateUser' => UserSession::GetUserId(),
                        'Statue' => 1,
                        'OpenId' => $this->postObj->FromUserName
                    );
                    $newarticleid = M('topicArticle')->add($data);
                    if ($newarticleid) {
                        M('Topic')->where('TopicId='.$topic['TopicId'])->save(array('LastDate'=>  now_time()));
                        D('WxTopic')->alter($topic['TopicId'], $data);
                        $tip = C('success.quick');
                    } else {
                        $tip = C('error.quickserver');
                    }
                    $this->postObj->Statue = 0;
                } else {
                    $this->postObj->Statue = 1;
                    $this->postObj->EndTime = time() + 300;
                    $tip = C('success.wait');
                }
            } else {
                $tip = C('error.quicknot');
                $this->postObj->Statue = 0;
            }
        } else {
            $tip = C('error.quick');
            $this->postObj->Statue = 0;
        }
        if ($tip) {
            $a = array(
                "%openid" => $this->postObj->FromUserName,
                "%topicid" => $topic['TopicId'],
                "%topictitle" => $topic['TopicTitle'],
                '%mp' => php_encrypt($this->postObj->ToUserName)
            );
            $tip = filterkey($tip, $a);
            $this->passive->text($tip);
        }
    }

}
