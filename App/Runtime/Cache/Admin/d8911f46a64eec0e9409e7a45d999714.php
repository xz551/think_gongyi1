<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="/css/todc-bootstrap.css" rel="stylesheet" media="screen">
    <link href="/css/style.css" rel="stylesheet" media="screen">
    <script src="/js/jquery.js"></script> 
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/loader.jquery.js"></script>
    <script src="/js/common.js"></script>
    <script src="http://static.gy.com/public/js/WdatePicker.js"></script>
    <script src="http://static.gy.com/public/js/ajaxupload.js"></script>
    <title>中青公益，后台管理系统</title>
    <script type="text/javascript">
        $(function(){
            if(!placeholderSupport()){   // 判断浏览器是否支持 placeholder
                $('[placeholder]').focus(function() {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                        input.removeClass('placeholder');
                    }
                }).blur(function() {
                    var input = $(this);
                    if (input.val() == '' || input.val() == input.attr('placeholder')) {
                        input.addClass('placeholder');
                        input.val(input.attr('placeholder'));
                    }
                }).blur();
                $('form').submit(function(){
                    var that=$(this);
                    that.find('[placeholder]').each(function(){
                        var _input=$(this);
                        if(_input.val()==_input.attr('placeholder')){
                            _input.val('');
                        }
                    });
                });
            };

        })
        function placeholderSupport() {
            return 'placeholder' in document.createElement('input');
        }
    </script>
</head>
<body style="padding-top: 30px;">
<div class="navbar navbar-static-top navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <a class="brand" href="/home.html">后台管理系统</a>
        <ul class="nav">
            <li class="active"><a href="/home.html">Home</a></li>
        </ul>
        <span class="navtext pull-right"">
            今天是<?php echo date('Y年m月d日');?>&nbsp;&nbsp;&nbsp;Welcome ~ | 
            <a href="/site/logout.html">退出</a>    
        </span>
    </div>
</div>
<!-- NavBar End ] -->
<div class="wrapper"> 
        <?php W('Menu/init');?>
    <div class="main_content">
        <div class="container-fluid">
            <script type="text/javascript">
$(function(){
	$(".verify_pass").click(function(){
		var url=$(this).data('url');
		var id=$(this).data('id');
		$.ajax({
			url:url,
			data:{id:id},
			success:function(d){
				if(d.status){
					location.reload();
				}else{
					alert(d.message);
				}
			}
		});
	});
	$(".verify_fail").click(function(){
		$("#EventModal").show();
		var id=$(this).data('id');
		$("#verifyfailed_id").attr('value',id);
		
	});
	$(".btn-link").click(function(){
		$("#EventModal").hide();
	});
	$("#verifyfailed").click(function(){
		$("form").submit();
	});
});
</script>
<div class="row-fluid">
    <div class="span12">
        <div id="project_verify_list" class="list-view">
			<div class="items">
			<?php if(is_array($info)): $i = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="well well-small">
				    <div class="media" style="position: relative">
				        <a class="pull-left" href="javascript:;">
				            <img class="media-object img-polaroid" src="<?php echo ($vo['image']); ?>" width="200" height="160">
				        </a>
				        <div class="media-body">
				            <h5 class="media-heading"><span style="color:red;">【<?php if(!empty($vo["type"])): ?>资源<?php else: ?>求助<?php endif; ?>项目】</span><a href="javascript:;"><?php echo ($vo['title']); ?></a></h5>
				            <div class="media">
				                <div class="menu_admin">
				                    <div class="btn-group">
				                        <a class="btn dropdown-toggle btn-mini btn-primary" data-toggle="dropdown" href="javascript:;">
				                        	审核操作<span class="caret"></span>
				                        </a>
				                        <ul class="dropdown-menu pull-right">
				                            <li><a href="javascript:;" class="verify_pass" data-url="/Admin/concur/verifysuccess" data-id="<?php echo ($vo['id']); ?>">审核通过</a></li>
				                            <li><a href="javascript:;" class="verify_fail" data-id="<?php echo ($vo['id']); ?>" >审核失败</a></li>
				                        </ul>
				                    </div>
				                </div>
					                <ul>
					                    <li><b>发起人：</b><?php echo ($vo['nickname']); ?> (UID:<?php echo ($vo['creator']); ?>)</li>
					                	<li><b>类别标签：</b>  
					                		<?php if(is_array($vo['label'])): $i = 0; $__LIST__ = $vo['label'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; echo ($v); ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
            							</li>
					                    <li><b>受助对象：</b><?php echo ($vo['server_for']); ?></li>
					                    <li><b>受助地点：</b><?php echo ($vo['area']); ?></li>
					                    <li><b>求助时间：</b><?php echo ($vo['start_time']); ?> 至 <?php echo ($vo['end_time']); ?></li>
					                <?php if($vo['type'] == 0): ?><!-- <li><b>求款项：</b>10000.00元</li> -->
					                	<?php if(!empty($vo['is_supplies'])): ?><li><b>求物资：</b><?php echo ($vo['supplies']); ?><br/>
										    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;收件地址：<?php echo ($vo['mailAddress']); ?><br/>
										    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;收件人：<?php echo ($vo['mailName']); ?><br/>
										    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;联系电话：<?php echo ($vo['mailPhone']); ?><br/>
										    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;邮政编码：<?php echo ($vo['code']); ?><br/>
										</li><?php endif; ?>
										<?php if(!empty($vo['is_service'])): ?><li><b>求服务：</b><?php echo ($vo['servicesummary']); ?><br/>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;服务时间：<?php echo ($vo['servicetime']); ?><br/>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;服务地点：<?php echo ($vo['serviceaddress']); ?><br/>
										</li><?php endif; ?>
									<?php else: ?>
										<?php if(!empty($vo['is_supplies'])): ?><li><b>捐资源：</b><?php echo ($vo['supplies']); ?><br/></li><?php endif; ?>
										<?php if(!empty($vo['is_service'])): ?><li><b>捐服务：</b><?php echo ($vo['servicesummary']); ?><br/>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;服务时间：<?php echo ($vo['servicetime']); ?><br/>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;服务地点：<?php echo ($vo['serviceaddress']); ?><br/>
										</li><?php endif; endif; ?>
										
										<li><a href="javascript:;" class="open_modal" data-type="ajax" data-load="/admin/Concur/detail/id/<?php echo ($vo['id']); ?>.html">查看详细</a></li>
					                </ul>
				            </div>
				        </div>
				    </div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>	
			</div>
		</div>                    
		<div class="clearfix"></div>
    </div>
</div>
<?php echo ($page); ?>
<div id="EventModal" class="modal hide fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="display: none;">
	<form method="post" action="/admin/concur/verifyrefuse">
	<div class="modal-header">
        <button type="button" class="close btn-link" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">审核不通过的原因</h3>
    </div>
	<div class="modal-body">
		<input type="hidden" id="verifyfailed_id" name="id" >
		<textarea rows="5" style="width:515px;" id="verifyfailed_reject" name="reject" maxlength="100"></textarea>
		<p class="text-warning">100字以内</p>
	</div>
    <div class="modal-footer">
        <input type="button" id="verifyfailed" class="btn btn-primary" value="审核不通过">
        <span class="btn btn-link" data-dismiss="modal" aria-hidden="true">关闭</span>
    </div>
	</form>
</div>
        </div>
    </div>
    
</div>

<!-- Modal -->
<div id="EventModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<!-- Modal -->

<script type="text/javascript">
    function closemodal(){
        $("#EventModal").modal('hide');
    }
$(document).on('click', '.open_modal', function(){
    var _loading = '<div style="background: url(/img/loading.gif) no-repeat center center; min-height: 50px;"></div>';
    var _obj = $("#EventModal");

    _obj.html(_loading);
    _obj.modal('show');

    var _show_type = $(this).attr('data-type');
    var _show_content  = $(this).attr('data-load');
    if( _show_content.indexOf('js:') == 0 )
        _show_content = eval(_show_content.substr(3));

    if( _show_type == 'text' )
    {
        _obj.html(_show_content);
    }
    else if( _show_type == 'ajax' )
    {
        $.get(_show_content, function(data){
            _obj.html(data);
        });
    }
    else if( _show_type == 'img' )
    {
    	_obj.html('<img src="'+_show_content+'" />');
    }
    return false;
});
</script>
</body>
</html>