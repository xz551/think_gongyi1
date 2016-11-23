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
		
    </head>
    <body>
        <?php W('Layout/header');?>
        <link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/concur/css/help_list.css?1">
<script type="text/javascript">
	$(function(){
		/*去掉奇数所对应的下边的对象的右边距*/
		var res_list = $(".tuijian_list > li");
		for(var i=1;i<res_list.length;i++){
			res_list.eq(i).css("margin-right","0px");
			i++;
		}
		$(".hp_pjt_right > dl").hover(function(){
			$(this).find('.wraper_detail_info').show();
		},function(){
			$(this).find('.wraper_detail_info').hide();
		});
		/*分页*/ 
		$(".wraper_paging ul li").hover(function(){
			$(this).addClass("mouse_hover").siblings().removeClass("mouse_hover");
		});  
		$(".wraper_paging ul .curr").addClass("mouse_hover");
		var type=$(".type").attr("data-class");
		var status=$(".status").attr("data-class");
		var label=$(".label").attr("data-class");
		var pro=$(".pro").attr("data-class");
		$(".status_"+status).addClass("sp_cur");
		$(".type_"+type).addClass("sp_cur");
		$(".label_"+label).addClass("sp_cur");
		$(".pro_"+pro).addClass("sp_cur");
	
	});
</script>
<div class="wraper_main clearfix">
		<div id='mask' style="display: none;"></div>
		<!-- 左边部分[ -->
		<div class="slidebar_left">
			<?php W('Donate/left_top');?>
			<!-- 左—CENTER[ -->
			<!-- <div class="slider_lf_center">
				<h3>推荐资源</h3>
				<ul class="tuijian_list clearfix">
					<li>
						<a href="#" class="tjlist_top">
							<img src="images/qq8.jpg">
							<span class="tjlist_top_choice"><i class="icon_money"></i><i class="icon_gift"></i><i class="icon_heart"></i></span>
						</a>
						<a href="#" class="thlist_bottom">
							求助求助求助求助求助求助求助求助求助求助求助求助
						</a>
					</li>
				</ul>
			</div> --><!-- ]左—CENTER -->
		</div><!-- ]左边部分 -->
		<!-- 右边部分[ -->
		<div class="wpr_main_right">
			<?php W('Donate/main_top');?>
			<?php if($concurInfo): if(is_array($concurInfo)): $i = 0; $__LIST__ = $concurInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><!-- 一个项目[ -->
				<div class="help_project">
					<a href="http://www.gy.com/T/Concurinfo/index/id/<?php echo ($vo['id']); ?>" class="hp_pjt_left" target="_blank" title="<?php echo ($vo["title"]); ?>"><img src="<?php echo ($vo["image"]); ?>"></a>
					<div class="hp_pjt_right">
						<h3><a href="http://www.gy.com/T/Concurinfo/index/id/<?php echo ($vo['id']); ?>" target="_blank" title="<?php echo ($vo["title"]); ?>"><?php echo ($vo["title"]); ?></a></h3>
						<span class="form_people">求助发起人：<a href="<?php echo userUrl($vo['creator']);?>" target="_blank" title="<?php echo ($vo["nickname"]); ?>"><?php echo ($vo["nickname"]); ?></a></span>
						<?php if($vo['money']): ?><dl>
								<dt class="icon_money"></dt>
								<dd>
									<p class="progress_bar"><span class="this_progress"></span></p>
									<p class="progress_detail"><span class="pd_lt">已捐助：<i>760.00</i>元</span><span class="pd_rt">目标：<?php echo ($vo["money"]); ?></span></p>
								</dd>
							</dl><?php endif; ?>
						<?php if($vo['supplies']): ?><dl>
								<dt class="icon_gift"></dt>
								<dd><p class="progress_det_cont" title="需要<?php echo ($vo['supplies'][0]['res']); ?>等物">需要<?php echo ($vo['supplies'][0]['res']); ?>等物</p></dd>
								<!-- 隐藏的详细信息 -->
								<div class="wraper_detail_info">
									<i class="icon_arrow_up"></i>
									<div class="hover_detail">
										<?php if(is_array($vo['supplies'])): $i = 0; $__LIST__ = $vo['supplies'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><p><label><?php echo (str_ellipsis($v["name"],6)); ?>：</label><span class="sp_progress"><i style="width:<?php echo ($v['percent']); ?>;"></i></span><span class="sp_number"><?php echo ($v["yNum"]); ?>/<?php echo ($v["num"]); ?></span></p><?php endforeach; endif; else: echo "" ;endif; ?>
									</div>
								</div>
							</dl><?php endif; ?>
						<?php if($vo['is_service']): ?><dl>
								<dt class="icon_serivce"></dt><dd><p class="progress_det_cont">需要服务</p></dd>
							</dl><?php endif; ?>
					</div>
				</div><!-- ]一个项目 --><?php endforeach; endif; else: echo "" ;endif; ?>
			<!-- 分页 -->
				<div class="wraper_paging"><?php echo ($page); ?></div>
			<?php else: ?>
				<div class="none_msg">该条件下暂无相关求助项目</div><?php endif; ?>
		</div><!-- ]右边部分 -->
</div>
        <?php W('Layout/footer');?>
        
        <script type="text/javascript"></script>
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