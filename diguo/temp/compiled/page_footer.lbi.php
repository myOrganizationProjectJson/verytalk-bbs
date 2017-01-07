<?php if ($this->_var['helps']): ?>
<div id="Index_foot">
<?php $_from = $this->_var['helps']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'help_cat');if (count($_from)):
    foreach ($_from AS $this->_var['help_cat']):
?>     
  <div class="Index_foot_NR">
      <div class="Index_foot_title">
          <a href='<?php echo $this->_var['help_cat']['cat_id']; ?>' title="<?php echo $this->_var['help_cat']['cat_name']; ?>"><?php echo $this->_var['help_cat']['cat_name']; ?></a>
      </div>
      <ul>
          <?php $_from = $this->_var['help_cat']['article']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item_0_32812500_1397790200');if (count($_from)):
    foreach ($_from AS $this->_var['item_0_32812500_1397790200']):
?>
          <li style="position: relative;"><a href="<?php echo $this->_var['item_0_32812500_1397790200']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['item_0_32812500_1397790200']['title']); ?>"><?php echo $this->_var['item_0_32812500_1397790200']['short_title']; ?></a></li>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      </ul>
    </div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
<?php endif; ?> 
<div class="Index_foot_black"> 
      <?php if ($this->_var['navigator_list']['bottom']): ?>
   <?php $_from = $this->_var['navigator_list']['bottom']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav_0_32812500_1397790200');$this->_foreach['nav_bottom_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav_bottom_list']['total'] > 0):
    foreach ($_from AS $this->_var['nav_0_32812500_1397790200']):
        $this->_foreach['nav_bottom_list']['iteration']++;
?>
        <a href="<?php echo $this->_var['nav_0_32812500_1397790200']['url']; ?>" <?php if ($this->_var['nav_0_32812500_1397790200']['opennew'] == 1): ?> target="_blank" <?php endif; ?>><?php echo $this->_var['nav_0_32812500_1397790200']['name']; ?></a>
        <?php if (! ($this->_foreach['nav_bottom_list']['iteration'] == $this->_foreach['nav_bottom_list']['total'])): ?>
          &nbsp;&nbsp;|&nbsp;&nbsp;
        <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  <?php endif; ?>
    </div>
    
  
<?php if ($this->_var['img_links'] || $this->_var['txt_links']): ?>  
    <style>
    .friendlink{width:auto;}
    </style>
          <div class="Index_foot_black00" style=" position: relative;">
         <span style="float:left;margin-left:230px;">友情链接：</span>
         <img src="themes/hd/images/d.png" style="position: absolute;right: 6px;top: 6px;cursor:pointer;" id="fold">
         <ul class= "friendlink">
     <?php $_from = $this->_var['img_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['link']):
?>
    <li><a href="<?php echo $this->_var['link']['url']; ?>" target="_blank" title="<?php echo $this->_var['link']['name']; ?>"><img src="<?php echo $this->_var['link']['logo']; ?>" alt="<?php echo $this->_var['link']['name']; ?>" border="0" /></a></li>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php if ($this->_var['txt_links']): ?>
    <?php $_from = $this->_var['txt_links']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'link');if (count($_from)):
    foreach ($_from AS $this->_var['link']):
?>
    <li><a href="<?php echo $this->_var['link']['url']; ?>" target="_blank" title="<?php echo $this->_var['link']['name']; ?>"><?php echo $this->_var['link']['name']; ?></a></li>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <?php endif; ?>
         </ul>
         &nbsp;&nbsp;
      </div>
	<?php endif; ?>

<div class="Index_foot_black00">
 <span> <?php echo $this->_var['copyright']; ?><br />
 <?php echo $this->_var['shop_address']; ?> <?php echo $this->_var['shop_postcode']; ?>
 <?php if ($this->_var['service_phone']): ?>
      Tel: <?php echo $this->_var['service_phone']; ?>
 <?php endif; ?>
 <?php if ($this->_var['service_email']): ?>
      E-mail: <?php echo $this->_var['service_email']; ?><br />
 <?php endif; ?>  <?php if ($this->_var['icp_number']): ?>
  <?php echo $this->_var['lang']['icp_number']; ?>:<a href="http://www.miibeian.gov.cn/" target="_blank"><?php echo $this->_var['icp_number']; ?></a>
  <?php endif; ?>   <?php if ($this->_var['stats_code']): ?>
  <?php echo $this->_var['stats_code']; ?>
    <?php endif; ?>
</span>
</div>
<div id="returnTop" style="background-position:0 -81px;"></div>
<script language="javascript" type="text/javascript">
$(window).scroll(function(){
    var tt = document.documentElement.scrollTop||document.body.scrollTop;;
    if(tt>500){
      $('#returnTop').show();
    }
    else{
       $('#returnTop').hide();
    }
 });
$('#returnTop').click(function(){
    $("html, body").animate({'scrollTop':0},320);
})

</script>
  <div class="Index_foot_pinpai"></div>
<div style="height:8px; width:100%; float:left"></div>

 <div style="background:url(themes/hd/images/kefu3.jpg) no-repeat;width:100px;height:310px; float:right; position:fixed; left:0; bottom:0; z-index:99" id="div_leftfloat"> 
    <a href="javascript:closeLeftFloat();" style="display: block;width: 100px;height: 15px;position: absolute;top: 0;cursor:pointer;z-index:999"></a>
    <a href="javascript:void(0);" onclick="javascript:NTKF.im_openInPageChat();" style="display:block;width:100px;height: 90px;position: absolute;top: 83px;z-index:999cursor:pointer;"></a>
  </div>
 <script type="text/javascript">
     function closeLeftFloat()
     {
        $("#div_leftfloat").css("display","none");
     }
  </script>