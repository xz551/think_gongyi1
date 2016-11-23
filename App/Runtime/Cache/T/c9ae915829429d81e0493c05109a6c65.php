<?php if (!defined('THINK_PATH')) exit();?>	<div class="welfare_ogm">
  		<div class="welfare_ogm_cent">
  			<?php if(is_array($org)): $i = 0; $__LIST__ = array_slice($org,0,7,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo ($vo['url']); ?>" target="_blank"><img src="<?php echo ($vo['photo_photo']); ?>"></a><?php endforeach; endif; else: echo "" ;endif; ?>
  		</div>  
		</div>
		
		<script type="text/javascript">
			$(function(){
					$('.welfare_ogm_cent a:first-child').css("border-left","none");
			});
		</script>