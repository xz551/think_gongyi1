<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
    $(function () {
        /*对textarea 和 input 的输入实时监听事件代码*/
        $.event.special.valuechange = {
            teardown: function (namespaces) {
                $(this).unbind('.valuechange');
            },
            handler: function (e) {
                $.event.special.valuechange.triggerChanged($(this));
            },
            add: function (obj) {
                $(this).on('keyup.valuechange cut.valuechange paste.valuechange input.valuechange', obj.selector, $.event.special.valuechange.handler)
            },
            triggerChanged: function (element) {
                var current = element[0].contentEditable === 'true' ? element.html() : element.val()
                        , previous = typeof element.data('previous') === 'undefined' ? element[0].defaultValue : element.data('previous')
                if (current !== previous) {
                    element.trigger('valuechange', [element.data('previous')])
                    element.data('previous', current)
                }
            }
        }
        /*我来说两句的交互*/
        $(".okBtn1").click(function () {
        	   var inputV = $(".edit_box").val().substr(0, 140);   //发的内容
               var _this = $(this);
               /**
                * 给哪个求助项目||资源项目回复 sid
                * 给哪个人 回复 calluid
                * 给哪条评论回复 cid
                */
               var sid = _this.attr('data-sid');//
               var calluid = 0;//
               var cid = 0;//
               addComment(sid, calluid, cid, inputV);
        });
        /*回复那里的交互*/
        $(".okBtn2").live("click", function () {
        	  var _this = $(this);
        	  var inputV = _this.prev().val();
        	  /**
               * 给哪个求助项目||资源项目回复 sid
               * 给哪个人 回复 calluid
               * 给哪条评论回复 cid
               */
              var sid = _this.attr('data-sid');
              var calluid = _this.attr('data-calluid');
              var cid = _this.attr('data-cid');
              addComment(sid, calluid, cid, inputV,_this);
        });
        /*点击回复按钮*/
        $(".a_reply").live("click", function () {
        	   var islogin = "<?php echo ($islogin); ?>";
        	  
               if (islogin == 0) {
                   var ac = "<?php echo ($login); ?>";
                   $('#form1').attr('action', ac);
                   showbox();
               } else {
                   $(this).next('form').toggle("50");
   				promptInfo();
               }
        });
        /*textarea实时监听事件*/
        $('.edit_box').on('valuechange', function (e, previous) {
            var _this = $('.edit_box');
            var _result = 140 - _this.val().length;
            _result > 0 ? $(".font_length").text(_result) : $(".font_length").text(0);

        })
        $(".edit_box").live('click', function () {
            var islogin = "<?php echo ($islogin); ?>";
            var isVip = "<?php echo ($isVip); ?>";
            if (islogin == 0) {
                var ac = "<?php echo ($login); ?>";
                $('#form1').attr('action', ac);
                showbox();
            }else{
            	if(isVip == 0){
            		alert("只有认证的用户才可以参与讨论");
            	}
            }
            
        });
        /*
         	为textarea模拟placeholder
         */
        $("textarea").focus(function () {
            $(this).parent().next('.sp_placeholder').remove();
        });
        //调用回复框的提示信息
        promptInfo();
        submitAjaxData(1); 
    });
    /*回复框的提示输入信息*/
    function promptInfo() {
        $(".edit_box").text("我来说两句...").css({
            color: '#999',
            fontSize: '14px',
            fontFamily: '微软雅黑'
        }).focus(function () {
            $(this).text("").css("color", "#555555");
        });
    }
    //添加讨论回复
    function addComment(sid, calluid, cid, content,_this) {
        //content = encodeURIComponent(content);
        if(content.length>=2 && content.length<=140){
        	var data ="sid=" + sid + "&calluid=" + calluid + "&cid=" + cid + "&content=" + content;
        	   $.ajax({
                   type: "POST",
                   url: "/T/ConcurAjax/addcomment",
                   data: data,
                   success: function (msg) {
                       if (msg) {
                            $(".edit_box").val("");
                            $(".font_length").html("140");
                           submitAjaxData();
                       } else {
                           alert("发表讨论失败");
                       }
                   }
               });
        }else{
        	alert("请输入2-140个字符");
        }
     
    }
    //评论
    function submitAjaxData(page) {
    	 var condition=$(".wrp_discuss_list").attr("data-condition");
    	 var url=$(".wrp_discuss_list").attr("data-url")
        $.ajax({
            type: "POST",
            url: url,
            data: condition,
            success: function (msg) {
           	   $(".wrp_discuss_list").html(msg);
                  if (page == 1) {
                      $(".wraper_paging a:contains(1):first").addClass('selected');
                  }
            }
        });
    }
</script>
<div class="wpr_mclt_column4">
	  <h3 class="h3_title"><i></i>讨论区</h3><a name="discuss"></a>
	  <!-- 讨论区[ -->
	  <div class="wpr_comment">
	      <img src="<?php echo ($userPhoto); ?>" class="wpr_mlbt_left">
	      <div class="wpr_mlbt_right">
	          <form action="">
	              <div class="wrp_textarea">
	                  <textarea name="edit_box" class="edit_box" maxlength="140"></textarea>
	              </div>
	              <p class="ok_btn">最多可输入<strong class="font_length">140</strong>个字符<span class="okBtn1" data-uid="<?php echo ($uid); ?>" data-sid="<?php echo ($sid); ?>">确定</span></p>								
	          </form>
	      </div>
	  </div><!-- ]讨论区 -->
	  <!-- 列表显示讨论的内容 -->
	  <div class="wrp_discuss_list" id='getAjaxPage' data-url="/T/ConcurAjax/ajaxReply" data-condition='id=<?php echo ($sid); ?>' ></div>
</div>