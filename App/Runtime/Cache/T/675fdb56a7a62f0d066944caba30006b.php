<?php if (!defined('THINK_PATH')) exit();?><div class="wpr_mclt_column6">
    <h3 class="h3_title"><i></i><span class="sp_name">爱心认证员</span><span class="sp_small">什么是爱心认证员？</span>
        <?php if($is_curr_user == true ): ?><a href="javascript:void(0);" class="a_rz">已认证</a>
            <?php else: ?>
            <a href="javascript:void(0);" class="a_rz renzheng">我来认证这个求助的真实性</a><?php endif; ?>
    </h3>
    <div class="wpr_aixinrz_cont" style="display: none;"><i class="icon_arrow_up"></i>
        <dl><dt>什么是爱心认证员？</dt><dd>爱心认证员，即确认求助信息真实可靠的人员。</dd></dl>
        <dl><dt>爱心认证员需要干什么？</dt><dd>爱心认证员完成求助项目真实性的核实，并承担信息真实可靠的连带责任。</dd></dl>
        <dl><dt>爱心认证员的作用是什么？</dt><dd>可提高求助信息的真实性，吸引爱心人士的捐助。</dd></dl>
    </div>
    <img class="loader_love" src="http://static.gy.com/yijuan/product/images/loader.gif">
</div>
<script type="text/javascript">

    $(function(){  
        getLove();
    });
    function getLove(){
        var _load_love=$('.loader_love');
        _load_love.show();
        $.ajax({
            url:'/t/ConcurAjax/ilove/id/<?php echo ($concur_id); ?>',
            success:function(d){
                _load_love.nextAll().remove();
                _load_love.hide();
                _load_love.after(d);
            }
        });
    }
</script>
<?php if($is_curr_user != 1 ): ?><div class="promptHtml ptHtml9">
        <p class="promptHtmlText">我已经线下了解过这个求助，确认这个求助的信息是真实可靠的！如有问题，我自愿承担相关的连带责任!</p>
    </div>
    <script type="text/javascript">
        $(".renzheng").popWindow({
            width:"400",
            height:"200",
            content:$(".ptHtml9").html(),
            id:"box4",
            callback:function(a,box){},
            button:{
                cancel:function(){},
                ok:function(t,box){
                    box.hide();$("#mask1").hide();
                    $('.loader_love').show();
                    renzheng();
                }
            }
        });
        /**
         * 执行认证
         */
        function renzheng(){
            $.ajax({
                url:'/T/ConcurAjax/ilove_renzheng/id/<?php echo ($concur_id); ?>',
                dataType:'json',
                success:function(d){
                    $('.loader_love').hide();
                    if(d.login==-1){
                       $('#mnlogin_wraper').show();
                        showbox();
                    }else if(d.status==-1){
                        alert(d.msg);
                    }else{
                        //重新获取下认证信息
                        getLove();
                        $('.renzheng').html('已认证').removeClass('renzheng');
                    }
                }
            });
        }
        $("#box4").find("#winTitle").css("border-bottom","none");
    </script><?php endif; ?>