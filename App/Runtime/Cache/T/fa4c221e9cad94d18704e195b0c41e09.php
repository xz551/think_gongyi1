<?php if (!defined('THINK_PATH')) exit();?><ul class="wpr_tab_top">

    <?php if(($money) != "0"): ?><li class="tab_li1 tab_cur">求款项</li><?php endif; ?>
    <?php if(($supplies) != "0"): ?><li class="tab_li2">求物资<a name="wuzi"></a></li><?php endif; ?>
    <?php if(($service) != "0"): ?><li class="tab_li3">求服务</li><?php endif; ?>
</ul>
<!-- 选项卡所对应的内容容器[ -->
<div class="wraper_tab_cont" data-id='<?php echo ($id); ?>'>
    <!--
    <?php if(($money) != "0"): ?>求款项
    <div class="wpr_tab_cont tab_cont1">
        <i class="detail_arrow_up"></i>
        <p class="progress_bar"><span class="this_progress"></span><span class="this_prog_num">34%</span></p>
        <p class="progress_detail"><span class="pd_lt">已捐助：<i>7600.00</i>元</span><span class="pd_rt">目标：20000.00</span></p>
        <p class="tab_cont1_btns"><span class="btns_manager">捐款管理</span><span class="btns_endkx">结束求助款项</span></p>
    </div>><?php endif; ?>
    -->

    <?php if(($supplies) != "0"): ?><!-- 求物资[ -->
    <div class="wpr_tab_cont tab_cont2">
        <i class="detail_arrow_up"></i>
        <p class="wpr_tc_info">
            <label>还需要物资：</label>
			<span><?php echo ((isset($needSupplies) && ($needSupplies !== ""))?($needSupplies):"无"); ?></span> 
        </p>
        <?php if(!empty($alShow)): ?><p class="wpr_tc_info">
                <label>已筹得物资：</label>
                <span><?php echo ($alShow); ?></span>
            </p><?php endif; ?>
        <?php echo W('Concurpage/supplies',array($isown,$applyStatus,$islogin,1));?>
    </div><!-- 求物资 --><?php endif; ?>

    <?php if(($service) != "0"): ?><!-- 求服务[ -->
    <div class="wpr_tab_cont tab_cont3">
        <i class="detail_arrow_up"></i>
        <p class="wpr_tc_info">
            <label>需要服务：</label>
            <span><?php echo ($serviceInfo["summary"]); ?></span>
        </p>
        <p class="wpr_tc_info">
            <label>服务时间：</label>
            <span><?php echo (date("Y-m-d H:i:s",$serviceInfo["start_time"])); ?> - <?php echo (date("Y-m-d H:i:s",$serviceInfo["end_time"])); ?></span>
        </p>
        <div class="serviceApply">
            <?php echo W('Concurpage/service',array($isown,$serviceApplyStatus,$islogin,1));?>
        </div>
    </div><!--求服务 --><?php endif; ?>

</div><!-- 选项卡所对应的内容容器[ -->
<!--友好提示弹出层[-->
<div class="promptHtml ptHtml2">
    <p class="promptHtmlText">服务虽免费的，爱心却无价~确定要提供此项服务吗？建议您先与发起人就“服务内容、服务时间、服务地点”等问题进行沟通，并待对方确认您的捐助请求后，再进行服务的对接和进行</p>
</div><!--]友好提示弹出-->

<div class="promptHtml ptHtml4">
    <p class="promptHtmlText">确定要撤销当前的请求吗？</p>
</div>

<script>
    $(function () {
        //选择金额
        $(".col3_jk_items > span").click(function () {
        $(this).addClass("sp_cur").siblings('span').removeClass("sp_cur");
                //$(".jinE").val($(".col3_jk_items").find("span.sp_cur").text());
        });
        $("#box1,#mask1").hide();              
    })
</script>