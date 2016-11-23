<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
	<title><?php echo ($title); ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	
	<link rel="stylesheet" href="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/css/psl_about_pjt.css"/>
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
            
<div id='getAjaxPage' data-url="<?php echo U('ajaxGetActive');?>" data-uid="<?php echo ($uid); ?>"><img src="http://static.gy.com/public/images/loading.gif"/></div>

		</div><!-- ]myhome_center_main -->
	</div><!-- ]Center -->
		<?php W('Layout/footer');?>

<script type="text/javascript">$(function(){
	   //ajax动态加载参加的活动||发起的活动
    var url = $("#getAjaxPage").attr('data-url');  
    var uid =$("#getAjaxPage").attr('data-uid');
    $.ajax({
        url: url,
        data:{uid:uid},
        success: function(msg){
            $("#getAjaxPage").html(msg);
            $(".wraper_paging a:contains(1):first").addClass('selected');
        }
    });
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