<?php if (!defined('THINK_PATH')) exit();?>
<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/usercenter/group/js/ajaxpage.js"></script>  
<!-- 右边主体内容[ -->
<div class="myhome_cmain_right">
	<?php if(!empty($act)): if(is_array($act)): foreach($act as $key=>$vo): ?><div class="wraper_about_pjt">
			<figure><figcaption>发起人</figcaption><a href="<?php echo userUrl($vo['uid']);?>" target="_blank"><img src="<?php echo ($vo['userPhoto']); ?>" title="<?php echo ($vo['nickname']); ?>"></a></figure>
			<div class="about_pjt_right">
				<a href="<?php echo YI_JUAN;?>/active/view/id/<?php echo ($vo['id']); ?>.html" target="_blank"><img src="<?php echo ($vo['image']); ?>" class="this_pjt_photo" title="<?php echo ($vo['name']); ?>"></a>
				<div class="this_pjt_details">
					<p class="this_pjt_title"><a href="<?php echo YI_JUAN;?>/active/view/id/<?php echo ($vo['id']); ?>.html" target="_blank" title="<?php echo ($vo['name']); ?>"><?php echo ($vo['name']); ?></a></p>
					<p class="this_pjt_time"><?php echo ($vo['create_date']); ?></p>
					<?php if($vo['uid'] != $vo['userId']): if($vo['status'] == 2): ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps3.png" class="pjt_state">
						<?php elseif($vo['status'] == 3): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps2.png" class="pjt_state">
						<?php elseif($vo['status'] == 4): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps5.png" class="pjt_state"><?php endif; ?>
					<?php else: ?>
						<?php if($vo['status'] == 2): ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps3.png" class="pjt_state">
							<p class="all_btn"><a href="<?php echo YI_JUAN;?>/active/joinuserlist/id/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">查看报名名单</a></p>
						<?php elseif($vo['status'] == 3): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps2.png" class="pjt_state">
							<p class="all_btn"><a href="<?php echo YI_JUAN;?>/active/joinuserlist/id/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">查看报名名单</a></p>
						<?php elseif($vo['status'] == 4): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps5.png" class="pjt_state">
							<p class="all_btn"><a href="<?php echo YI_JUAN;?>/active/joinuserlist/id/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">查看报名名单</a></p>
						<?php elseif($vo['status'] == 0): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps1.png" class="pjt_state">	
							<p class="all_btn"><a href="<?php if(($vo["type"]) == "21"): echo JIA_SERVER_URL;?>/Active/createActivity/actid/<?php echo ($vo['id']); ?>.html<?php else: echo YI_JUAN;?>/active/create/id/<?php echo ($vo['id']); ?>.html<?php endif; ?>" class="bm_manager" target="_blank">修改活动信息</a></p>
						<?php elseif($vo['status'] == -1): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps15.png" class="pjt_state">
							<p class="all_btn"><a href="<?php if(($vo["type"]) == "21"): echo JIA_SERVER_URL;?>/Active/createActivity/actid/<?php echo ($vo['id']); ?>.html<?php else: echo YI_JUAN;?>/active/create/id/<?php echo ($vo['id']); ?>.html<?php endif; ?>" class="bm_manager" target="_blank">修改活动信息</a></p><?php endif; endif; ?>		
				</div>
			</div>
		</div><?php endforeach; endif; ?>
	<div class="wraper_paging"><?php echo ($page); ?></div>
	<?php else: ?>
		<h3 class="project_title">相关活动<i></i></h3>
		<div class="prompt_states">暂无数据！</div><?php endif; ?>
</div><!-- 右边主体内容[ -->