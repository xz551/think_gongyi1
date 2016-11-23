<?php if (!defined('THINK_PATH')) exit();?><ul class="volunteer_content">
    <?php if(is_array($active)): $i = 0; $__LIST__ = $active;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li <?php if($i == 4): ?>style="margin-right:0px"<?php endif; ?> >
                                    <span class="Project_img">
                                        <a href="<?php echo ($vo['url']); ?>"><img width='177' height='124' src="<?php echo ($vo['image']); ?>"/></a>
                                    </span>
            <span class="Vtitle_text"><a href="<?php echo ($vo['url']); ?>"><?php echo ($vo['name']); ?></a></span>
            <span class="count_num"><span class="s_bule"><?php echo ($vo['usercount']); ?></span><br /> 总人数</span>
            <span class="count_num"><span class="s_yellow"><?php echo ($vo['allcount']); ?></span><br />参加人数</span>
            <span class="canjia" id="CanJia"><a href="<?php echo ($vo['url']); ?>">参加</a></span>
        </li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>