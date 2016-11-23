<?php if (!defined('THINK_PATH')) exit();?><div class="welfare_content">
    <div class="gongyi_news">
        <p class="reports_title"><img src="<?php echo STATIC_SERVER_URL;?>/gongyi/images/gongyidaodao.png"/>&nbsp;<a href="http://www.gy.com/gong_yi_bao_dao.html">公益报道</a></p>
        <?php if(is_array($baodao)): $i = 0; $__LIST__ = $baodao;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dl class="information_one">
                <dt><img src="<?php echo STATIC_SERVER_URL;?>/gongyi/images/duobianxing.png"/><a href="http://www.gy.com/news/<?php echo ($vo['id']); ?>.html"><strong><?php echo ($vo['title']); ?></strong></a></dt>
                <dd><?php echo ($vo['summary']); ?></dd>
            </dl><?php endforeach; endif; else: echo "" ;endif; ?> 


    </div>

    <div class="gongyi_news">
        <p class="reports_title"><img src="<?php echo STATIC_SERVER_URL;?>/gongyi/images/gongyiyuqing.png"/>&nbsp;<a href="http://www.gy.com/gong_yi_yu_qing.html">公益舆情</a></p>
        <?php if(is_array($yuqing)): $i = 0; $__LIST__ = $yuqing;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dl class="information_one">
                <dt>
                <img src="<?php echo STATIC_SERVER_URL;?>/gongyi/images/duobianxing.png"/>
                <a href="http://www.gy.com/news/<?php echo ($vo['id']); ?>.html"><strong><?php echo ($vo['title']); ?></strong></a>
                </dt>
                <dd><?php echo ($vo['summary']); ?></dd>
            </dl><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>