<?php if (!defined('THINK_PATH')) exit();?>
<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/usercenter/group/js/ajaxpage.js"></script>
<script>
	$(function(){
		var tag=$(".hc_mrt_nav").attr("data-class");
		$(".tag_"+tag).addClass("nav_curr");
	});
</script>
<div class="hc_main_rt">
	<ul class="hc_mrt_tab">
		<?php if($uid == $userId): ?><li class="tab_curr"><a href="javascript:void(0)">我发起的求助</a></li>
			<li><a href="<?php echo U('/Uc/Concur/donate',array('uid'=>$uid));?>">我捐助的求助</a></li>
			<li><a href="<?php echo U('/Uc/Concur/approve',array('uid'=>$uid));?>">我认证真实的求助</a></li>
			<li><a href="<?php echo U('/Uc/Concur/index',array('uid'=>$uid,'type'=>1));?>">我提供的资源</a></li>
			<li><a href="<?php echo U('/Uc/Concur/donate',array('uid'=>$uid,'type'=>1));?>">我申请的资源</a></li>
		<?php else: ?>
			<li class="tab_curr"><a href="javascript:void(0)">发起的求助</a></li>
			<li><a href="<?php echo U('/Uc/Concur/donate',array('uid'=>$uid));?>">捐助的求助</a></li>
			<li><a href="<?php echo U('/Uc/Concur/approve',array('uid'=>$uid));?>">认证真实的求助</a></li>
			<li><a href="<?php echo U('/Uc/Concur/index',array('uid'=>$uid,'type'=>1));?>">提供的资源</a></li>
			<li><a href="<?php echo U('/Uc/Concur/donate',array('uid'=>$uid,'type'=>1));?>">申请的资源</a></li><?php endif; ?>
	</ul>
	<div class="hc_mrt_nav" data-class="<?php echo ($tag); ?>">
		<a href="javascript:void(0)" data-tag="0" class="tag_0">全部</a>
		<!-- <a href="javascript:void(0)" data-tag="1" class="tag_1">求款项</a> -->
		<a href="javascript:void(0)" data-tag="-1" class="tag_-1">求物资</a>
		<a href="javascript:void(0)" data-tag="-2" class="tag_-2">求服务</a>
	</div>
	<!-- 项目列表[ -->
	<div class="hc_mrt_prjlist">
		<?php if(!empty($concurInfo)): if(is_array($concurInfo)): $i = 0; $__LIST__ = $concurInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="wraper_about_pjt">
					<div class="wpr_ap_lt"><span>发起人</span><a href="<?php echo userUrl($vo['creator']);?>" target="_blank"><img src="<?php echo ($vo['photo']); ?>" title="<?php echo ($vo['nickname']); ?>" ></a></div>
					<div class="about_pjt_right">
						<a href="http://www.gy.com/T/Concurinfo/index/id/<?php echo ($vo['id']); ?>" target="_blank"><img class="this_pjt_photo" src="<?php echo ($vo['image']); ?>" title="<?php echo ($vo['title']); ?>"></a>
						<div class="this_pjt_details">
							<p class="this_pjt_title"><a href="http://www.gy.com/T/Concurinfo/index/id/<?php echo ($vo['id']); ?>" target="_blank"><?php echo ($vo['title']); ?></a></p>
							<p class="this_pjt_time"><?php echo ($vo['create_date']); ?></p>
							<?php if(($vo['status'] == -1) or ($vo['status'] == 404) or ($vo['status'] == 403) ): ?><p class="wraper_btns">
									<a href="<?php echo U('T/Concur/ConcurOne',array('id'=>$vo['id']));?>" target="_blank">修改求助信息</a>
								</p>
							<?php elseif(($vo['status'] == 100) or ($vo['status'] == 888)): ?>
								<?php if($uid == $userId): ?><p class="wraper_btns">
										<!-- <a href="" target="_blank">捐款管理</a> -->
										<?php if(!empty($vo['is_supplies'])): ?><a href="<?php echo U('T/Donate/suppliesManager',array('id'=>$vo['id']));?>" target="_blank">物资捐助管理</a><?php endif; ?>
										<?php if(!empty($vo['is_service'])): ?><a href="<?php echo U('T/Donate/serviceManager',array('id'=>$vo['id']));?>" target="_blank">服务捐助管理</a><?php endif; ?>
									</p><?php endif; endif; ?>
							<?php if($vo['status'] == -1): ?><span class="pjt_state s4"></span>
		                    <?php elseif($vo['status'] == 403): ?> 
		                    	<span class="pjt_state s1"></span>	
		                    <?php elseif($vo['status'] == 404): ?> 
		                    	<span class="pjt_state s13"></span>	
		                    <?php elseif($vo['status'] == 100): ?> 
		                    	<span class="pjt_state s20"></span>	
		                    <?php elseif($vo['status'] == 888): ?> 
		                    	<span class="pjt_state s21"></span><?php endif; ?>
						</div>
					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		<?php else: ?>
			<div class="prompt_states">暂无数据</div><?php endif; ?>	
	</div><!-- ]项目列表 -->
	<!-- 分页 -->
	<div class="wraper_paging"><?php echo ($page); ?></div>
</div>