<?php if (!defined('THINK_PATH')) exit();?><ul class="Project_one fl">
    <li class="pjt_one_left">
        <a href="http://yijuan.gy.com/project/alleventlist/id/<?php echo ($event[0]['id']); ?>.html">
            <img src="<?php echo ($event[0]['show_image']); ?>" />
        </a>
    </li>
    <li class="pjt_one_right">
                        <span class="Project_one_title">
                            <a href="http://yijuan.gy.com/project/alleventlist/id/<?php echo ($event[0]['id']); ?>.html"><?php echo ($event[0]['name']); ?></a>
                        </span>
        <span class="Project_one_date">发起方:<?php echo ($event[0]['host']); ?><br/><?php echo ($event[0]['event_begin_time']); ?>-<?php echo ($event[0]['event_end_time']); ?></span>
        <span class="Project_one_state <?php echo ($event[1]['statue']==-1? "projrct_end":"  "); ?> "><?php echo ($event[0]['statue_text']); ?></span>
    </li>
</ul>
<ul class="Project_one fr">
    <li class="pjt_one_left"><a href="http://yijuan.gy.com/project/alleventlist/id/<?php echo ($event[1]['id']); ?>.html"><img src="<?php echo ($event[1]['show_image']); ?>"/></a></li>
    <li class="pjt_one_right">
        <span class="Project_one_title"><a href="http://yijuan.gy.com/project/alleventlist/id/<?php echo ($event[1]['id']); ?>.html"><?php echo ($event[1]['name']); ?></a></span>
        <span class="Project_one_date">发起方:<?php echo ($event[1]['host']); ?><br/><?php echo ($event[1]['event_begin_time']); ?>-<?php echo ($event[1]['event_end_time']); ?></span>
        <span class="Project_one_state <?php echo ($event[1]['statue']==-1? "projrct_end":" "); ?>"><?php echo ($event[1]['statue_text']); ?></span>
    </li>
</ul>