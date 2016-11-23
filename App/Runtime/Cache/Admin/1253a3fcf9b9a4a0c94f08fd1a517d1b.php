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
        <div class="row">
            <div class="span2">
                <input class="input-medium" value="<?php echo I('uid');?>" type="text" name="uid" placeholder="用户编号"/>
            </div>
            <div class="span2">
                <input class="input-medium" value="<?php echo I('nickname');?>" type="text" name="nickname" placeholder="昵称"/>
            </div>
            <div class="span2">
                <input class="input-medium" value="<?php echo I('real_name');?>" type="text" name="real_name" placeholder="真实姓名"/>
            </div>
            <div class="span2">
                <input class="input-medium" value="<?php echo I('phone');?>" type="text" name="phone" placeholder="手机号"/>
            </div>
            <div class="span2"><input class="input-medium" value="<?php echo I('idcard_code');?>" type="text" name="idcard_code"
                                      placeholder="身份证号码"/></div>
        </div>
        <div>
            <div class="row">
                <div class="span2">
                    <select name="type" id="type" class="span2">
                        <option value="">--用户类型类型--</option>
                        <option value="10">认证用户</option>
                        <option value="11">普通用户</option>
                        <option value="-1">注销用户</option>
                    </select>
                </div>
                <div class="span2">
                    <select name="gender" id="gender" class="span2">
                        <option value="">--性别--</option>
                        <option value="1">男</option>
                        <option value="0">女</option>
                    </select>
                </div>
                <div class="span2">
                    <select name="provinceid" id="provinceid" class="city span2">
                        <option value="">--省份--</option>
                        <?php if(is_array($province)): $i = 0; $__LIST__ = $province;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['class_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="span2">
                    <select name="cityid" id="cityid" class="city span2">
                        <option value="">--市区--</option>
                        <?php if(is_array($city)): $i = 0; $__LIST__ = $city;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['class_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
                <div class="span2">
                    <select name="countyid" id="countyid" class="span2">
                        <option value="">--县区--</option>
                        <?php if(is_array($county)): $i = 0; $__LIST__ = $county;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['class_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <label><input type="checkbox" name="iscard" id="iscard" value="1"/>有身份证号码</label>
            <label><input type="checkbox" name="isphone" id="isphone" value="1"/>有手机号码</label>
        </div>
        <div>
            <input type="hidden" name="p" value="1"/>
            关注领域：
            <?php if(is_array($server)): $i = 0; $__LIST__ = $server;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><label>
                    <input class="severtag" type="checkbox" name="tags[]" value="<?php echo ($key); ?>"/><?php echo ($vo); ?>
                </label><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <input type="submit" value="搜索"/>
    </form>
    <input type="hidden" name="h_tags" value="<?php echo join(',',I('tags'));?>" id="h_tags"/>
</div>
<input type="hidden" id="h_iscard" value="<?php echo I('iscard');?>"/>
<input type="hidden" id="h_isphone" value="<?php echo I('isphone');?>"/>
<script type="text/javascript">
    (function () {
        $('#type').val(<?php echo I('type');?>);
        $('#gender').val(<?php echo I('gender');?>) ;
        $('#provinceid').val(<?php echo I('provinceid');?>);
        $('#cityid').val(<?php echo I('cityid');?>);
        $('#countyid').val(<?php echo I('countyid');?>);
        var _iscard = $("#h_iscard").val();
        var _isphone = $("#h_isphone").val();
        if (_iscard == 1) {
            $("#iscard").attr("checked", "checked");
        }
        if (_isphone == 1) {
            $("#isphone").attr("checked", "checked");
        }
        var _tag=$("#h_tags").val();
        if(_tag!=""){
            var _tags=_tag.split(",");
            for(var i=0;i<_tags.length;i++){
                var v=_tags[i];
                $(".severtag[value="+v+"]").attr("checked","checked");
            }
        }
        $('.city').on("change", function () {
            var that = $(this);
            var id = that.val();
            that.parent().next().find("select").html('<option value="0">--请选择--</option>');
            that.parent().next().next().find('select').html('<option value="0">--请选择--</option>');
            if (id == 0) {
                return;
            }
            $.ajax({
                url: "/admin/city/getChildrenCity",
                data: {id: id},
                dataType: "json",
                success: function (data) {
                    var p = ['<option value="0">--请选择--</option>'];
                    for (var n  in data) {
                        p.push('<option value="' + n + '">' + data[n] + '</option>');
                    }
                    that.parent().next().find("select").html(p.join(''));
                    that.parent().next().next().find('select').html('<option value="0">--请选择--</option>');
                }
            });
        });
    })();
</script>
<table class="table table-bordered table-hover table-condensed">
    <tr>
        <th style="width: 100px">用户ID</th>
        <th style="width: 200px">用户昵称</th>
        <th style="width: 150px">真实姓名</th>
        <th style="width: 180px">用户性别</th>
        <th style="width: 100px">用户邮箱</th>
        <th style="width: 100px">用户QQ</th>
        <th style="width: 120px">用户手机</th>
        <th style="width: 60px">身份证号码</th>
        <th style="width: 60px">用户类型</th>
        <th style="width: 100px">用户状态</th>
        <th style="width: 100px">认证状态</th>
        <th style="width: 100px">注册时间</th>
        <th style="width: 100px">操作</th>
    </tr>
    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
            <td>
                <?php echo ($vo['uid']); ?>
            </td>
            <td>
                <?php echo ($vo['nickname']); ?>
            </td>
            <td>
                <?php echo ($vo['real_name']); ?>
            </td>
            <td>
                <?php echo ($vo['gender']==1?"男":($vo['gender']==0?"女":"未知" )); ?>
            </td>
            <td>
                <?php echo ($vo['email']); ?>
            </td>
            <td>
                <?php echo ($vo['qq']); ?>
            </td>
            <td>
                <?php echo ($vo['phone']); ?>
            </td>
            <td>
            <?php echo ($vo['idcard_code']); ?>
        </td>
            <td>
                <?php echo ($vo['type']==1?'认证用户':"普通用户"); ?>
            </td>
            <td>
                <?php echo ($vo['status']==-1?"已注销":($vo['status']==1?"邮箱已验证":"邮箱未验证")); ?>
            </td>
            <td>
                <?php echo ($vo['volunteer_status']); ?>
            </td>
            <td>
                <?php echo ($vo['create_date']); ?>
            </td>
            <td>
                <a href="javascript:void(0)">注销</a>
            </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>
<?php echo ($pageHtml); ?>
<style>
    td {
        word-wrap: break-word;
        word-break: break-all
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