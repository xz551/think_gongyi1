<?php if (!defined('THINK_PATH')) exit();?>
<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/usercenter/group/js/ajaxpage.js"></script>  
     <?php if(!empty($donName)): ?><ul>
	     <?php if(is_array($donName)): $i = 0; $__LIST__ = $donName;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
	             <a href="<?php if(!empty($vo["anonymous"])): ?>javascript:void(0)<?php else: echo userUrl($vo['apply_uid']); endif; ?>" title="<?php echo ($vo['nickname']); ?>" class="jzmd_lt" target="_blank"><img src="<?php echo ($vo['image']); ?>"><span><?php echo ($vo['nickname']); ?></span></a>
	             <?php if(empty($vo['supplies'])): ?><span class="jzmd_ctr"><i><?php echo ($vo['service']); ?></i></span>
	             <?php else: ?>
	             	<span class="jzmd_ctr" title="<?php echo ($vo['supplies']); ?>"><i><?php echo (str_ellipsis_new($vo['supplies'],85)); ?></i></span><?php endif; ?>
	             <span class="jzmd_rt"><i><?php echo ($vo['time']); ?></i></span>
	         </li><?php endforeach; endif; else: echo "" ;endif; ?>    
	     </ul>
	   	 <?php if(($donCount) > "8"): ?><div class="wraper_paging"><?php echo ($page); ?></div><?php endif; ?>
     <?php else: ?>
     	<div class="none_msg">暂无申请人</div><?php endif; ?>