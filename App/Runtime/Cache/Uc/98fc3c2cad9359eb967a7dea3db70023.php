<?php if (!defined('THINK_PATH')) exit();?><div class="myhome_cmain_left" data-class='<?php echo ($info); ?>'>
	<ul>
		<li class='user_0'><a href="<?php echo userUrl($uid);?>">主页<span class='user_1'></span></a></li>
		<li class='project_0'><a href="<?php echo U('Project/index',array('uid'=>$uid));?>">发起项目<span class='project_1'></span></a></li>
		<li class='active_0'><a href="<?php echo U('Active/index',array('uid'=>$uid));?>">发起活动<span class='active_1'></span></a></li>
		<li class='concur_0'><a href="<?php echo U('Concur/index',array('uid'=>$uid));?>">求助与捐助<span class='concur_1'></span></a></li>
		<?php if(($showPic) == "1"): ?><li class='picturewall_0'><a href="<?php echo U('PictureWall/index');?>">图片墙<span class="picturewall_1"></span></a></li><?php endif; ?>
	</ul>
</div>
<script>
    var info = $(".myhome_cmain_left").attr('data-class').toLowerCase();
    $("."+info+"_0").addClass('add_arrow_bg');
    $("."+info+"_1").html('<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_arrow_right.png">');
</script>