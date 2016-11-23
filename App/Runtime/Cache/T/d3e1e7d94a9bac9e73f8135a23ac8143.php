<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="renderer" content="webkit" />
        <meta name="Author" content="北京中青华云新媒体科技有限公司" />
        <meta name="force-rendering" content="webkit" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
		<meta property="qc:admins" content="35124657676217767116727" />
		<meta property="qc:admins" content="3512465207767116727" />
        <title><?php echo ($title); if(!empty($title)): ?>&nbsp;|&nbsp;<?php endif; ?>中青公益-聚合青年公益力量</title>
        <meta name="Keywords" content="公益网站,中青公益,中青公益网,公益,慈善,志愿者,NGO,基金会,爱心,梦想,透明,正能量,招募,活动,求助,捐助,认捐,中青,zqgongyi,益涓,志愿云,青年成长,高校,科技项目,中青公益官网">
        <meta name="description" content="中青公益（gy.com）是依托于中国青年报的全新整合公益服务平台。作为国家“十二五”科技支撑项目（项目编号2013BAK09B00），致力于建设一个国家级的、统一的“国家志愿者公益服务支撑平台”，建立一套“透明捐助--志愿者统一管理--公益服务与互动--爱心回报”的国家志愿者公益服务模式。为广大热心公益的NGO组织、基金会、志愿者管理单位及爱心企业服务，推进公益事业及社会态度的正向引导。展现聚合青年公益力量，助力志愿者成长的公益新态度。"/>
        <script src="<?php echo STATIC_SERVER_URL;?>/public/js/jquery.js"></script>
        <script src="<?php echo STATIC_SERVER_URL;?>/public/js/jquery.slides.min.js"></script>
        <script src="<?php echo STATIC_SERVER_URL;?>/public/js/jquery.lazyload.js"></script>
        <script src="<?php echo STATIC_SERVER_URL;?>/public/js/defaultImg.js"></script>
        <!--[if IE 8]>
        <link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/gongyi/css/ie_8.css"/>
        <![endif]-->

        <link type="image/x-icon" rel="shortcut icon" href="<?php echo STATIC_SERVER_URL;?>/favicon.ico" />
        <link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/gongyi/css/header.css?1" />
        <link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/gongyi/css/footer.css?d" />
        <link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/gongyi/css/main.css?11" />
        <link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/usercenter/group/css/style.css?0">

        
        <script>var static_url="<?php echo STATIC_SERVER_URL;?>";</script>
		<link rel="stylesheet" href="<?php echo STATIC_SERVER_URL;?>/usercenter/group/css/page1.css?0"/>
    </head>
    <body>
        <?php W('Layout/header');?>
        


<div class="wraper_main">
    <?php if(!empty($info["groupNum"])): ?><div class="wraper_main_left">
            <div class="wraper_menus_tab">
                <a class="bottom_border">我的公益小组</a>
                <a href="<?php echo U('T/Group/FindGroup');?>" >发现公益小组</a>
            </div>
            <div class="wraper_group_cont">
                <!-- 一个小组 -->
                <?php if(is_array($info["groupInfo"])): $i = 0; $__LIST__ = $info["groupInfo"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="my_group_list">
                        <div class="group_top"><a href="<?php echo U('t/group/selgroup',array('id'=>$vo['id']));?>" ><img src="<?php echo ($vo["image"]); ?>" title="<?php echo ($vo["name"]); ?>"></a><a href="<?php echo U('t/group/selgroup',array('id'=>$vo['id']));?>" title="<?php echo ($vo["name"]); ?>"><?php echo ($vo["name"]); ?></a></div>
                        <?php if(!empty($vo["newReply"])): ?><div class="new_reply">最新回复</div>              
                        <?php if(is_array($vo["newReply"])): $i = 0; $__LIST__ = $vo["newReply"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><p><span class="replay_title"><a href="<?php echo U('t/subject/subinfo',array('id'=>$v['id']));?>" title="<?php echo ($v["title"]); ?>"><?php echo ($v["title"]); ?></a></span><span class="raply_num"><a href="<?php echo U('t/subject/subinfo',array('id'=>$v['id']));?>#discuss"><?php echo ($v["num"]); ?>回复</a></span><span class="raply_time"><?php echo ($v["mintime"]); ?></span></p><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                        <?php if(!empty($vo["newJoinUser"])): ?><div class="new_join">最新加入</div>
                        <div class="join_users_list">
                            <?php if(is_array($vo["newJoinUser"])): $i = 0; $__LIST__ = $vo["newJoinUser"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$photo): $mod = ($i % 2 );++$i;?><a href="<?php echo userUrl($photo['uid']);?>"><img src="<?php echo ($photo["photo"]); ?>" title="<?php echo ($photo["nickname"]); ?>"></a><?php endforeach; endif; else: echo "" ;endif; ?>
                        </div><?php endif; ?>
                        
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                <?php if(!empty($info["groupInfo"])): ?><!-- 分页 -->
                <div class="wraper_paging"><?php echo ($page); ?></div><?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/usercenter/group/css/page2.css">
        <div class="wraper_main_left">
            <div class="wraper_menus_tab">
                <a class="bottom_border"><?php if(($userType) != "2"): ?>我的<?php endif; ?>公益小组</a>
                <a href="<?php echo U('T/Group/FindGroup');?>" >发现公益小组</a>
            </div>
            <div class="wraper_group_cont">
                <p>还没有加入任何公益小组哈~</p>
                <p>先看看我们都有那些公益小组吧，没准儿有你喜欢的~</p>
                <a href="<?php echo U('T/Group/FindGroup');?>" ><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/group/images/icon_found.png">发现小组</a>
            </div>
        </div><?php endif; ?>
    <!-- 右边 -->
    <div class="wraper_main_right">
        <!-- top -->
        <div class="wpr_mr_top">
            <p class="mrtop_show_num"><a href="<?php echo U('t/group/liveGroup',array('id'=>$uid));?>" ><?php echo ($info["groupNum"]); ?></a><a href="<?php echo U('t/group/startSub',array('uid'=>$uid));?>"  class="a_center"><?php echo ($info["initiatedSubNum"]); ?></a><a href="<?php echo U('t/group/partakeSub',array('uid'=>$uid));?>" ><?php echo ($info["partakeSubNum"]); ?></a></p>
            <p class="mrtop_show_text"><span>所在的小组</span><span>发起的话题</span><span>参与的话题</span></p>
        </div>
        <!-- center -->
        <div class="wpr_mr_center">
            <h3 class="project_title">我访问过的小组<i></i></h3>
            <div class="visit_group_list">
                <?php if(is_array($isRead)): $i = 0; $__LIST__ = $isRead;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo U('t/group/selgroup',array('id'=>$vo['id']));?>" ><img src="<?php echo ($vo["image"]); ?>" style="max-height:60px;max-width:60px;"  title='<?php echo ($vo["name"]); ?>'></a><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        <!-- bottom -->
        <div class="wpr_mr_bottom">
            <h3 class="project_title">新创建的小组<i></i></h3>
            
            
            <?php if(is_array($info["newGroup"])): $i = 0; $__LIST__ = $info["newGroup"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><!-- 一个小组 -->
            <div class="new_create_group">
                <a href="<?php echo U('t/group/selgroup',array('id'=>$vo['id']));?>" ><img src="<?php echo ($vo["image"]); ?>" title="<?php echo ($vo["name"]); ?>"></a>
                <div class="new_cr_detail">
                    <a href="<?php echo U('t/group/selgroup',array('id'=>$vo['id']));?>" title="<?php echo ($vo["name"]); ?>"><?php echo ($vo["name"]); ?></a>
                    <p><?php echo ($vo["introduce"]); ?></p>
                </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
           

        </div>
    </div>
</div>
        <?php W('Layout/footer');?>
        <script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/usercenter/group/js/paging.js"></script>
        <script type="text/javascript">$(function () {
        var group_len = $(".visit_group_list a").length;
        for (var i = 4; i < group_len; i++) {
            $(".visit_group_list a").eq(i).css("margin-right", "0px");
            i += 4;
        }
    });;</script>
        <script type="text/javascript">
            $(function() {
                $('.lazy').lazyload();
                /*用户菜单js*/
                $(".top_users_wraper").hover(function(){
                        $(this).find('ul').show("200");
                },function(){
                        $(this).find('ul').hide();
                });

            });
        </script>
    </body>
</html>