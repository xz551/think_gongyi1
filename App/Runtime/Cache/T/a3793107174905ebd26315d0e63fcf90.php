<?php if (!defined('THINK_PATH')) exit();?><div id="slides">
    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['url']): ?><a href="<?php echo ($vo['url']); ?>" target="_blank">
                <img width="760" height='400' src="<?php echo ($vo['image']); ?>"/>
                <span class="this_textinfo1"><?php echo ($vo['title']); ?></span>
            </a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
</div>