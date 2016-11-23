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
            <!--
1.收入获得证书的人员编号或身份证号
2.选择要颁发的证书
-->
<div class="row-fluid hero-unit">
    <form class="form-horizontal" method="post" action="/Admin/Certificate/examine">
    <div class="control-group">
        <label class="control-label" for="shenfz">请收入获得证书人员的身份证号：</label>
        <div class="controls">
            <textarea name="shenfz" style='margin: 0px; width: 379px; height: 109px;'></textarea>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="zhengshu">请选择要颁发的证书</label>
        <div class="controls">
            <select name="zhengshu"> 
                <?php if(is_array($certificate)): $i = 0; $__LIST__ = $certificate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="control-group">
        <div class="controls"> 
            <button type="submit" class="btn">提交</button>
        </div>
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