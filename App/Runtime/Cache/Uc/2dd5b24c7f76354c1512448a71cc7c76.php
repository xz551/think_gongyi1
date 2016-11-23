<?php if (!defined('THINK_PATH')) exit();?><!-- Header[ -->
<div class="myhome_header">
    <div class="myhome_header_cont">
        <a href="<?php echo ($gongyi); ?>" target="_blank"><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/zqgylogo.png" clsas="myhome_top_left"></a>
        <div class="myhome_top_right">
            <?php if(($isLogin) == "1"): ?><div class="myhome_users_wraper">
                <a href="<?php echo userUrl($uid);?>"><?php echo ($name); ?><i class="icon_arrow_down"></i></a>
                <ul class="users_menus_bottom">
                    <li><a href="<?php echo ($edit); ?>" class="edit_data">编辑资料</a></li>
                    <li><a href="<?php echo ($logout); ?>" >退出登录</a></li>
                </ul>
            </div><?php endif; ?>
            <nav class="myhome_nav_wraper">
                <ul>
                    <?php if(($isLogin) == "1"): ?><li><a href="<?php echo ($privateLetter); ?>" target="_blank">私信<?php if(($messageboxNum) > "0"): ?>&nbsp;<span>&nbsp;<?php echo ($messageboxNum); ?>&nbsp;</span><?php endif; ?></a></li>
                    <li><a href="<?php echo ($notice); ?>" target="_blank" >通知
                           <?php if(($noticeNum) != "0"): ?>&nbsp;<span>&nbsp;<?php echo ($noticeNum); ?>&nbsp;</span><?php endif; ?>
                        </a></li>
                    <?php else: ?>
                    <li><a href="<?php echo ($login); ?>">登录</a></li><?php endif; ?>
                    <li><a href="<?php echo ($gongyi); ?>" target="_blank" >中青公益</a></li>
                    <li><a href="<?php echo ($jiaUrl); ?>" target="_blank" class="zhiyuanjia">志愿+</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div><!-- ]Header -->