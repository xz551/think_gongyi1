<?php if (!defined('THINK_PATH')) exit();?>	    <!-- 物资捐助[ -->
	       
		        <?php if($tag == 1): ?><p >为保证求助人、捐助人双方权益，请务必在平台上发起物资捐助请求，待发起人同意请求后，再将物资寄送至以下地址，不要盲目邮寄物资！</p>
		            <p class="text_info5">&nbsp;&nbsp;&nbsp;<a href="<?php echo U('/T/Donate/suppliesManager',array('id'=>$address['concur_id']));?>">物资捐助管理</a></p>
		            <?php W('Donate/address');?>
		        <?php else: ?>
		        	<?php if(!empty($supplies)): if(!empty($supplies['content'])): ?><span>已发货</span>
		        			<?php W('Donate/address');?>
			                <ul class="wuliu_info_list">
			                   <li><?php echo ($supplies['expTextName']); ?> 运单编号：<span class="sp_wlbh"><?php echo ($supplies['logistics_number']); ?></span></li>
							   <?php echo ($supplies['content']->data); ?>
			                </ul>
		        		<?php else: ?>
		        			<span>求助人已同意你的物资捐助请求，请尽快输入物流信息</span>
		        		 	<?php W('Donate/address');?>
		        			<div class="wraper_choice_wuliu">
			                 	<p>
			                      <label>请选择物流方式：</label>
			                      <select name="wuliu_list" id="ways_wuliu">
			                          <?php if(is_array($ems)): $i = 0; $__LIST__ = $ems;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" ><?php echo ($v); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			                      </select>
			                  	 </p>
			                     <p>
			                       	<label>请输入运单编号：</label>
			                      	<input type="text" name="ydcode" class="ydcode">
			                     </p>
			                  	 <p class="tab_cont1_btns"><label></label><span class="btns_manager">提交物流信息</span></p>
		           		 	</div>
		           		 	<ul class="wuliu_info_list"></ul><?php endif; ?>
		        	<?php else: ?>
			         	<p class="text_info5">为保证求助人、捐助人双方权益，请务必在平台上<a href="#wuzi">发起物资捐助请求</a>，待发起人同意请求后，再将物资寄送至以下地址，不要盲目邮寄物资！</p>
			            	<?php W('Donate/address'); endif; endif; ?>