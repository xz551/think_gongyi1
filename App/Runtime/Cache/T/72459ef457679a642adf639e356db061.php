<?php if (!defined('THINK_PATH')) exit();?>  
  <!-- 一个讨论 -->
<?php if(is_array($commentInfo)): $i = 0; $__LIST__ = $commentInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="wraper_taolun">
        <div class="taolun">
            <a href="<?php echo userUrl($vo['uid']);?>" target="_blank"><img src="<?php echo ($vo['image']); ?>" title="<?php echo ($vo['nickname']); ?>"></a>
            <div class="taolun_right">
                <p class="taolun_rht_uname"><a href="<?php echo userUrl($vo['uid']);?>" class="a_uname" title="<?php echo ($vo['nickname']); ?>" target="_blank"><?php echo (str_ellipsis_new($vo['nickname'],12)); ?></a><span class="a_time"><?php echo ($vo['time']); ?></span></p>
                <div class="wrp_discuss_cont">
                    <p class="taolun_rht_cont"><?php echo ($vo['content']); ?></p>
                </div>
                <a href="javascript:void(0);" class="a_reply">回复</a>
                <form action="#">
                    <textarea name="edit_box1" class="edit_box1" cols="30" rows="10"></textarea>
                    <span class="okBtn2" data-sid="<?php echo ($id); ?>" data-calluid="<?php echo ($vo['uid']); ?>" data-cid="<?php echo ($vo['id']); ?>">确定</span>
                </form>
            </div>
        </div>
        <?php if(!empty($vo["reply"])): if(is_array($vo["reply"])): $i = 0; $__LIST__ = $vo["reply"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class='taolun1'>
   					<a href="<?php echo userUrl($v['uid']);?>" target="_blank"><img src="<?php echo ($v['image']); ?>" title="<?php echo ($v['nickname']); ?>"></a>
   					<div class='taolun_right'>
        				<p class='taolun_rht_uname'><a  href="<?php echo userUrl($v['uid']);?>" class='a_uname' title="<?php echo ($v['nickname']); ?>" target="_blank"><?php echo (str_ellipsis_new($v['nickname'],12)); ?></a>回复&nbsp;&nbsp;&nbsp;&nbsp;<a class='a_uname' href="<?php echo userUrl($v['calluid']);?>" title="<?php echo ($v['callNickname']); ?>" target="_blank"><?php echo (str_ellipsis_new($v['callNickname'],10)); ?></a><span class='a_time'><?php echo ($v['time']); ?></span></p>
        				<div class='wrp_discuss_cont'>
            				<p class='taolun_rht_cont'><pre><?php echo ($v['content']); ?></pre></p>
        				</div>
        				<a href='javascript:void(0);' class='a_reply'>回复</a>
        				<form action='#'>
            				<textarea name='edit_box1' class='edit_box1' cols='30' rows='10'></textarea>
            				<span class='okBtn2' data-sid="<?php echo ($id); ?>" data-calluid="<?php echo ($v['uid']); ?>" data-cid="<?php echo ($v["cid"]); ?>">确定</span>
        				</form>
   					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
<?php if(($num) > "8"): ?><div class="wraper_paging">
    <?php echo ($page); ?>
</div><?php endif; ?>
<script type="text/javascript" src="<?php echo STATIC_SERVER_URL;?>/usercenter/group/js/ajaxpage.js"></script>