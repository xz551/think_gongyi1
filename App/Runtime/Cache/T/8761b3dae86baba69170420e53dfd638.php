<?php if (!defined('THINK_PATH')) exit();?><div class="wpr_mclt_column5">
    <h3 class="h3_title"><i></i><?php echo ($helpname); ?>发起人</h3>
    <div class="col5_leader_info">
        <a href="<?php echo (ucUrl($data['uid'])); ?>" class="col5_leader_lt" title="<?php echo ($data['nickname']); ?>" target="_blank"><img src="<?php echo ($data['photo']); ?>"></a>
        <div class="col5_leader_rt">
            <h3 class="col5_lrt_title"><a href="<?php echo (ucUrl($data['uid'])); ?>" class="helpren_name" title="<?php echo ($data['nickname']); ?>" target="_blank"><?php echo (str_ellipsis_new($data['nickname'],10)); ?></a>
            <?php if(($$data['type']) == "11"): ?><img src="<?php echo STATIC_SERVER_URL;?>/concur/images/icon_v.png" title="认证个人"><?php else: ?><img src="<?php echo STATIC_SERVER_URL;?>/concur/images/icon_v1.png" title="认证组织"><?php endif; ?>
			<a href="javascript:void(0)" class="pemail  pemail_btn" data-toid="<?php echo ($data['uid']); ?>" title="私信"></a></h3>
            <p class="col5_lrt_address"><?php echo ($data['address']); ?></p>
            <p>联系人：<?php echo ($data['real_name']); ?></p>
            <p>联系电话：<?php echo ($data['phone']); ?></p>
            <p>Email:<?php echo ($data['mail']); ?></p>
        </div>
    </div>
</div>