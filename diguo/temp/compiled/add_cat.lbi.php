<div id="shopbox" class="shopbox fixed-middle" style="top:400px;opacity:0;display:none;">
<div class="shopboxcon">
<div id="shoploading" style="display:none;">
<img alt="loading" src="themes/hd/images/loading.gif">
</div>
<div style="">
<h2>
<a class="track close" name="item-close-cart" href="javascript:;" onclick="catbox_hidden('shopbox')">
<span></span>
</a>
</h2>
<div class="spboxcontent">
<div class="shopboxdetail">
<div class="spboxleft">
<img src="themes/hd/images/DPshopcarIco.gif">
</div>
<div class="spboxright">
<span class="spboxtitle">该商品已成功放入购物车</span>
<span class="blank5"></span>
<p><span id="ECS_CARTINFO_flow"><?php 
$k = array (
  'name' => 'cart_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></span>

</p>

<span class="blank5"></span>
<p class="spbbtndiv">
<a class="track" target="_parent" href="flow.php"><img src="themes/hd/images/btn-gocart.gif"></a>
<a class="track" href="javascript:;" onclick="catbox_hidden('shopbox')"><img src="themes/hd/images/btn-goconn.gif"></a>
</p>
</div>
<span class="blank15"></span>
</div>
<?php if ($this->_var['bought_goods']): ?>
<div class="gmlist">
<h6>
购买过该商品的人还购买过：
<a class="track" target="_blank"  style="color:#A10000;margin-left:80px;" href="search.php?intro=best">更多您可能喜欢的商品&gt;&gt;</a>
</h6>
<ul>
<?php $_from = $this->_var['bought_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'bought_goods_data');if (count($_from)):
    foreach ($_from AS $this->_var['bought_goods_data']):
?>   
<li>
<div class="ygmPic">
<a href="<?php echo $this->_var['bought_goods_data']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['bought_goods_data']['goods_thumb']; ?>" alt="<?php echo $this->_var['bought_goods_data']['goods_name']; ?>" /></a>
</div>
 <a class="ygmName" href="<?php echo $this->_var['bought_goods_data']['url']; ?>" target="_blank" title="<?php echo $this->_var['bought_goods_data']['goods_name']; ?>"><?php echo $this->_var['bought_goods_data']['short_name']; ?></a>
<p class="ygmPrice">
<span>售价 <?php if ($this->_var['bought_goods_data']['promote_price'] != 0): ?><?php echo $this->_var['bought_goods_data']['formated_promote_price']; ?><?php else: ?><?php echo $this->_var['bought_goods_data']['shop_price']; ?><?php endif; ?></span>
</p>
</li>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>   
</ul>
</div>
<?php endif; ?>
</div>
</div>
</div>
<span class="blank0"> </span>

</div>
  <script language="javascript">
function addToCart_choose(goodsId, parentId)
{
  var goods        = new Object();
  var spec_arr     = new Array();
  var fittings_arr = new Array();
  var number       = 1;
  var formBuy      = document.forms['ECS_FORMBUY'];
  var quick		   = 0;

  // 检查是否有商品规格 
  if (formBuy)
  {
    spec_arr = getSelectedAttributes(formBuy);

    if (formBuy.elements['number'])
    {
      number = formBuy.elements['number'].value;
    }
    else{
	    number = $("#quantity").html();

	    
    }
	quick = 1;
  }

  goods.quick    = quick;
  goods.spec     = spec_arr;
  goods.goods_id = goodsId;
  goods.number   = number;
  goods.parent   = (typeof(parentId) == "undefined") ? 0 : parseInt(parentId);

 Ajax.call('flow.php?step=add_to_cart', 'goods=' + obj2str(goods), addToCartResponse_choose, 'POST', 'JSON');//官方原模板
 // Ajax.call('flow.php?step=add_to_cart', 'goods=' + objToJSONString(goods), addToCartResponse_choose, 'POST', 'JSON');//有的改了模板机制的！
}
/* *
 * 处理添加商品到购物车的反馈信息
 */
function addToCartResponse_choose(result)
{
  if (result.error > 0)
  {
    // 如果需要缺货登记，跳转
    if (result.error == 2)
    {
      if (confirm(result.message))
      {
        location.href = 'user.php?act=add_booking&id=' + result.goods_id + '&spec=' + result.product_spec;
      }
    }
    // 没选规格，弹出属性选择框
    else if (result.error == 6)
    {
      openSpeDiv(result.message, result.goods_id, result.parent);
    }
    else
    {
      alert(result.message);
    }
  }
  else
  {
    var cartInfo = document.getElementById('ECS_CARTINFO');
	var cartInfo_flow = document.getElementById('ECS_CARTINFO_flow');
    var cart_url = 'flow.php?step=cart';
    if (cartInfo)
    {
      cartInfo.innerHTML = result.content;
    }
    if (cartInfo_flow)
    {
      cartInfo_flow.innerHTML = result.content2;
    }
    catbox_show('shopbox');
  }
}

function catbox_show(elfm)
{
	var cart_timecount=0;
	var cat_box = document.getElementById(elfm);
	cat_box.style.display='block';
	var aaaa = setInterval(function(){
	cart_timecount=cart_timecount+0.05;
	cat_box.style.opacity=cart_timecount;
	if(cart_timecount>=1)clearInterval(aaaa);
	},10)
}
function catbox_hidden(elfm)
{
	var cart_timecount=0;
	var cat_box = document.getElementById(elfm);
	cat_box.style.display='none';
	var aaaa = setInterval(function(){
	cart_timecount=cart_timecount+0.05;
	cat_box.style.opacity=cart_timecount;
	if(cart_timecount>=1)clearInterval(aaaa);
	},10)
}
  </script>