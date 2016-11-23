<?php if (!defined('THINK_PATH')) exit();?><div class="wpr_mclt_column2">
    <h3 class="h3_title"><i></i><?php echo ($helpname); ?>反馈</h3>
    <?php if($show_form): ?><p class="text_info"><?php echo ($helpname); echo ($htype==0?"发起人、捐助人、爱心认证员，可随时在项目反馈中提交求助项目的执行情况，如求助的真实情况、物资的运送、资金的结款、服务质量等信息。":"发起人、申请人可随时在项目反馈中提交资源项目的执行情况，如资源的真实情况、服务质量等信息。"); ?>
        </p>
    <form action="#" method="post" id="feedback_form">
        <p><textarea name="content" id="content" class="help_fankui"></textarea></p>
        <span class="sp_tishi1 sp_placeholder">发布<?php echo ($helpname); ?>反馈</span>
        <div class="wpr_options">
            <div class="wpr_opt_lt">
                <p><span class="sp_camera " id="upload-show-image">上传照片 <img style="width: 20px;position: absolute;display: none;" class="loader_img" src="http://static.gy.com/yijuan/product/images/loader.gif"></span><span class="sp_video" id="upload-show-video">上传视频</span></p>
                <p class="text_info2">图片支持jpg、jpeg和png格式，大小不超过1M</p>
            </div>
            <div class="wpr_opt_rt">
                你最多可输入<span>140</span>个字符<button id="submitbtn" type="button" onclick="subFeedback()">发布</button><img style="width: 20px;position: absolute;display: none;" class="loader_feedback" src="http://static.gy.com/yijuan/product/images/loader.gif">
            </div>
        </div>
        <div class="wraper_upload" style="display: none">
            <i class="icon_arrow"></i>
            <dl id="show_img" style="display: none">
                <dt>照片</dt>
                <dd>
                </dd>
            </dl>
            <dl id="show_video" style="display: none">
                <dt>视频</dt>
                <dd>
                    <span class="wpr_img"></span>
                </dd>
            </dl>
        </div>
    </form><?php endif; ?>
	<input type="hidden" name="relation_id" id="relation_id" value="<?php echo I('id');?>"/>
    <img style="width: 20px;position: absolute;;" class="loding_feedback" src="http://static.gy.com/yijuan/product/images/loader.gif">
</div>
<?php if($show_form): ?><input name="vname" type="hidden" id="vname" value="concur_feedback:concur_id-<?php echo ($concur_id); ?>:uid-<?php echo ($uid); ?>"/>
    <script src="http://static.gy.com/concur/js/help_feedback.js"></script><?php endif; ?>
<script src="http://static.gy.com/concur/js/max.img.js"></script>
<script src="http://static.gy.com/public/js/jquery.mousewheel.js"></script>
<script type="text/javascript">
    $(function(){
        getFeedBack(3);
        $('.show_max').maxImg();
    });
    function getFeedBack(size,fn){
        $.ajax({
            url:'/t/ConcurAjax/getfeedback',
            data:{'id':$('#relation_id').val(),'pagesize':size},
            success:function(h){
                $('.wpr_fankui_items').remove();
                $('.loding_feedback').hide();
                $('#relation_id').after(h);
                if(typeof fn=='function'){
                    fn();
                }
            }
        });
    }
</script>