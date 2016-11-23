<?php if (!defined('THINK_PATH')) exit();?>	
<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/usercenter/group/js/ajaxpage.js"></script>  
<!-- 右边主体内容[ -->
<div class="myhome_cmain_right">
	<?php if(!empty($pro)): if(is_array($pro)): foreach($pro as $key=>$vo): ?><div class="wraper_about_pjt">
			<figure><figcaption>发起人</figcaption><a href="<?php echo userUrl($vo['creator']);?>" target="_blank"><img src="<?php echo ($vo['userPhoto']); ?>" title="<?php echo ($vo['nickname']); ?>"></a></figure>
			<div class="about_pjt_right">
				<a href="<?php echo YI_JUAN;?>/project/view/id/<?php echo ($vo['id']); ?>.html" target="_blank"><img src="<?php echo ($vo['show_image']); ?>" class="this_pjt_photo" title="<?php echo ($vo['name']); ?>"></a>
				<div class="this_pjt_details">
					<p class="this_pjt_title"><a href="<?php echo YI_JUAN;?>/project/view/id/<?php echo ($vo['id']); ?>.html" target="_blank" title="<?php echo ($vo['name']); ?>"><?php echo ($vo['name']); ?></a></p>
					<p class="this_pjt_time"><?php echo ($vo['create_date']); ?></p>
					<div class="this_pjt_text"><?php echo ($vo['summary']); ?></div>
					<?php if($vo['creator'] == $vo['userId']): if($vo['isedit'] == true ): if($vo['status'] == 888): ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps12.png" class="pjt_state">
							<p class="all_btn"><a href="<?php echo YI_JUAN;?>/verify/view/id/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">报名管理</a></p>
						<?php elseif($vo['status'] == 100): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps11.png" class="pjt_state">
							<p class="all_btn"><a href="<?php echo YI_JUAN;?>/verify/view/id/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">报名管理</a></p>
						<?php elseif($vo['status'] == -1): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps4.png" class="pjt_state">
							<if condition="$vo['type'] eq 2">
								<p class="all_btn">
									<a href="<?php echo YI_JUAN;?>/project/preview/id/<?php echo ($vo['id']); ?>/type/event/close/1.html" class="bm_manager" target="_blank">预览</a>
									<a href="<?php echo YI_JUAN;?>/project/createEventProject/id/<?php echo ($vo['event']); ?>/project/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">修改项目信息</a>
								</p>
							<?php else: ?>
								<p class="all_btn"><a href="<?php echo JIA_SERVER_URL;?>/Project/updateProject/id/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">修改项目信息</a></p><?php endif; ?>
							
						<?php elseif($vo['status'] == 404): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps13.png" class="pjt_state">
							<?php if($vo['type'] == 2): ?><p class="all_btn">
									<a href="<?php echo YI_JUAN;?>/project/preview/id/<?php echo ($vo['id']); ?>/type/event/close/1.html" class="a_yulan" target="_blank">预览</a>
									<a href="<?php echo YI_JUAN;?>/project/createEventProject/id/<?php echo ($vo['event']); ?>/project/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">修改项目信息</a>
								</p>
							<?php else: ?>
								<p class="all_btn"><a href="<?php echo JIA_SERVER_URL;?>/Project/updateProject/id/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">修改项目信息</a></p><?php endif; ?>
						<?php elseif($vo['status'] == 403): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps16.png" class="pjt_state">
							<?php if($vo['type'] == 2): ?><p class="all_btn">
									<a href="<?php echo YI_JUAN;?>/project/preview/id/<?php echo ($vo['id']); ?>/type/event/close/1.html" class="a_yulan" target="_blank">预览</a>
									<a href="<?php echo YI_JUAN;?>/project/createEventProject/id/<?php echo ($vo['event']); ?>/project/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">修改项目信息</a>
								</p>
							<?php else: ?>
								<p class="all_btn"><a href="<?php echo JIA_SERVER_URL;?>/Project/updateProject/id/<?php echo ($vo['id']); ?>.html" class="bm_manager" target="_blank">修改项目信息</a></p><?php endif; ?>
						<?php elseif($vo['status'] == -2): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps17.png" class="pjt_state">
						<?php elseif($vo['status'] == -3): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps18.png" class="pjt_state"><?php endif; ?>
					<?php else: ?>
						<?php if($vo['status'] == 888): ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps12.png" class="pjt_state">
						<?php elseif($vo['status'] == 100): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps11.png" class="pjt_state">
						<?php elseif($vo['status'] == -2): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps17.png" class="pjt_state">
						<?php elseif($vo['status'] == -3): ?>
							<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/ps18.png" class="pjt_state"><?php endif; endif; ?>	
				</div>
			</div>
		</div><?php endforeach; endif; ?>
	<div class="wraper_paging"><?php echo ($page); ?></div>
	<?php else: ?>
		<h3 class="project_title">相关项目<i></i></h3>
		<div class="prompt_states">暂无数据！</div><?php endif; ?>
</div><!-- 右边主体内容[ -->