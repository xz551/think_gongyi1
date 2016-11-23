<?php if (!defined('THINK_PATH')) exit();?><div class="for_help_items" id="IE8_fh_left" style="float:left">
    <ul>
        <?php if(is_array($goods)): $i = 0; $__LIST__ = array_slice($goods,0,6,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($vo['url']); ?>"><?php echo ($vo['name']); ?></a><a href="<?php echo ($vo['url']); ?>" id="renjuan">认捐</a></li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
</div>
<div class="for_help_items" id="IE8_fh_right" style="float:right">
    <ul>
        <?php if(is_array($goods)): $i = 0; $__LIST__ = array_slice($goods,6,6,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($vo['url']); ?>"><?php echo ($vo['name']); ?></a><a href="<?php echo ($vo['url']); ?>" id="renjuan">认捐</a></li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
</div>