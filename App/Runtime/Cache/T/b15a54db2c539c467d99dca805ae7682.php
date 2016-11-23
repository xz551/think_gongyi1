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
		<link rel="stylesheet" href="<?php echo STATIC_SERVER_URL;?>/usercenter/group/css/page6.css?0"/>
    </head>
    <body>
        <?php W('Layout/header');?>
        


<div class="wraper_main">
    <div class="wraper_main_left">
        <div class="wraper_menus_tab">
            <?php if(($uid) != "0"): ?><a href="<?php echo U('T/Group/usergroup',array('id'=>$uid));?>">我的公益小组</a><?php endif; ?>
            
            <a class="bottom_border" href='javascript:void(0)'>发现公益小组</a>
        </div>
        <div class="wraper_group_cont">
            <?php if(($isOrgVip) == "1"): ?><p class="create_gy_group"><a href="<?php echo U('T/Group/addGroup');?>" >创建小组</a></p>
            <?php else: ?>
                <p class="create_gy_group"><a href="javascript:void(0)" >创建小组</a></p>
                <script>                   
                    $('.create_gy_group > a').click(function(){
                        $(".promptHtmlText").text("只有认证的组织可以创建小组");
                        var login = <?php echo ($islogin); ?>;
                            if(login == 1){
                            $(this).popWindow({
                                width: "400",
                                height: "170",
                                content: $(".promptHtml").html(),
                                closePic: "<?php echo STATIC_SERVER_URL;?>/usercenter/group/images/close_icon.png",
                                id: "box1",
                                autoshow:'true',
                                callback: function (a, box) {
                                },
                                button: {                               
                                    ok: function (t, box) {
                                        box.hide();
                                        $("#mask1").hide();
                                    }
                                }
                            }); 
                            $("#box1").find("#winTitle").css("border-bottom", "none");
                        }else{
                             showbox();
                        }                        
                     });                   
                </script><?php endif; ?>
            <div class="class_label">
                <label>类别标签：</label>
                <p>
                    <a href="#"  class="selected getPage"  data-label="0">全部</a>
                    <?php if(is_array($attList)): $i = 0; $__LIST__ = $attList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="#" class="getPage"  data-label="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
                    <img class='loader' src="<?php echo STATIC_SERVER_URL;?>/yijuan/product/images/loader.gif">
                   
                </p>
            </div>           
            <div id='getAjaxPage' data-url="<?php echo U('t/Group/ajaxGetGroup');?>" data-condition=""></div>           
        </div>
    </div>

    <!-- 右边 -->
    <div class="wraper_main_right">
        <!-- bottom -->
        <div class="wpr_mr_bottom">
            <h3 class="project_title">正在讨论<i></i></h3>
            <?php if(is_array($nowSubject)): $i = 0; $__LIST__ = $nowSubject;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><!-- 一个小组 -->
            <div  class="new_create_group">
                <a href="<?php echo U('t/group/selgroup',array('id'=>$vo['group']['id']));?>" ><img src="<?php echo ($vo['group']["image"]); ?>" title="<?php echo ($vo['group']["name"]); ?>"></a>
                <div class="new_cr_detail">
                    <a href="<?php echo U('T/subject/subInfo',array('id'=>$vo['sid']));?>"  class='chat_title' title="<?php echo ($vo["title"]); ?>"><?php echo ($vo["title"]); ?></a>
                    <p class="chat_text"> 
                        <?php echo ($vo["content"]); ?>
                    </p>
                    <p class="form_info"><span class="form_info_name"><a href="<?php echo userUrl($vo['ruid']);?>" title="<?php echo ($vo["runame"]); ?>"><?php echo ($vo["runame"]); ?></a></span><span class="form_info_time"><?php echo ($vo["mtime"]); ?></span></p>
                </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>

          
        </div>
    </div>
</div><!-- wraper_main[ -->

<!--弹出层1[-->
<div class="promptHtml">
    <p class="promptHtmlText">只有认证的组织可以创建小组</p>
</div> <!--]弹出层1-->
<?php W('Common/Login/showLogin');?>


        <?php W('Layout/footer');?>
        <script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/public/js/popWindow.js"></script>
        <script type="text/javascript">$(function () {
		$(".class_label p a").hover(function(){
			$(this).addClass("hover").siblings().removeClass("hover");
		},function(){
			$(this).removeClass("hover");
		});
    });;//ajax动态加载小组页面
    var url = $("#getAjaxPage").attr('data-url');
    var condition = $("#getAjaxPage").attr("data-condition");
    $.ajax({
        type: "POST",
        url: url,
        data:condition,
        success: function(msg){
            $("#getAjaxPage").html(msg);
            $(".wraper_paging a:contains(1):first").addClass('selected');
            $(".loader").hide();
        }
    });
    
    var tag = 0; 
    $(".getPage").click(function(){
    if(tag==1){return;}
        $(".loader").show();
	var _this = $(this);
        var url = $("#getAjaxPage").attr('data-url');
        var label = "lid="+$(this).attr("data-label");
        $("#getAjaxPage").attr("data-condition",label);
        var condition = $("#getAjaxPage").attr("data-condition");

            tag = 1;
            $.ajax({
                type: "POST",
                url: url,
                data:condition,
                success: function(msg){
                    $("#getAjaxPage").html(msg);
                    //$(".wraper_paging a:contains(1):first").addClass('selected');
                     _this.addClass('selected').siblings().removeClass('selected');
                     tag = 0;
                     $(".loader").hide();
                }
            });
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