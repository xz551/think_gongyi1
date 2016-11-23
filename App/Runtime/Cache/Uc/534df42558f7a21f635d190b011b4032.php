<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" href="<?php echo STATIC_SERVER_URL;?>/usercenter/org/css/style.css?1"/>
<link rel="stylesheet" href="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/css/help_center.css">
<script type="text/javascript">
$(function(){
	$(".btn_helpOrziyuan").popWindow({
	  width:"880",
            height:"425",
	  title:"请选择",
            content:$(".ptHtml1").html(),
            id:"box1",
            callback:function(a,box){}
	});
	$("#box1").wrap("<form class='foot'></form>");
});
</script>
<div class="myhome_center_top">
			<div id='mask' style="display: none;"></div>
			<?php W('Common/Login/showLogin');?>
			<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_ogm.png" class="myhome_volunteer">
			<div class="infoimg">
			<img src="<?php echo ($userInfo["photo"]); ?>" class="myhome_users_photo">
				<div class="popmsg">
					<span class="join_btn" title="申请加入组织" style="display:none">申请加入</span>
				<?php if(($isMine) != "1"): ?><span class="pemail_btn" data-toid="<?php echo ($uid); ?>" data-uname="<?php echo ($userInfo["name"]); ?>">私信</span>
					{*~W('Layout/complain',array("type"=>2,"id"=>$uid,"uname"=>$userInfo['name']))*}<?php endif; ?>
				</div>
			</div>
			<div class="myhome_users_details">
                <p class="myhome_users_name"><strong><?php echo ($userInfo["name"]); ?></strong>
                	<?php if(($isVip) == "1"): ?><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_v.png" title="认证组织"><?php endif; ?> 
                </p>
				<?php if($userInfo["area"] != '&nbsp;&nbsp;'): ?><p class="myhome_users_address"><?php echo ($userInfo["area"]); ?></p><?php endif; ?>

				<p class="myhome_users_timelen">志愿服务时长：<a href="<?php if(($isMine == 1) and ($isVip == 1)): echo JIA_SERVER_URL;?>/Duration/durateDetail.html<?php else: ?>javascript:void(0)<?php endif; ?>"><?php echo ($serverTime); ?></a>&nbsp;小时</p>
				<p class="myhome_users_level">
					<label>综合服务评价：</label>
            		<span><i class="users_level" ></i></span>
            		<i><?php echo ($orgLevel); ?></i>
        		</p>
        		<script type="text/javascript">
					$(function(){
						setUsersLevel(<?php echo ($orgLevel); ?>);
					});
					/*
					设置评价星级
					*/
					function setUsersLevel(j){
						$(".users_level").css("width",j*16+"px");
					}
				</script>
				<p>组织类型：<?php echo ($orgInfo->typename); ?></p>
				<?php if(($isshow) == "1"): ?><a href="<?php echo ($orgInfo->website); ?>" target='_blank' class="this_website"><?php echo ($orgInfo->website); ?></a><?php endif; ?>
				<div class="myhome_attention_ly">
					<label>关注领域：</label>
					<p class="mh_attent_lylist">
                                            <?php if(is_array($interest)): $i = 0; $__LIST__ = $interest;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><span><?php echo ($vo); ?></span><?php endforeach; endif; else: echo "" ;endif; ?>
					</p>
				</div>
				<p class="network_icon">
					 <?php if(is_array($aouth)): $i = 0; $__LIST__ = $aouth;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($vo["type"]) == "1"): ?><a href="<?php echo ($vo["homepage"]); ?>" target="_black"><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_tencent.png"></a><?php endif; ?>
                                            <?php if(($vo["type"]) == "2"): ?><a href="<?php echo ($vo["homepage"]); ?>" target="_black"><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_sana.png"></a><?php endif; ?> 
                                            <?php if(($vo["type"]) == "3"): ?><a href="<?php echo ($vo["homepage"]); ?>" target="_black"><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_renren.png"></a><?php endif; endforeach; endif; else: echo "" ;endif; ?>   
				</p>
			</div>

</div>
<?php if(($isMine) == "1"): ?><div class="myhome_center_options">
	    <?php if(($isVip) == "0"): ?><a href="http://uc.gy.com/organization/apply.html"><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_ren.png">申请成为认证组织</a>	
	        <a href="http://www.gy.com/T/Group/addGroup.html" ><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_zu.png">创建公益小组</a>
	        <a href="<?php echo YI_JUAN;?>/project/create_recruit_1.html"><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_zhi.png">发起志愿招募项目</a>
	        <a href="<?php echo YI_JUAN;?>/active/create.html" ><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_yi.png">发起公益活动</a>
	   <?php else: ?>
	        <a href="http://www.gy.com/T/Group/addGroup.html" ><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_zu.png">创建公益小组</a>
	        <a href="<?php echo JIA_SERVER_URL;?>/Project/createProject.html" ><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_zhi.png">发起志愿招募项目</a>
	        <a href="<?php echo JIA_SERVER_URL;?>/Active/createActivity.html" ><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_yi.png">发起公益活动</a><?php endif; ?>
	     	<a href="javascript:void(0)" class="btn_helpOrziyuan"><img src="<?php echo STATIC_SERVER_URL;?>/usercenter/org/images/icon_zhu.png">发起求助/资源</a> 
	</div><?php endif; ?>
<!-- 弹出层选择求助还是资源 -->
<div class="promptHtml ptHtml1" style="display:none;">
	<div class="promptHtmlText">
		<div class="choice_options">
			<a href="<?php echo U('/T/Concur/concurOne');?>" class="co_top">
				<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_help.png">
				<span>我要求助</span>
			</a>
			<p class="co_tips">
				有困难，快来发布求助项目，互相帮助渡难关！
				支持：物资求助、款项求助、服务求助
			</p>
		</div>
		<div class="choice_options">
			<a href="<?php echo U('/T/Concur/concurOne/type/1');?>" class="co_top">
				<img src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/images/icon_ziyuan.png">
				<span>我有资源</span>
			</a>
			<p class="co_tips">
				有闲置物资或时间想要帮助别人，快来广而告之！
				支持：物资资源、服务资源
			</p>
		</div>
	</div>
</div>        
<script>
var obj = $(".myhome_center_options a");
var anum = obj.length;
if(anum==3){
        obj.width(1190/3);
}else if(anum==4){
        obj.width(1190/4);
}else if(anum==5){
        obj.width(1190/5);
}
function joinorg(org){
	$.ajax({
		url:'/t/UCenter/join_org/orgid/'+org,
		dataType:'jsonp',
		success:function(d){
			if(d){
				var status=d['status'];
				if(status==-1){
					//需要登陆
					showbox();
				}else if(status==-2){
					//需要认证
					window.location.href='<?php echo UCENTER;?>/volunteer/apply/type/1/org/'+(d['orgname']);
				}else{
					alert(d['msg']);
				}

				if(status==1){
					$('.join_btn').text("已申请").unbind('click');
				}
			}
		}
	});
}
function getStatus() {
	var org=$(".pemail_btn").data('toid'); 
	if(org){
		$.ajax({
			url:'/t/UCenter/org_user_status/org/'+org,
			dataType:'jsonp',
			success:function(d){
				if(d){
					var status=d['status'];
					var jBtn=$('.join_btn');
					if(status==-1){
						//审核失败，或者没有申请
						$(".popmsg").addClass("join");
						jBtn.html('申请加入').show();
						jBtn.bind('click',function(){
							joinorg(org);
						});
					}else if(status==0){
						//已经审核 等待审核
						$(".popmsg").addClass("join");
						jBtn.html("已申请").show();
					}else if(status==1){
						//审核通过已申请
						$(".popmsg").addClass("join");
						jBtn.html("已加入").show();
					}else{
						//不显示
						$('.join_btn').remove();
					}
				}
			}
		});
	}
}
getStatus();
</script>