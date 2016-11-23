<?php if (!defined('THINK_PATH')) exit(); if(is_array($nowSubject)): $i = 0; $__LIST__ = $nowSubject;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="wpr_group_list">
        <a href="<?php echo U('t/group/selgroup',array('id'=>$vo['id']));?>"><img src="<?php echo ($vo["image"]); ?>" class="wpr_gl_left"></a>
        <div class="wpr_gl_right">
            <a href="<?php echo U('t/group/selgroup',array('id'=>$vo['id']));?>"><?php echo ($vo["name"]); ?></a>
            <p class="group_details"><?php echo ($vo["introduce"]); ?></p>
            <p><a href="<?php echo userUrl($vo['creator']);?>" class="group_member"><?php echo ($vo["username"]); ?></a><span class="group_time"><?php echo ($vo["mtime"]); ?></span></p>
        </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
<script type="text/javascript">
    $(function () {
        $(".volunteer_content li:last-child").css("margin-right", "0px");
        var group_len = $(".wpr_group_list").length;
        for (var i = 1; i < group_len; i++) {
            $(".wpr_group_list").eq(i).css("margin-right", "0px");
            i++;
        }

    });
</script>