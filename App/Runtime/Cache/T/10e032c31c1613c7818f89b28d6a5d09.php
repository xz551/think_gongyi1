<?php if (!defined('THINK_PATH')) exit(); if($tag): ?><div class="mr_topbar"><a href="/T/Donate/index.html" >求助项目</a><a href="javascript:void(0)" class="a_cur">爱心资源</a></div>
<?php else: ?>
<div class="mr_topbar"><a href="javascript:void(0)" class="a_cur">求助项目</a><a href="/T/Donate/index/tag/1.html">爱心资源</a></div><?php endif; ?>
<div class="wpr_choice_items">
	<dl>
		<dt>状态：</dt><dd class="status" data-class='<?php echo ($status); ?>'><a href="<?php echo create_href('status','0');?>" class="status_0" >全部</a><a href="<?php echo create_href('status','1');?>" class="status_1">进行中</a><a href="<?php echo create_href('status','-1');?>" class="status_-1">已完成</a></dd>
	</dl>
	<dl>
		<dt><?php echo I('tag')=='1'?'资源类型':'求助类型';?>：</dt>
		<?php if(($tag) == "1"): ?><dd class="type" data-class='<?php echo ($type); ?>'><a href="<?php echo create_href('type','0');?>"  class="type_0">全部</a><a href="<?php echo create_href('type','-1');?>" class="type_-1">捐物资</a><a href="<?php echo create_href('type','-2');?>" class="type_-2">捐服务</a></dd>
		<?php else: ?>
			<dd class="type" data-class='<?php echo ($type); ?>'><a href="<?php echo create_href('type','0');?>"  class="type_0">全部</a><!-- <a href="<?php echo create_href('type','1');?>" class="type_1">求款项</a> --><a href="<?php echo create_href('type','-1');?>" class="type_-1">求物资</a><a href="<?php echo create_href('type','-2');?>" class="type_-2">求服务</a></dd><?php endif; ?>
	</dl>
	<dl>
		<dt>类别标签：</dt>
			<dd class="label" data-class="<?php echo ($label); ?>"><a href="<?php echo create_href('label','0');?>" class="label_0">全部</a>
				<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo (create_href('label',$vo["id"])); ?>" class="label_<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
			</dd>
	</dl>
	<dl>
		<dt>地域：</dt>
			<dd class="pro" data-class="<?php echo ($pro); ?>"><a href="<?php echo create_href('pro','0');?>" class="pro_0">全部</a>
				<?php if(is_array($province)): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="<?php echo (create_href('pro',$v["id"])); ?>" class="pro_<?php echo ($v["id"]); ?>"><?php echo ($v["class_name"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
			</dd>
	</dl>
</div>