<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
	<title><?php echo ($title); ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	
	<link rel="stylesheet" href="<?php echo STATIC_SERVER_URL;?>/usercenter/org/css/ogz_home.css"/>
	<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/public/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/usercenter/group/js/popWindow.js"></script>
	<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/js/message.js"></script>
	<script type="text/javascript">
		var closeUrl="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/close_icon.png";
		var static_url="<?php echo STATIC_SERVER_URL;?>";
		var server_url="<?php echo SERVER_VISIT_URL;?>";
		$(function(){
			$(".myhome_users_wraper").hover(function(){
				$(this).find('ul').show("200");
			},function(){
				$(this).find('ul').hide();
			});
		});
	</script>
</head>
<body>
        <?php W('Layout/header');?>
	<!-- Center[ -->
	<div class="myhome_center">
		<!-- Center-Top[ -->
		<?php W('Layout/top');?>
		<!-- Center-Top[ -->
		<!-- myhome_center_options[ -->
		
		<!-- ]myhome_center_options -->
		<!-- myhome_center_main[ -->
		<div class="myhome_center_main">
		<?php W('Layout/left');?>
		<?php W('Layout/privateLetter');?>
            
<!-- 右边主体内容[ -->
<div class="myhome_cmain_right">
    <!-- 我所在的团体[ -->
    <div class="wraper_volunteer_group">
        <h3 class="project_title">组织简介<i></i></h3>
        <?php if(!empty($introduce)): ?><p class="group_introduc"><?php echo ($introduce); ?></p>
	    <?php else: ?>
	    	<div class="prompt_states">暂无简介<?php if(($isMine) == "1"): ?>，<a href="<?php echo UCENTER;?>/accountinfo/base.html"><添加组织简介></a><?php endif; ?></div><?php endif; ?>
        
    </div><!-- ]团体简介 -->
    <!-- 我的志愿者 -->
    <div class="my_volunteers">
       <?php if(!empty($userNum)): ?><h3 class="project_title">
       			<?php if(($isMine) == "1"): ?>我的志愿者<a href="<?php echo JIA_SERVER_URL;?>/volunteers/index.html" target="_blank">(全部<?php echo ($userNum); ?>)</a>
	        	<?php else: ?>
	        		组织的志愿者 <a href="javascript:void(0)" target="_blank">(全部<?php echo ($userNum); ?>)</a><?php endif; ?><i></i>
       		</h3>
       		<p class="my_volunt_list">
	            <?php if(is_array($userInfo)): $i = 0; $__LIST__ = $userInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo userUrl($vo['uid']);?>" title="<?php echo ($vo['nickname']); ?>" target="_blank"><img src="<?php echo ($vo["photo"]); ?>"></a><?php endforeach; endif; else: echo "" ;endif; ?>
        	</p>
        <?php else: ?>
        	<h3 class="project_title">
       			<?php if(($isMine) == "1"): ?>我的志愿者<?php else: ?>组织的志愿者<?php endif; ?><i></i>
       		</h3>
       		<div class="prompt_states">暂无志愿者！</div><?php endif; ?> 
        
        
        
        
        
        
    </div>

    <!-- 我所在的公益小组[ -->
    <div class="wraper_volunteer_group">
        <?php if(empty($group)): ?><h3 class="project_title"><?php if(($isMine) == "1"): ?>我<?php else: ?>组织<?php endif; ?>所在的公益小组<i></i></h3>
            <div class="prompt_states">尚未加入小组<?php if(($isMine) == "1"): ?>，<a href="http://www.gy.com/T/Group/FindGroup.html"><发现公益小组></a><?php endif; ?></div>
        <?php else: ?>
        <h3 class="project_title"><?php if(($isMine) == "1"): ?>我<?php else: ?>组织<?php endif; ?>所在的公益小组 <a href="http://www.gy.com/T/Group/liveGroup/id/<?php echo ($uid); ?>" >(全部<?php echo ($groupNum); ?>)</a><i></i></h3>
        <?php if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><!-- 小组1 -->
        <div class="group_details_info">
           <a href="<?php echo SERVER_VISIT_URL;?>/t/group/selgroup/id/<?php echo ($vo["id"]); ?>.html" ><img src="<?php echo ($vo["image"]); ?>" title="<?php echo ($vo["name"]); ?>"></a>
            <div class="group_introduc_text">
                <h3><a href="<?php echo SERVER_VISIT_URL;?>/t/group/selgroup/id/<?php echo ($vo["id"]); ?>.html" class="h3_title" title="<?php echo ($vo["name"]); ?>"><?php echo ($vo["name"]); ?></a></h3>
                <div class="grop_details_intr">
                    <?php echo ($vo["introduce"]); ?>
                </div>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
        
        
        
        
    </div><!-- ]我所在的公益小组 -->
    <div id='getAjaxPage' data-url="<?php echo U('ajaxGetDynamic');?>" data-uid="<?php echo ($uid); ?>"><img src="http://static.gy.com/public/images/loading.gif"/></div>
</div><!-- 右边主体内容[ -->
<?php W('Layout/Tipswindown');?>

		</div><!-- ]myhome_center_main -->
	</div><!-- ]Center -->
		<?php W('Layout/footer');?>

<script type="text/javascript">//ajax动态加载小组页面
    var url = $("#getAjaxPage").attr('data-url');
    var uid =$("#getAjaxPage").attr('data-uid');
    $.ajax({
        url: url,
        data:{uid:uid},
        success: function(msg){
            $("#getAjaxPage").html(msg);
            $(".wraper_paging a:contains(1):first").addClass('selected');
        }
    });;</script>
        <script type="text/javascript">
            var _hmt = _hmt || [];
            (function() {
                var hm = document.createElement("script");
                hm.src = "//hm.baidu.com/hm.js?32d80154d45fbea6c3447e50edbac692";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
            })();
        </script>

</body>
</html>