<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
    $(function () {
    function slidLeft(maxNum){
    var number = $(".Detail-Scores").text();
            if (parseFloat(number) < maxNum){

    var numbers = number.split(".");
            var gewei = numbers[0];
            var fenwei = numbers[1] || 0;
            var fw = parseInt(fenwei) + 1;
            if (fw > 9){
    fw = 0;
            gewei = parseInt(gewei) + 1
    }
    //var shu = gewei+"."+fw;
    var shu = maxNum;
            var Left = (shu / 5) * 400 - 14.5;
            $(".Detail-Scores").css("left", Left + "px");
            $(".Detail-Scores").html(shu);
            //setTimeout("slidLeft()",100);
    }
    }
    slidLeft(<?php echo ($score); ?>);
            //提交评价
            $(".SubmitBtn").click(function(){
    var content = $("#Appra-text").val();
            if (!content){
    alert("评价不能为空");
    }
    var score = $("input[name='Appraisal']:checked").val();
            var concurid = <?php echo ($concur['id']); ?>;
            $.ajax({
            type: "POST",
                    url: "/t/Concurinfo/getassess",
                    data: "content=" + content + "&score=" + score + "&concurid=" + concurid,
                    success: function(msg){
                    $(".Appraisal-content").remove();
                            var cont = '<li>';
                            cont += '<p class="Evalua-list-top">';
                            cont += '<a href="<?php echo ($userurl); ?>"><img src="<?php echo ($userphoto); ?>"></a>';
                            cont += '<span>' + content + '</span>';
                            cont += '</p>';
                            cont += '<p class="Evalua-list-bottom"><a href="<?php echo ($userurl); ?>"><?php echo ($username); ?></a><span><?php echo ($usertime); ?></span></p>';
                            cont += '</li>';
                            $(".Evalua-list").append(cont);
							var shu = msg['score'];
				            var Left = (shu / 5) * 400 - 14.5;
				            $(".Detail-Scores").css("left", Left + "px");
				            $(".Detail-Scores").html(shu);
							$(".comscore").html(shu+"分");
                    }
            });
    });
            //ajax返回申请名单
            var url = $(".wpr_col3_cont3").attr('data-url');
            var id = $(".wpr_col3_cont3").attr('data-id');
            $.ajax({
            url: url,
                    data:{id:id},
                    success: function(msg){
                    $(".wpr_col3_cont3").html(msg);
                            $(".wraper_paging a:contains(1):first").addClass('selected');
                    }
            });
            /*
             tab捐助的选项卡
             */
            $(".wpr_col3_choice li").click(function () {
    var _this = $(this), _ind = _this.index();
            _this.addClass("col3_li_cur").siblings().removeClass("col3_li_cur");
            $(".wpr_mclt_column3 > div").eq(_ind).show().siblings('div').hide();
    });
            $(".wuliu_info_list li:nth-child(2)").addClass("wuliu_info_cur");
            //提交物流信息
            $(".btns_manager").click(function(){
    //得到各个对象
    var _this = $(this), _parentojb = _this.parents('div.wraper_choice_wuliu'), _selectV = _parentojb.find('#ways_wuliu option:selected').text(),
            _ydbhV = _parentojb.find(".ydcode").val(), wlfs = _parentojb.next('ul');
            var text = _parentojb.find(".ydcode").val(); //获取物流单号
            var logistics = _parentojb.find("select option:selected").val(); //快递公司代码
            if (text.length){
    $.ajax({
    url:"/T/Donate/express",
            data:{id:id, text:text, logistics:logistics},
            type:"post",
            dataType:"json",
            success:function(d){
            //debugger;
            var html = "";
                    if (d){
            var res = d.res;
                    var res_json = eval("(" + res + ")");
                    html += "<li>";
                    html += d.expTextName + " 运单编号：" + d.mailNo;
                    html += "</li>";
                    /* 	$.each(d.data,function(index,value){
                     //逻辑处理
                     html +="<li>";
                     html +=value.time+value.context;
                     html +="</li>";
                     }); */
                    for (var i = res_json.data.length - 1; i >= 0; i--){
            //逻辑处理
            if (i == res_json.data.length - 1){
            html += "<li class='wuliu_info_cur'>";
            } else{
            html += "<li>";
            }
            html += res_json.data[i].time + " " + res_json.data[i].context;
                    html += "</li>";
            }

            } else{
            html += "该单号暂无物流进展，请稍后再试，或检查公司和单号是否有误。";
            }
            wlfs.html(html);
                    _parentojb.html("");
            }
    });
    } else{
    alert("请输入运单编号");
    }
    });
    });
</script>
<div class="wpr_mclt_column3">
    <a name="logistics" ></a>
    <?php if(empty($concur['type'])): ?><ul class="wpr_col3_choice">
            <?php if($concur['money'] != 0): ?><li class="col3_li_cur">在线捐助</li>
                <?php if(($concur['is_supplies'] == 1) or $concur['is_supplies'] == -1): ?><li>物资捐助</li><?php endif; ?>
                <li>捐助名单</li>
                <?php else: ?>
                <?php if(($concur['is_supplies'] == 1) or $concur['is_supplies'] == -1): ?><li class="col3_li_cur" name='logistics'>物资捐助</li>
                    <li>捐助名单</li>
                    <?php else: ?>
                    <li lass="col3_li_cur">捐助名单</li><?php endif; endif; ?>
            <li>项目评价</li>
        </ul>
        <?php if($concur['money'] != 0): ?><!-- 在线捐助[ -->
            <div class="wpr_col3_cont1">
                <span class="text_info3">一次性可捐助最大金额为50元，您的爱心款项将由中青公益专项基金托管帮助受助者。</span>
                <form action="/t/Donate/doContribute" method="post" id="form_zxjz" target="_blank" >
                    <input type="hidden" name="jinE" class="jinE">
                    <p class="col3_jk_items sumon"><label>
                        <span class="sp_color">*</span>捐助金额:</label>
                        <span class="sp_cur"data-money='1' >1元</span>
                        <span data-money='5'>5元</span>
                        <span data-money='10'>10元</span>
                        <span data-money='50'>50元</span>
                        <span class="other_money" data-money='0'><input type="text" name="other_money" placeholder="其他金额"><i>元</i></span>
                    </p>
                    <input type="hidden" class='submon' name='mon' value=''>
					<input type="hidden" name='concur_id' value="<?php echo I('id');?>">
                    <script>
                        $(function(){
                            var smon = $(".sumon .sp_cur").attr('data-money');                                                     
                            $(".submon").val(smon);
							$(".wpr_col3_cont1 form p span").click(function(){
								var donation = $(this).attr('data-money');
								$(".submon").val(donation);		
							})                                                    
                                $(".btns_jk").click(function(){
                                    $("#form_zxjz").submit();    
								});
                            });
                    </script>
                    <p class="col3_jk_items"><label>真是姓名：</label><input type="text" name="input_name"></p>
                    <p class="col3_jk_items"><label>联系电话：</label><input type="text" name="input_phone"></p>
                    <p class="col3_jk_items"><label>我的寄语：</label><textarea name="txtarea_jiyu" class="txtarea_jiyu"></textarea></p>
                    <span class="sp_tishi2 sp_placeholder">写几句话来支持Ta吧！最多可输入50个字符。</span>
                    <p class="p_chk"><label></label><input type="checkbox" name="input_chk2" id="input_chk1">匿名捐款</p>
                    <button class="btns_jk">捐款</button>
                </form>
            </div><!-- ]在线捐助 -->
            <?php if(($concur['is_supplies'] == 1) or $concur['is_supplies'] == -1): ?><div class="wpr_col3_cont2">
                    <?php W('Donate/sup');?>
                </div><?php endif; ?>
            <div class="wpr_col3_cont3" id='getAjaxPage' data-url="/T/ConcurAjax/ajaxPage" data-id="<?php echo ($concur['id']); ?>"><img src="http://static.gy.com/public/images/loading.gif"/></div>
            <?php else: ?>
            <?php if(($concur['is_supplies'] == 1) or $concur['is_supplies'] == -1): ?><div class="wpr_col3_cont2" style="display:block;">
                    <?php W('Donate/sup');?>
                </div>
                <div class="wpr_col3_cont3" id='getAjaxPage' data-url="<?php echo U('/T/ConcurAjax/ajaxPage');?>" data-id="<?php echo ($concur['id']); ?>"><img src="http://static.gy.com/public/images/loading.gif"/></div>
                <?php else: ?>
                <div class="wpr_col3_cont3" id='getAjaxPage' data-url="<?php echo U('/T/ConcurAjax/ajaxPage');?>" data-id="<?php echo ($concur['id']); ?>" style="display:block;"><img src="http://static.gy.com/public/images/loading.gif"/></div><?php endif; endif; ?>
        <?php else: ?>	                
        <ul class="wpr_col3_choice">
            <li class="col3_li_cur">申请名单</li>
            <li>项目评价</li>
        </ul>
		 <div class="wpr_col3_cont3" id='getAjaxPage' data-url="/T/ConcurAjax/ajaxPage" data-id="<?php echo ($concur['id']); ?>" style="display:block;"><img src="http://static.gy.com/public/images/loading.gif"/></div><?php endif; ?>
    <!-- 项目评价[ -->
    <div class="wpr_col3_cont4">
        <form action="#" method="get" class="Pro_pj">
            <p class="Pro-Rating">
                <span class="Pro-Rating-title">项目评级：</span>
                <input type="radio" name="Appraisal" id="Radio1" checked="checked"  value="5">&nbsp;非常好&nbsp;&nbsp;
                <input type="radio" name="Appraisal" id="Radio2"  value="4">&nbsp;很好&nbsp;&nbsp;
                <input type="radio" name="Appraisal" id="Radio3"  value="3">&nbsp;一般&nbsp;&nbsp;
                <input type="radio" name="Appraisal" id="Radio4"  value="2">&nbsp;较差&nbsp;&nbsp;
                <input type="radio" name="Appraisal" id="Radio5"  value="1">&nbsp;非常差&nbsp;&nbsp;
            </p>
            <?php if(empty($access)): ?><div class="Appraisal-content">
                    <div class="Appraisal-content-title">项目评价：</div>
                    <div class="Appraisal-content-Region">
                        <textarea name="Appra-text" id="Appra-text" placeholder="请输入140字以内的项目评价"></textarea>
                        <div class="SubmitBtn">提交</div>
                    </div>
                </div><?php endif; ?>
        </form>
        <div class="Pro-Evalua">
            <div class="Show-Evalua">
                <p class="Show-Evalua-title">综合满意度<span style="color:red" class='comscore'><?php echo ($score); ?>分</span><?php if(!empty($accessnum)): ?><span class="count">共<?php echo ($accessnum); ?>人评价</span><?php endif; ?></p>
                <p class="Scores-num">
                    <em class="Detail-Scores">0</em>
                    <span class="sp1">1</span><span class="sp2">2</span><span class="sp3">3</span><span class="sp4">4</span><span class="sp5">5</span>
                </p>
                <p class="Scores-text">
                    <span class="st1">非常差</span><span class="st2">较差</span><span class="st3">一般</span>
                    <span class="st4">很好</span><span class="st5">非常好</span>
                </p>
            </div>

            <ul class="Evalua-list">
                <?php if(is_array($content)): $i = 0; $__LIST__ = $content;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                        <p class="Evalua-list-top">
                            <a href="<?php echo ($vo["url"]); ?>" title="<?php echo ($vo["nickname"]); ?>"><img src="<?php echo ($vo["image"]); ?>"></a>
                            <span>
                                <?php echo ($vo["content"]); ?>
                            </span>
                        </p>
                        <p class="Evalua-list-bottom"><span><?php echo ($vo["time"]); ?></span></p>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
</div><!-- ]Column3 -->