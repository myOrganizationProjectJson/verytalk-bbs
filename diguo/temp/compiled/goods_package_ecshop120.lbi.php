<?php if ($this->_var['package_goods_list_120']): ?>
<style>
.clearfix:after{
content:"."; display:block; height:0; clear:both;
visibility:hidden;
}
*html .clearfix{
 height:1%;
}
*+html .clearfix{
 height:1%;
}
.blank{height:8px; line-height:8px; clear:both; visibility:hidden;}
.B_eee{border:1px solid #eee;width:130px;height:130px;}
.none{display:none;}
.package{background:#fff; padding-bottom:2px; overflow:hidden;}
.pa_tit{width:100%;height:33px;background:url(themes/hd/images/bg_package_ecshop120.gif) repeat-x 0 bottom;}
.pa_tit h2{
	float:left;width:132px;height:33px;line-height:33px;
	background:url(themes/hd/images/bg_package_ecshop120.gif) no-repeat 0 -33px;
	font-size:14px;text-align:center;margin-right:3px;
}
.pa_tit h2.current{background:url(themes/hd/images/bg_package_ecshop120.gif) no-repeat 0 0;}
.pa_box{height:auto;border:1px solid #dadada;border-top:none;padding-bottom:10px;}
.pa_box ul{float:left;width:77%;padding:10px;overflow:hidden;}
.pa_box ul li{float:left;width:163px;padding-left:10px;background:url(themes/hd/images/ico_add2_ecshop120.gif) no-repeat right 50px;}
.pa_box ul li a{color:#000;text-decoration:none;width:100px;display:block;}
.pa_box ul li.last{background:none;}
.pa_box .buypack{float:right;width:20%;padding-top:30px;}
.pa_box .buypack .f_yuan{font-size:14px; text-decoration:line-through;}
.pa_box .buypack .f_save{font-size:14px; font-weight:bold;}
.pa_box .buypack .f_pack{color:#ff3300; font-size:17px; font-weight:bold;}
.pa_box .buypack .f_pack1{color:#ff3300;font-size:14px;}
.btn_pack{width:103px;height:32px;margin-top:10px;border:none;background:url(themes/hd/images/ico_buypackage_ecshop120.gif) no-repeat 0 0;cursor:pointer;}
.pa_box .name{padding-top:3px;height:38px;line-height:18px;overflow:hidden;}
</style>
<div class="blank"></div>
<div class="package" >
	<div class="pa_tit" id="package_tit">
	<?php $_from = $this->_var['package_goods_list_120']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pa_item');$this->_foreach['pa_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['pa_list']['total'] > 0):
    foreach ($_from AS $this->_var['pa_item']):
        $this->_foreach['pa_list']['iteration']++;
?>
	<h2 <?php if ($this->_foreach['pa_list']['iteration'] == 1): ?>class="current"<?php endif; ?>>优惠套餐<?php echo $this->_foreach['pa_list']['iteration']; ?></h2>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</div>
	<div class="pa_box clearfix" >		
		<?php $_from = $this->_var['package_goods_list_120']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pa_item');$this->_foreach['pa_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['pa_list']['total'] > 0):
    foreach ($_from AS $this->_var['pa_item']):
        $this->_foreach['pa_list']['iteration']++;
?>
		<div id="package_box_<?php echo ($this->_foreach['pa_list']['iteration'] - 1); ?>" <?php if (($this->_foreach['pa_list']['iteration'] - 1) > 0): ?>class="none"<?php endif; ?>>
		<ul>
			<?php $_from = $this->_var['pa_item']['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'pa_goods');$this->_foreach['pa_list_goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['pa_list_goods']['total'] > 0):
    foreach ($_from AS $this->_var['pa_goods']):
        $this->_foreach['pa_list_goods']['iteration']++;
?>
			<li <?php if (($this->_foreach['pa_list_goods']['iteration'] == $this->_foreach['pa_list_goods']['total'])): ?>class="last"<?php endif; ?>>
			<a href="goods.php?id=<?php echo $this->_var['pa_goods']['goods_id']; ?>" target="_blank">
			<img src="<?php echo $this->_var['pa_goods']['goods_thumb']; ?>" class="B_eee" >
			</a>
			<p class="name"><a href="goods.php?id=<?php echo $this->_var['pa_goods']['goods_id']; ?>" target="_blank"><?php echo $this->_var['pa_goods']['goods_name']; ?><?php echo $this->_var['pa_goods']['goods_attr_str']; ?></a></p>
			<input type="checkbox" name="<?php echo $this->_var['pa_goods']['rank_price']; ?>" id="<?php echo $this->_var['pa_goods']['rank_price_zk']; ?>" value="<?php echo $this->_var['pa_goods']['goods_id']; ?>-<?php echo $this->_var['pa_goods']['product_id']; ?>" checked=checked onClick="check_package(<?php echo ($this->_foreach['pa_list']['iteration'] - 1); ?>,this);" <?php if (($this->_foreach['pa_list_goods']['iteration'] - 1) == 0): ?>style="display:none;"<?php endif; ?>  autocomplete="off">
			<font color=#ff3300><?php echo $this->_var['pa_goods']['rank_price_zk_format']; ?>  </font>
			</li>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</ul>
		<div class="buypack">
			<strong><?php echo $this->_var['lang']['old_price']; ?></strong><font class="f_yuan" id="price_yuan_<?php echo ($this->_foreach['pa_list']['iteration'] - 1); ?>"><?php echo $this->_var['pa_item']['subtotal']; ?></font><br />
			<strong><font class="f_pack1" >套餐价：</font></strong><font class="f_pack" id="price_pack_<?php echo ($this->_foreach['pa_list']['iteration'] - 1); ?>"><?php echo $this->_var['pa_item']['package_price']; ?></font><br />
			<strong><?php echo $this->_var['lang']['then_old_price']; ?></strong><font class="f_save" id="price_save_<?php echo ($this->_foreach['pa_list']['iteration'] - 1); ?>"><?php echo $this->_var['pa_item']['saving']; ?> </font><br />
			<input type="button" class="btn_pack" onClick="javascript:addPackageToCart(<?php echo $this->_var['pa_item']['act_id']; ?>, <?php echo ($this->_foreach['pa_list']['iteration'] - 1); ?>)" >			
		</div>
		</div>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</div>	 
	  
</div>
<div class="blank"></div>
<script type="text/javascript">
reg_package();
</script>
<?php endif; ?>