<?php if (!defined('THINK_PATH')) exit(); if($user_list): if(is_array($user_list)): $i = 0; $__LIST__ = $user_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="col5_leader_info">
            <a href="<?php echo (ucUrl($vo['uid'])); ?>" class="col5_leader_lt" title="<?php echo ($vo['nickname']); ?>" target="_blank"><img src="<?php echo ($vo['photo']); ?>"></a>
            <div class="col5_leader_rt">
                <h3 class="col5_lrt_title">
                    <a href="<?php echo (ucUrl($vo['uid'])); ?>" class="helpren_name" title="<?php echo ($vo['nickname']); ?>" target="_blank"><?php echo (str_ellipsis_new($vo['nickname'],12)); ?></a>
                    <img src="<?php echo ($vo['type']); ?>"><a href="javascript:void(0)" class="pemail  pemail_btn" data-toid="<?php echo ($vo['uid']); ?>" title="私信"></a>
                </h3>
                <p class="col5_lrt_address"><?php echo ($vo['address']); ?></p>
                <?php if(is_array($vo['org_list'])): $i = 0; $__LIST__ = $vo['org_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$org): $mod = ($i % 2 );++$i;?><p class="col5_lrt_ogmname"><?php echo ($org); ?> 志愿者</p><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
    <div class="none_msg">还没有人认证这个求助的真实性</div><?php endif; ?>