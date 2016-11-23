<?php if (!defined('THINK_PATH')) exit();?>
<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/usercenter/group/js/ajaxpage.js"></script>  
<!-- 动态[ -->
<div class="wraper_dynamic">
    <h3 class="project_title">动态<i></i></h3>
    <!-- 一条动态[ -->
    <?php if(count($userDynamic) == 0): ?><div class="prompt_states">暂无动态！</div>
        <?php else: ?>

        <?php if(is_array($userDynamic)): $i = 0; $__LIST__ = $userDynamic;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($vo["type"]) == "Primaries"): ?><div class="dynamic_list">
                <a href="<?php echo userUrl($vo['uid']);?>"><img src="<?php echo ($vo["userPhoto"]); ?>" class="user_photos" title="<?php echo ($vo["nickname"]); ?>"></a>
                <div class="dynamic_list_right">
                    <p class="user_name_info"><a href="<?php echo userUrl($vo['uid']);?>" title="<?php echo ($vo["nickname"]); ?>"><?php echo ($vo["nickname"]); ?></a>
                    <?php if(($vo["isorg"]) == "1"): ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_v.png" title="认证组织">
                    <?php else: ?>
                    	<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_v.png" title="认证个人"><?php endif; echo ($vo["actionName"]); ?></p>
                    <div class="dynamic_activit_details">
                        <a href="<?php echo ($vo["url"]); ?>"><img src="<?php echo ($vo["image"]); ?>" class="activ_left_photo" title="<?php echo ($vo["title"]); ?>"></a>
                        <div class="this_project_details">
                            <span class="blue_strip"></span>
                            <a href="<?php echo ($vo["url"]); ?>" title="<?php echo ($vo["title"]); ?>"><?php echo ($vo["title"]); ?></a>
                            <p class="allphoto_list">
                                <?php if(is_array($vo["user"])): $i = 0; $__LIST__ = array_slice($vo["user"],0,10,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="<?php echo userUrl($key);?>" target='_blank'><img src="<?php echo ($v["image"]); ?>" title="<?php echo ($v["nickname"]); ?>"></a><?php endforeach; endif; else: echo "" ;endif; ?>
                            </p>
                            <p class="content_time"><?php echo ($vo["time"]); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <?php if(($vo["type"]) == "SuccessRaise"): ?><div class="dynamic_list">
                <a href="<?php echo userUrl($vo['uid']);?>"><img src="<?php echo ($vo["userPhoto"]); ?>" class="user_photos" title="<?php echo ($vo["nickname"]); ?>"></a>
                <div class="dynamic_list_right">
                    <p class="user_name_info"><a href="<?php echo userUrl($vo['uid']);?>" title="<?php echo ($vo["nickname"]); ?>"><?php echo ($vo["nickname"]); ?></a>
                    <?php if(($vo["isorg"]) == "1"): ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_v.png" title="认证组织">
                    <?php else: ?>
                    	<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_v.png" title="认证个人"><?php endif; echo ($vo["actionName"]); ?></p>
                    <div class="dynamic_activit_details">
                        <a href="<?php echo ($vo["url"]); ?>"><img src="<?php echo ($vo["image"]); ?>" class="activ_left_photo" title="<?php echo ($vo["title"]); ?>"></a>
                        <div class="this_project_details">
                            <span class="blue_strip"></span>
                            <a href="<?php echo ($vo["url"]); ?>" title="<?php echo ($vo["title"]); ?>"><?php echo ($vo["title"]); ?></a>
                            <p class="allphoto_list">
                                认捐方：
                                <?php if(is_array($vo["user"])): $i = 0; $__LIST__ = array_slice($vo["user"],0,10,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="<?php echo userUrl($key);?>" target='_blank'><img src="<?php echo ($v["image"]); ?>" title="<?php echo ($v["name"]); ?>"></a><?php endforeach; endif; else: echo "" ;endif; ?>
                            </p>
                            <p><?php echo ($vo["time"]); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php else: ?>          
            
            <?php if(in_array(($vo["type"]), explode(',',"ActiveCreate,ActiveJoin"))): ?><div class="dynamic_list">
                <a href="<?php echo userUrl($vo['uid']);?>"><img src="<?php echo ($vo["userPhoto"]); ?>" class="user_photos" title="<?php echo ($vo["nickname"]); ?>"></a>
                <div class="dynamic_list_right">
                    <p class="user_name_info"><a href="<?php echo userUrl($vo['uid']);?>" title="<?php echo ($vo["nickname"]); ?>"><?php echo ($vo["nickname"]); ?></a>
                        <?php if(($vo["isorg"]) == "1"): ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_v.png" title="认证组织"><?php else: ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_v.png" title="认证个人"><?php endif; echo ($vo["actionName"]); ?>
                    </p>
                    <div class="dynamic_activit_details">
                        <a href="<?php echo ($vo["url"]); ?>"><img src="<?php echo ($vo["image"]); ?>" class="activ_left_photo" title="<?php echo ($vo["title"]); ?>"></a>
                        <div class="this_project_details">
                            <span class="blue_strip"></span>
                            <a href="<?php echo ($vo["url"]); ?>" title="<?php echo ($vo["title"]); ?>"><?php echo ($vo["title"]); ?></a>
                            <p class="content_time"><?php echo ($vo["time"]); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php else: ?>
            <div class="dynamic_list">
                <a href="<?php echo userUrl($vo['uid']);?>"><img src="<?php echo ($vo["userPhoto"]); ?>" class="user_photos" title="<?php echo ($vo["nickname"]); ?>"></a>
                <div class="dynamic_list_right">
                    <p class="user_name_info"><a href="<?php echo userUrl($vo['uid']);?>" title="<?php echo ($vo["nickname"]); ?>"><?php echo ($vo["nickname"]); ?></a>
                        <?php if(($vo["isorg"]) == "1"): ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_v.png" title="认证组织"><?php else: ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_v.png" title="认证个人"><?php endif; echo ($vo["actionName"]); ?>
                    </p>
                    <div class="dynamic_activit_details">
                        <a href="<?php echo ($vo["url"]); ?>"><img src="<?php echo ($vo["image"]); ?>" class="activ_left_photo" title="<?php echo ($vo["title"]); ?>"></a>
                        <div class="this_project_details">
                            <span class="blue_strip"></span>
                            <a href="<?php echo ($vo["url"]); ?>" title="<?php echo ($vo["title"]); ?>"><?php echo ($vo["title"]); ?></a>
                            <p class="content_text">
                            <?php echo ($vo["introduce"]); ?>
                            </p>
                            <p class="content_time"><?php echo ($vo["time"]); ?></p>
                        </div>
                    </div>
                </div>
            </div><?php endif; endif; endif; endforeach; endif; else: echo "" ;endif; ?>



</div>

<div class="wraper_paging"><?php echo ($page); ?></div><?php endif; ?>
</div><!-- ]动态 -->