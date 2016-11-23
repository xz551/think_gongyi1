<?php if (!defined('THINK_PATH')) exit();?><ul class="volunteer_content">
    <?php if(is_array($project)): $i = 0; $__LIST__ = $project;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li <?php if($i == 4): ?>style="margin-right:0px"<?php endif; ?> >
                                    <span class="Project_img">
                                        <a href="<?php echo ($vo['url']); ?>"><img width='177' height='124' src="<?php echo ($vo['show_image']); ?>" /></a> 
                                    </span>
            <span class="Vtitle_text"><a href="<?php echo ($vo['url']); ?>"><?php echo ($vo['name']); ?></a></span>
            <span class="count_num"><span class="s_bule"><?php echo ($vo['needcount']); ?></span><br /> 总招募</span>
            <span class="count_num"><span class="s_yellow"><?php echo ($vo['usercount']); ?></span><br /> 已招募</span>
            <span class="baoming"><a href="<?php echo ($vo['url']); ?>">报名</a></span>
        </li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>