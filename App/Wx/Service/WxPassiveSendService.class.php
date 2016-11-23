<?php

namespace Wx\Service;

/**
 * 相应微信被动消息
 *
 * @author Administrator
 */
class WxPassiveSendService {

    public $postObj;

    /**
     * 响应发送文本消息
     * @param type $postObj 接收的对象信息
     * @param type $content 发送给用户的信息
     */
    public function text($content) {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";
        $resultStr = sprintf($textTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), 'text', $content);
        echo $resultStr;
    }

    /**
     * 图片消息
     * @param type $postObj
     */
    public function image($media_id) {
        $imageTpl = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    <Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                </xml>
                ';

        $media_id = $media_id ? $media_id : "Fw8kCmB9lXIk3JTwoWoSTceEbRmTDGNCMJV6t7jucSKhpeNYf7pXsqVBb_Sfk-4-";
        $resultStr = sprintf($imageTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $media_id);
        echo $resultStr;
    }

    /**
     * 语音消息
     */
    public function voice($media_id) {
        $voiceTpl = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%d</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    <Voice>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>
                    </xml>
                ';
        $media_id = $media_id ? $media_id : "JmipHM1GL0ds9gRPUk1zdnatdcBYI1-npIylo5Ks-AozEfotPbXOQh82-sT41Igx";
        $resultStr = sprintf($voiceTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $media_id);
        echo $resultStr;
    }

    /**
     * 视频消息
     * @param type $postObj
     * @param type $media_id
     */
    public function video($media_id) {
        $videoTpl = '<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%d</CreateTime>
                <MsgType><![CDATA[video]]></MsgType>
                <Video>
                        <MediaId><![CDATA[%s]]></MediaId>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                </Video> 
        </xml>';
        $media_id = $media_id ? $media_id : "R-JHkGLp6oYwuzGYOIK3sPvTJxxx4fOFkD7ALiJ--2Jhd4_AcQj1WZ0tnv3vIxTY";
        $resultStr = sprintf($videoTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $media_id, '这个是视频的标题', '这个是视频的描述');
        echo $resultStr;
    }

    /**
     * 音乐消息
     */
    public function music($ThumbMediaId) {
        $Title = '';
        $Description = '';
        $MusicUrl = ''; //音乐链接
        $HQMusicUrl = ''; //高质量音乐链接，WIFI环境优先使用该链接播放音乐
        //    $ThumbMediaId='';//缩略图的媒体id，通过上传多媒体文件，得到的id

        $musicTpl = '<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%d</CreateTime>
                        <MsgType><![CDATA[music]]></MsgType>
                        <Music>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <MusicUrl><![CDATA[%s]]></MusicUrl>
                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                        </Music>
                        </xml>
                ';
        $ThumbMediaId = $ThumbMediaId ? $ThumbMediaId : "JmipHM1GL0ds9gRPUk1zdnatdcBYI1-npIylo5Ks-AozEfotPbXOQh82-sT41Igx";
        $resultStr = sprintf($musicTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), $Title, $Description, $MusicUrl, $HQMusicUrl, $ThumbMediaId);
        echo $resultStr;
    }
    /**
     * 图文消息
     * @param type $postObj
     * @param type $Articles 集合
     */
    public function news($Articles = array()) {
        if ($Articles) {
            $topic = $Articles;
        } else {
            $topic = $this->Topic();
        }
        $newsTpl = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%d</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>%d</ArticleCount>
                    <Articles>';
        $item = '<item>
                            <Title><![CDATA[%s]]></Title> 
                            <Description><![CDATA[%s]]></Description>
                            <PicUrl><![CDATA[%s]]></PicUrl>
                            <Url><![CDATA[%s]]></Url>
                        </item>';

        foreach ($topic as $key => $value) {
            $mp = php_encrypt($this->postObj->ToUserName);
            if (isset($value['TopicId'])) {
                //发送的频道信息
                $article = M('topic_article')->where(' TopicId=' . $value['TopicId'] . ' and Statue=1 and  ArticleImg !=""')->order(' ArticleId desc')->find();
                $ArticleImg = $article['ArticleImg'];
                if ($key == 0) {
                    $ArticleImg = str_replace('/80/', '/', $ArticleImg);
                }
                $img = $article ? web_site . $ArticleImg : web_site.'/Public/img/wxlogoa.png';
                $title=$value['TopicTitle'];
                $desc=$value['TopicTitle'];
                if(stripos($title,'部落')!==false){
                    $url=web_site.'/t/tribe/users/id/'.$value['TopicId'].'/mp/'.$mp;
                }else {
                    $url = web_site . "/t/" . $value['TopicId'] . "?mp=" . $mp;
                }
            }else{
                $img=$value['img'];
                $title=$value['title'];
                $desc=$value['desc'];                
                $url= str_replace('%mp', $mp, $value['url']);
            }
            $newsTpl.=sprintf($item,$title, $desc, $img, $url);
        }
        $newsTpl.= '</Articles>
                   </xml> ';

        $resultStr = sprintf($newsTpl, $this->postObj->FromUserName, $this->postObj->ToUserName, time(), count($topic));
        echo $resultStr;
    }

    private function Topic() {
        $c = M('wx_text_message')->where(array('Content' => array('in', '集合,全部萌想,萌想'), 'FromUserName' => $this->postObj->FromUserName))->count();
        $c+=1;
        $topic_count = M('topic')->where('statue=1')->count();
        $allpage = ceil($topic_count / C('list_page_count'));
        if ($c <= $allpage) {
            $page = $c;
        } else {
            $page = $c % $allpage;
            if ($page == 0) {
                $page = $allpage;
            }
        }
        $topic = M('topic')->where('statue=1')->page($page . ',' . C('list_page_count'))->select();
        return $topic;
    }

}
