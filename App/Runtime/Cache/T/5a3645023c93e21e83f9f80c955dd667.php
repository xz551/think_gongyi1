<?php if (!defined('THINK_PATH')) exit();?>  
 <h3 class="h3_title"><i></i>爱心动态</h3>
 <?php if(!empty($donName)): if(is_array($donName)): $i = 0; $__LIST__ = $donName;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="col5_leader_info">
	     <a href="<?php if(!empty($v["anonymous"])): ?>javascript:void(0)<?php else: echo userUrl($v['uid']); endif; ?>" title="<?php echo ($v['nickname']); ?>" class="col5_leader_lt" target="_blank"><img src="<?php echo ($v['image']); ?>"></a>
	     <div class="col5_leader_rt">
	         <h3 class="col5_lrt_title">
	         	 <a href="<?php if(!empty($v["anonymous"])): ?>javascript:void(0)<?php else: echo userUrl($v['uid']); endif; ?>" class="helpren_name" target="_blank" title="<?php echo ($v['nickname']); ?>"><?php echo (str_ellipsis_new($v['nickname'],12)); ?></a>
		         <?php if(($v["type"]) == "11"): ?><img src="<?php echo STATIC_SERVER_URL;?>/concur/images/icon_v.png" title="认证个人"><?php else: ?><img src="<?php echo STATIC_SERVER_URL;?>/concur/images/icon_v1.png" title="认证组织"><?php endif; ?>
		         <a href="javascript:void(0)" class="pemail_btn pemail" data-toid="<?php echo ($v['uid']); ?>" title="私信"></a>
	         </h3>
	         <p class="col5_lrt_address"><?php echo ($v['userAddress']); ?></p>
	          <p>
				<?php if(empty($v['service'])): echo ($v['supplies']); ?>
				<?php else: ?>
					<?php echo ($v['service']); endif; ?>
			</p>
			<p><?php echo ($v['time']); ?></p>
	     </div>
	 </div><?php endforeach; endif; else: echo "" ;endif; ?>
<?php else: ?>
	<div class="none_msg">还没有人提出申请，还不快来申请~</div><?php endif; ?>