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
            
<div class="alert alert-success  ">
    <form class="form-inline">
        <input value="<?php echo I('uid');?>" type="text" name="uid" placeholder="组织编号"/>
        <input value="<?php echo I('org_name');?>" type="text" name="org_name" placeholder="组织名称"/>
        <select name="type" id="type">
            <option value="">--组织类型--</option>
            <option value="1">基金会</option>
            <option value="2">非盈利机构(NGO/NPO)</option>
            <option value="3">社团/协会</option>
            <option value="4">企业</option>
        </select>

        <input value="<?php echo I('user_email');?>" type="text" name="user_email" placeholder="组织邮箱"/>
        <input value="<?php echo I('org_phone');?>" type="text" name="org_phone" placeholder="联系人手机号"/>
        <input value="<?php echo I('contact');?>" type="text" name="contact" placeholder="联系人姓名"/>


        <input type="submit" value="搜索"/>

    </form>
</div>
        <script type="text/javascript">
            (function(){
                $('#type').val(<?php echo I('type');?>);
            })();
        </script>
<table class="table table-bordered table-hover table-condensed">
    <tr>
        <th style="width: 100px">组织ID</th>
        <th style="width: 200px">组织名称</th>
        <th style="width: 150px">组织类型</th>
        <th style="width: 180px">组织邮箱</th>
        <th style="width: 100px">联系人手机</th>
        <th style="width: 100px">联系人姓名</th>
        <th style="width: 120px">用户类型</th>
        <th style="width: 60px">用户状态</th>
        <th style="width: 60px">认证状态</th>
        <th style="width: 100px">注册时间</th>
    </tr>
    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
             <td>
                 <?php echo ($vo->uid); ?>
             </td>
            <td>
                <?php echo ($vo->org_name); ?>
            </td>
            <td>
                <?php echo ($vo->type); ?>
            </td>
            <td>
                <?php echo ($vo->email); ?>
            </td>
            <td>
                <?php echo ($vo->phone); ?>
            </td>
            <td>
                <?php echo ($vo->contact); ?>
            </td>
            <td>
                <?php echo ($vo->user_type); ?>
            </td>
            <td>
                <?php echo ($vo->user_status); ?>
            </td>
            <td>
                <?php echo ($vo->org_status); ?>
            </td>
            <td>
                <?php echo ($vo->user_create_date); ?>
            </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>
<?php echo ($pageHtml); ?>
<style>
    td{
        word-wrap:break-word;word-break:break-all
    }
</style>
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