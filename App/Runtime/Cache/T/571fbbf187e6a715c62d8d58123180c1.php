<?php if (!defined('THINK_PATH')) exit();?> 

 <style>
     .p_sxren{
         padding:20px 0px 10px 30px;
     }
     .p_sxren input{
         width:220px;
         height:27px;
         line-height:27px\9;
         color:#555555;
         font-size:14px;
         font-family:"微软雅黑";
         border:1px solid #999;
     }
     .div_sxcont{
         padding:10px 0px 10px 30px;
     }
     .div_sxcont textarea{
         width:270px;
         height:100px;
         resize:none;
         color:#555555;
         font-size:14px;
         font-family:"微软雅黑";
         border:1px solid #999;
     }
 </style>
<!--弹出层1[-->
   <div class="promptHtml msgbox" style="display:none;">
       <p class="p_sxren"><label>收信人 * </label>
       		<input type="text" class="toName"  name="toName" readonly="readonly" value="">
       		<input type="hidden" class="toid" name="toid" value="">
       </p>
       <div class="div_sxcont"><label>内&nbsp;&nbsp;&nbsp;容 * </label><textarea name="content" id="content"></textarea></div>
   </div> <!--]弹出层1-->