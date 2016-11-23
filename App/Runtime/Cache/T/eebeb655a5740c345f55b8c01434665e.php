<?php if (!defined('THINK_PATH')) exit(); W('Common/Login/showLogin');?>
<!-- 左—TOP[ -->
<div class="slider_lf_top">
	<a href="javascript:void(0)" class="slider_lft_btns icon_help" data-tag="0">我要求助</a>
	<p class="slider_cont_intrd">有困难，快来发布求助项目，互相帮助渡难关！支持：物资求助、款项求助、服务求助。</p>
    <a href="javascript:void(0)" class="slider_lft_btns icon_resoures" data-tag="1">我有资源</a>
	<p class="slider_cont_intrd">有闲置物资或时间想要帮助别人，快来广而告之！支持：物资资源、服务资源。</p>
	<a href="javascript:void(0)" class="slider_lft_btns icon_resoures" data-tag="2">项目统计</a>
	<p class="slider_cont_intrd">透明捐助项目的阶段性与群体性统计!</p>
</div><!-- ]左—TOP -->
<script type="text/javascript">
$(function(){
	$(".icon_help,.icon_resoures").click(function(){
		var tag=$(this).data('tag');
		$.ajax({
			url:'/T/Donate/help',
			data:{tag:tag},
			success:function(d){
				if(d.res==1){
					//是认证用户
					if(d.tag==1){
						window.location.href='/T/Concur/concurOne/type/1.html';
					}else{
						if(d.tag==2){
							window.location.href='http://w69.gy.com.cn/Recommender/general.jsp';
						}else{
							window.location.href='/T/Concur/concurOne.html';
						}
					}
				}else if(d.res==0){
					//非认证用户
					if(d.tag==1){
						alert("只有通过认证的用户才可以发布资源！");
					}else{
						alert("只有通过认证的用户才可以发起求助！");
					}
				}else if(d.res==-1){
					//需要登陆
					showbox();
				}
			}
		});
	});
});
</script>