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
        <link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/concur/css/help_header.css">
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/concur/css/help_detail.css?0">
<link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/public/css/jquery.validator.css">
<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/public/js/jquery.validator.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/public/js/zh_CN.js"></script>
<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/public/js/popWindow.js"></script>
<script type="text/javascript" src="http://static.gy.com/usercenter/tp/js/message.js"></script>
<script type="text/javascript" src="http://static.gy.com/public/js/ajaxupload.js"></script>
<script type="text/javascript" src="http://static.gy.com/public/js/letv.video.js"></script>
<script type="text/javascript">
    $(function () {
    	//2分钟调用一次爱心动态信息
    	var p = 0;
    	var pageCount="<?php echo ($pageCount); ?>";
    	var id="<?php echo ($id); ?>";
    	showLove(id,p);
    	function showLove(id,p){
    		if(p<pageCount){
    			p += 1;
    		}else{
    			p=1;
    		}
   		   $.ajax({
       	    	 url:"/T/ConcurAjax/showLove",
       	    	 data:{id:id,p:p},
       	    	 type:"post",
       	    	 success:function(d){
       	    		 $(".wpr_mclt_column7").html(d);
       	    	 }
       	    });
    		setTimeout(function(){showLove(id,p);},120000);
    	}
        /*对textarea 和 input 的输入实时监听事件代码*/
        $.event.special.valuechange = {
            teardown: function (namespaces) {
                $(this).unbind('.valuechange');
            },
            handler: function (e) {
                $.event.special.valuechange.triggerChanged($(this));
            },
            add: function (obj) {
                $(this).on('keyup.valuechange cut.valuechange paste.valuechange input.valuechange', obj.selector, $.event.special.valuechange.handler)
            },
            triggerChanged: function (element) {
                var current = element[0].contentEditable === 'true' ? element.html() : element.val()
                        , previous = typeof element.data('previous') === 'undefined' ? element[0].defaultValue : element.data('previous')
                if (current !== previous) {
                    element.trigger('valuechange', [element.data('previous')])
                    element.data('previous', current)
                }
            }
        }
        //动态设置容器向上的三角的位置
        var divCont = $(".wraper_tab_cont > div"),divLen = divCont.length,tabUl = $(".wpr_tab_top"),lt = 60;
        for(var i=0;i<divLen;i++){
                divCont.eq(i).find("i.detail_arrow_up").css("left",lt);
                lt+=110;
        }
        tabUl.find('li').eq(0).addClass("tab_cur");
        divCont.eq(tabUl.find("li.tab_cur").index()).show();
        $(".wpr_tab_top > li").hover(function(){
                var _this = $(this),_ind = _this.index(),_div = $(".wraper_tab_cont > div");
                _this.addClass("tab_cur").siblings().removeClass("tab_cur");
                _div.eq(_ind).show(0,function(){
                        $(this).parent().css({
                                height:$(this).outerHeight()
                        })
                }).siblings().hide();
        });
		
		/*
			设置初始化容器的高度
		*/
		initHeight();
		function initHeight(){
			var a=$(".wraper_tab_cont"),b=a.find('div:visible').outerHeight();
			a.height(b);
		}

        /*
         解释爱心认证员
         */
        $(".sp_small").hover(function () {
            $(".wpr_aixinrz_cont").show();
        }, function () {
            $(".wpr_aixinrz_cont").hide();
        });
       
        /**
         *	下拉图标，显示完整内容
         */
        var desc_cont = $(".help_desc_cont").height(),desc_xiala = $(".img_xiala");
        desc_cont>135?desc_xiala.show():desc_xiala.hide();
        desc_xiala.click(function(){
                var t = $(this),id = t.attr('data-id');
                if(id == 0){
                        t.attr('data-id','1').prop('src','<?php echo STATIC_SERVER_URL;?>/concur/images/xl_up.png').prev('div').css('max-height','10000px');
                }else{
                        t.attr('data-id','0').prop('src','<?php echo STATIC_SERVER_URL;?>/concur/images/xl_down.png').prev('div').css('max-height','135px');
                }
        });


        /*
         为textarea模拟placeholder
         */
        $("textarea").focus(function () {
            $(this).parent().next('.sp_placeholder').remove();
        });
        $('.sp_tishi1').click(function () {
            $(this).remove();
            $('.help_fankui').focus();
        });
        $('.sp_tishi2').click(function () {
            $(this).remove();
            $('.txtarea_jiyu').focus();
        });
        /*
        分享弹出窗
        */
       $(".wpr_mtl_share").click(function () {
           var url = "/T/ConcurAjax/shareSub";
           var sid = "<?php echo ($id); ?>";
           var name = "<?php echo ($info['title']); ?>";
           $.ajax({
               type: "POST",
               url: url,
               data: "sid="+sid+"&name="+name,
               success: function (msg) {
                   $(".wpr_mtl_share").popWindow({
                       width: "548",
                       height: "268",
                       title: "分享到：",
                       content: msg,
                       autoshow: true,
                       id: "box2",
                       callback: function (a, box) {
                           uid = a.data('uid');
                           obj = a.parent().parent().parent();
                       },
                       button: {
                       }
                   });
               }
           });

       });

       $(".txtBox").focus(function () {
           $(this).css("color", "#333");
       });
       $("#box2").find(".popOkBtn").css({float: "right", marginRight: '0px'}).text("分享");
       $("#box2").find("#winfooter").css({"text-align": "right", "color": "#626262"}).append("你最多可以输入<strong class='font_length1'>140</strong>个字符&nbsp;&nbsp;");
    });
</script>
<!-- wraper_main[ -->
<div class="wraper_main clearfix">
    <!-- main_top[ -->
    <div class="wpr_main_top">
        <!-- main_top_左边[ -->
        <div class="wpr_mtop_lt">
            <?php if(($recommend) == "1"): if(($htype) == "1"): ?><img src="<?php echo STATIC_SERVER_URL;?>/concur/images/icon_rzzy.png" class="img_rzbz">
                <?php else: ?>
                    <img src="<?php echo STATIC_SERVER_URL;?>/concur/images/icon_rzbz.png" class="img_rzbz"><?php endif; endif; ?>
            <a href="javascript:void(0)" class="wpr_mtl_img"><img src="<?php echo ($image); ?>"></a>
            <a href="javascript:void(0)" class="wpr_mtl_share">分享(<?php echo ($sharenum); ?>)</a>
        </div><!-- ]main_top_左边 -->
        <!-- main_top_右边[ -->
        <div class="wpr_mtop_rt">
            <h3><a href="javascript:void(0)"><?php echo ($info["title"]); ?></a><span class="sp_state"><?php echo ($statusName); ?></span></h3>
            <p class="wpr_mtoprt_detail"><label>类别标签：</label>
            <?php if(is_array($label)): $i = 0; $__LIST__ = $label;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; echo ($vo); ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
            </p>
            <p class="wpr_mtoprt_detail"><label><?php if(($htype) == "1"): ?>资源有效期<?php else: ?>求助时间<?php endif; ?>：</label><?php echo (date("Y-m-d",$info["start_time"])); ?> - <?php echo (date("Y-m-d",$info["end_time"])); ?></p>
            <p class="wpr_mtoprt_detail"><label><?php if(($htype) == "1"): ?>所在地点<?php else: ?>受助地点<?php endif; ?>：</label><?php echo ($area); ?></p>
            <p class="wpr_mtoprt_detail"><label><?php if(($htype) == "1"): ?>捐助对象<?php else: ?>受助对象<?php endif; ?>：</label><?php echo ($info["server_for"]); ?></p>
            <?php echo W('Concur/concurInfo',array($info['id'],$info['is_service'],$info['is_supplies'],$info['money'],$info['type'],$isown));?>
        </div><!-- ]main_top_右边 -->
    </div><!-- ]main_top -->
    <!-- main_center[ -->
    <div class="wpr_main_center clearfix">
        <!-- main_center_左边[ -->
        <div class="wpr_mcenter_lt">
            <!-- Column1[ -->
            <div class="wpr_mclt_column1">
                <h3 class="h3_title"><i></i><?php if(($htype) == "1"): ?>资源<?php else: ?>求助<?php endif; ?>描述</h3>
                <div class="wpr_help_detail">
                    <div class="wpr_help_desc">
                            <div class="help_desc_cont">
                                <?php echo ($info['summary']); ?>
                            </div>
                    </div>
                    <img src="<?php echo STATIC_SERVER_URL;?>/concur/images/xl_down.png" class="img_xiala" data-id="0">
                </div>
            </div><!-- ]Column1 -->
            <!-- 反馈信息 -->
            <?php W('Concur/feedback',array($info['creator']));?>
            <!-- 捐助申请名单 -->
            <?php W('Donate/main_donate');?>
            <!-- 讨论区 -->
            <?php W('Donate/reply');?>
        </div>
        <!-- ]main_center_右边 -->
        <div class="wpr_mcenter_rt">
            <!-- 创建者信息 -->
            <?php W('Concur/creator',array($info['creator']));?><!-- ]Column5 -->
            <!-- 爱心认证-->
            <?php W('Concur/iLove',array($info['type']));?>
            <!-- 爱心动态 -->
            <div class="wpr_mclt_column7"></div>
        </div><!-- ]main_center_右边 -->
    </div><!-- ]main_center -->
    <!-- ]main_bottom[ -->
    <!--
    <div class="wpr_main_bottom">
        <h3 class="h3_title"><i></i>他们也在求助</h3>
        <ul class="wpr_mbhelp_list">
            <li>
                <a href="#" class="wpr_mbhl_pic"><img src="<?php echo STATIC_SERVER_URL;?>/concur/images/qq7.jpg"></a>
                <a href="#" class="wpr_mbhl_title">今天天气真好适合睡觉。今天天气真好适合睡觉.</a>
            </li>
            <li>
                <a href="#" class="wpr_mbhl_pic"><img src="<?php echo STATIC_SERVER_URL;?>/concur/images/qq7.jpg"></a>
                <a href="#" class="wpr_mbhl_title">今天天气真好适合睡觉。今天天气真好适合睡觉.</a>
            </li>
            <li>
                <a href="#" class="wpr_mbhl_pic"><img src="<?php echo STATIC_SERVER_URL;?>/concur/images/qq7.jpg"></a>
                <a href="#" class="wpr_mbhl_title">今天天气真好适合睡觉。今天天气真好适合睡觉.</a>
            </li>
            <li>
                <a href="#" class="wpr_mbhl_pic"><img src="<?php echo STATIC_SERVER_URL;?>/concur/images/qq7.jpg"></a>
                <a href="#" class="wpr_mbhl_title">今天天气真好适合睡觉。今天天气真好适合睡觉.</a>
            </li>
            <li>
                <a href="#" class="wpr_mbhl_pic"><img src="<?php echo STATIC_SERVER_URL;?>/concur/images/qq7.jpg"></a>
                <a href="#" class="wpr_mbhl_title">今天天气真好适合睡觉。今天天气真好适合睡觉.</a>
            </li>
        </ul>
    </div>
    -->
    <p class="text_info4">发起人确认以上发布内容合法且真实有效，中青公益作为平台方支持不承担任何后果。</p>
</div><!-- ]wraper_main -->
<script type="text/javascript">
    $("#form_zxjz").validator({
        stopOnError: false,
        fields: {
            hp_money: {rule: "required", msg: "标题不能为空"}
        }
    });
</script>
<?php W('Common/Login/showLogin');?>
<?php W('Common/Message/privateLetter');?>
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