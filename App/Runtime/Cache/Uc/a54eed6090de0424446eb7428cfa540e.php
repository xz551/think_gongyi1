<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo ($title); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/css/base.css">
	<link rel="stylesheet" type="text/css" href="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/css/login_regist.css">
	<script src="<?php echo STATIC_SERVER_URL;?>/public/js/jquery.js"></script>
	<script src="<?php echo STATIC_SERVER_URL;?>/usercenter/tp/js/login.js"></script>
</head>
<body>
	<!-- zqgy-header[ -->
	<div class="zqgy_header"><a href="<?php echo SERVER_VISIT_URL;?>"></a>用户登录</div><!-- ]zqgy-header -->
	<!-- zqgy-main[ -->
	<div class="zqgy_main">
		<div class="zqgy_mcontent">
			<!-- 左边图部分[ -->
			<div class="zqgy_mct_lt"></div>
			<!-- 右边部分[ -->
			<div class="zqgy_mct_rt">
				<!-- 普通登录[ -->
				<div class="zqgy_ptlogin shw">
					<form action="javascript:void(0)" id="login_form" method="post">
						<input name="returnurl" type="hidden" value="<?php echo I('returnurl');?>" />
						<input name="smscode" id="smscode" type="hidden" value="login"/>
						<h3 class="zqgy_login_title"><span>普通登录</span><a href="javascript:void(0);" class="lg_swh" data-id="0">短信验证码登录</a></h3>
						<p class="error_msg"></p>
						<div class="zqgy_ptform fm_shw">
							<p><input placeholder="邮箱/手机号" name="LoginForm_username" id="LoginForm_username" type="text"></p>
							<p><input placeholder="密码" name="LoginForm_password" id="LoginForm_password" type="password"></p>
						</div>
						<div class="zqgy_dxform">
							<p><input type="text" name="phone" id="phone" placeholder="手机号码"></p>
							<div class="ckcode clear"><p>
								<input type="text" id="code" name="code" placeholder="验证码"></p>
								<i id="getCode">获取短信验证码</i>
							</div>
						</div>
						<input name='remember' id="remember" type="hidden" value="1">
						<div class="rem_me"><i class="rm_chk" data-id="1"></i><span>记住我</span></div>
						<div class="all_btns"><button id="btn">登录</button><a href="/Uc/Login/findPassword1" class="col1">忘记密码</a></div>
					</form>
					<!-- 其他方式登录[ -->
					<h3 class="zqgy_otherways"><i></i><span>社交登录账号</span></h3><!-- ]其他方式登录 -->
					<div class="zqgy_lgway_list">
						<a title="新浪微博登陆" href="http://uc.gy.com/oauth/auth/provider/weibo?returnurl=<?php echo I('returnurl');?>" class="ways1"></a>
						<a title="腾讯微博登录" href="http://uc.gy.com/oauth/auth/provider/qq?returnurl=<?php echo I('returnurl');?>" class="ways2"></a>
						<a title="人人账号登陆" href="http://uc.gy.com/oauth/auth/provider/renren?returnurl=<?php echo I('returnurl');?>" class="ways3"></a>
						<a title="微信登录" href="javascript:void(0)" class="ways4"></a>
						<a title="QQ登录" id="qqLoginBtn" class="ways5" href="<?php echo SERVER_VISIT_URL;?>/t/author/qq_login?returnurl=<?php echo I('returnurl');?>"></a>
					</div>
					<div class="tips">还没有中青公益账号?<a href="http://uc.gy.com/user/register.html?returnurl=<?php echo I('returnurl');?>">马上注册</a></div>
				</div><!-- ]普通登录 -->
			</div><!-- ]右边部分 -->
		</div>
	</div>
	<div class="popWeiXin">
		<h3><span>微信登录</span><i class="popw_close"></i></h3>
		<div class="wraper_ewcode"></div>
		<p><span>拿出手机，打开微信，扫一扫，就能登录中青公益啦~</span></p>
		<p><span>欢迎关注"中青公益"的服务号哦</span></p>
	</div>
	<div id="mask"></div>
	<div class="zqgy_footer">© Copyright Reserved </div>
	<script type="text/javascript">
		$(function(){
			$(".ways4").click(function(){showWindow();});
			$(".popw_close").click(function(){$("#mask,.popWeiXin").hide();});
		});
		function showWindow(){
			$('div.wraper_ewcode').html('');
			var e = $(window).width(),f = $(window).height(),g = $(".popWeiXin"),h = g.width(),i = g.height(),j =(e-h)/2,k= (f-i)/2;
			g.css({top:k + 'px',left:j + 'px'}).show();$("#mask").show();
			get_img();
		}
		/**
		 * 获取二维码图片
		 */
		function get_img(){
			$.ajax({
				url:'/uc/login/get_login_weixin_qrcode',
				dataType:'json',
				success:function(d){
					if(typeof d.status!='undefined' && d.status<0){
						alert(d.msg);
						$("#mask,.popWeiXin").hide();
					}else{
						$('div.wraper_ewcode').html('<img width="280px" src="'+ d.img+'"/>');
						check_login();
					}

				}
			});
		}
		function check_login(){
			$.ajax({
				url:'/uc/login/check_wx_bind',
				dataType:'json',
				success:function(d){ 
					if(d.status==-1 && $(".popWeiXin:visible").length>0){
						setTimeout(function(){
							check_login();
						},1000);
					}else{
						$('.popWeiXin').append('<p class="wxl_true"><i></i><span>登录成功</span></p>');
						setTimeout(function(){
							window.history.go(0);
						},2000);
					}
				}
			});
		} 
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?32d80154d45fbea6c3447e50edbac692";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</body>
</html>