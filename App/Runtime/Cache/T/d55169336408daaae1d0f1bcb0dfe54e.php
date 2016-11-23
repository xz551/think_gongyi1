<?php if (!defined('THINK_PATH')) exit();?><div class="header">
    <div class="wrap2">

        <div class="wraper_top">
            <div class="top_bar">
                
                    <div class="top_bar_right">
                    <?php if(($isLogin) == "1"): ?><div class="top_users_wraper">
                        <a href="<?php echo userUrl($user['uid']);?>"><?php echo ($user["nickname"]); ?><i class="icon_arrow_down"></i></a>
                        <ul class="users_menus_bottom">
                            <li><a href="<?php echo ($edit); ?>" class="edit_data">编辑资料</a></li>
                            <li><a href="<?php echo ($logout); ?>">退出登录</a></li>
                        </ul>
                    </div>
                    <nav class="top_nav_wraper">
                        <ul>
                            <li><a href="<?php echo ($privateLetter); ?>">私信<?php if(($messageboxNum) > "0"): ?><span class="col_span">[<?php echo ($messageboxNum); ?>]</span><?php endif; ?></a></li>
                            <li><a href="<?php echo ($notice); ?>">通知<?php if(($noticeNum) > "0"): ?><span class="col_span">[<?php echo ($noticeNum); ?>]</span><?php endif; ?></a></li>
                            <li><a href="<?php echo SERVER_VISIT_URL;?>">中青公益</a></li>
                        </ul>
                    </nav>                
                    <?php else: ?>
                    <nav class="top_nav_wraper">
                        <ul>
                            <li><a href="<?php echo ($login); ?>">登录</a></li>
                            <li><a href="<?php echo ($register); ?>">注册</a></li>
                            <li><a href="<?php echo SERVER_VISIT_URL;?>">中青公益</a></li>
                        </ul>
                    </nav><?php endif; ?>
                        
                        
                    </div>
            </div>
        </div> 
		
	
	
        <div class="logo_loginbar">
            <div class="zqgongyi_logo"><a href="<?php echo SERVER_VISIT_URL;?>"><img src="<?php echo STATIC_SERVER_URL;?>/gongyi/images/zhongqing_logo.png"/></a></div>
            <div class="banner_text"><img src="<?php echo STATIC_SERVER_URL;?>/gongyi/images/banner_text.png"/></div>
        </div>
        <div class="nav">
            <div class="main_nav">
                <ul class="navbar clearfix">
                    <li class="li_items"><a href="/">首页</a></li>
                    <li class="li_items"><a href="<?php echo YIJUAN_VISIT_URL;?>/project/all.html">招募</a></li>
                    <li class="li_items"><a href="<?php echo YIJUAN_VISIT_URL;?>/active/all.html">活动</a></li>
                    <li class="li_items">
                    	<a href="javascript:void(0)">求助与捐助</a>&nbsp;<img src="<?php echo STATIC_SERVER_URL;?>/gongyi/images/nav_ico.png"/>
                    	<ul class="sub" style="display:none;">
                            <li><a href="<?php echo SERVER_VISIT_URL;?>/T/Donate/index.html" title="求助项目">求助项目</a></li>	
                            <li><a href="<?php echo SERVER_VISIT_URL;?>/T/Donate/index/tag/1.html" title="爱心资源">爱心资源</a></li>	
                        </ul>
                    </li>
                    <li class="li_items"><a href="<?php echo YIJUAN_VISIT_URL;?>/project/alleventlist.html">征集</a></li>
                    <li class="li_items">
                        <a href="javascript:void(0)">资讯</a>&nbsp;<img src="<?php echo STATIC_SERVER_URL;?>/gongyi/images/nav_ico.png"/>
                        <ul class="sub" style="display:none;">
                            <li><a href="http://www.gy.com/gong_yi_bao_dao.html" title="公益报道">公益报道</a></li>
                            <li><a href="http://www.gy.com/gong_yi_yu_qing.html" title="公益舆情">公益舆情</a></li>
                        </ul>
                    </li>
                    <li class="li_items">
                        <a href="javascript:void(0)">互动</a>&nbsp;<img src="<?php echo STATIC_SERVER_URL;?>/gongyi/images/nav_ico.png"/>
                        <ul class="sub"  style="display:none;">	
                            <li><a href="<?php echo SERVER_VISIT_URL;?>/t/group/usergroup" title="公益小组">公益小组</a></li>
                            <li><a href="<?php echo YIJUAN_VISIT_URL;?>/authuser/all.html" title="公益伙伴">公益伙伴</a></li>	
                            <li><a href="<?php echo SERVER_VISIT_URL;?>/t/video/" title="益视频">益视频</a></li>
                        </ul>
                    </li>
                    <li class="li_items"><a href="<?php echo JIA_SERVER_URL;?>">志愿+</a></li>
                </ul>		
            </div>
        </div>
    </div>
</div> 
<script>
	$(function(){
		var timeId = null;
		 /*导航栏*/
        $(".li_items,.sub").hover(function () {
		var _this = $(this);
		timeId = setTimeout(function(){
			 _this.children('.sub').show(500);
		},300);
           
        }, function () {
			clearTimeout(timeId);
            $(this).children('.sub').hide();
        });	
	});
</script>