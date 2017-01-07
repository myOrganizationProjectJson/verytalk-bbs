<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />

<title><?php echo $this->_var['page_title']; ?></title>

<link rel="shortcut icon" href="favicon.ico" />
<link rel="icon" href="animated_favicon.gif" type="image/gif" />

<?php echo $this->smarty_insert_scripts(array('files'=>'common.js')); ?>
<link href="themes/hd/handu_base.css" rel="stylesheet" type="text/css" />
<link href="themes/hd/handu_style.css" rel="stylesheet" type="text/css" />
<link href="themes/hd/handu_detail.css" rel="stylesheet" type="text/css" />
<link href="themes/hd/jqzoom.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function $id(element) {
  return document.getElementById(element);
}
//切屏--是按钮，_v是内容平台，_h是内容库
function reg(str){
  var bt=$id(str+"_b").getElementsByTagName("h2");
  for(var i=0;i<bt.length;i++){
    bt[i].subj=str;
    bt[i].pai=i;
    bt[i].style.cursor="pointer";
    bt[i].onclick=function(){
      $id(this.subj+"_v").innerHTML=$id(this.subj+"_h").getElementsByTagName("blockquote")[this.pai].innerHTML;
      for(var j=0;j<$id(this.subj+"_b").getElementsByTagName("h2").length;j++){
        var _bt=$id(this.subj+"_b").getElementsByTagName("h2")[j];
        var ison=j==this.pai;
        _bt.className=(ison?"":"h2bg");
      }
    }
  }
  $id(str+"_h").className="none";
  $id(str+"_v").innerHTML=$id(str+"_h").getElementsByTagName("blockquote")[0].innerHTML;
}
</script>
<script type="text/javascript">
function changeAtt(t,a,goods_id) {
t.lastChild.checked='checked';
for (var i = 0; i<t.parentNode.childNodes.length;i++) {
if (t.parentNode.childNodes[i].className == 'selected') {
t.parentNode.childNodes[i].className = '';
}
}

t.className = "selected";
var formBuy = document.forms['ECS_FORMBUY'];
spec_arr = getSelectedAttributes(formBuy);
Ajax.call('goods.php?act=get_products_info', 'id=' + spec_arr+ '&goods_id=' + goods_id, shows_number, 'GET', 'JSON');
changePrice();
}
function shows_number(result)
{
if(result.product_number !=undefined)
{
document.getElementById('shows_number').innerHTML = result.product_number+'件';
}
else
{
document.getElementById('shows_number').innerHTML = '<font color=#ff0000>缺货</font>';
}
}
</script>
<script type="text/javascript" src="themes/hd/js/script.js"></script>
<script type="text/javascript" src="themes/hd/js/transport_ec.js"></script>
</head>
<body>
<?php echo $this->fetch('library/page_header.lbi'); ?>
<div class='detail_wrap'>
<div class="handu_crumb" id='detail_crumb'> <?php echo $this->fetch('library/ur_here.lbi'); ?></div>
<div class='side_bar'>
  <ul id="cate_guide">
      <h1>商品分类</h1>
      <?php $_from = $this->_var['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat');if (count($_from)):
    foreach ($_from AS $this->_var['cat']):
?>
              <li><a href="<?php echo $this->_var['cat']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['cat']['name']); ?>" target="_blank"><?php echo htmlspecialchars($this->_var['cat']['name']); ?></a></li>
         <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>   
        </ul>
</div>
<div class='product_detail'>
    <div class='detail_top'>
        
        <div class='goods_detail_left '>

    <div class='gallery' >
        <div class='gallery_content'>
            <a href="<?php echo $this->_var['goods']['goods_img']; ?>" class="jqzoom" title="商品大图" rel="gal1"><img id="masterImage" width="480" src='<?php echo $this->_var['goods']['goods_img']; ?>' /></a>
            <div class="videoplayer"></div>
            <div class="colorImg"></div>
        </div>
        <ul class='gallery_nav'>
             <?php $_from = $this->_var['pictures']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'picture');$this->_foreach['ptab'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['ptab']['total'] > 0):
    foreach ($_from AS $this->_var['picture']):
        $this->_foreach['ptab']['iteration']++;
?>
               <li>
            <a  href="javascript:void(0);" rel="{gallery: 'gal1', smallimage: '<?php echo $this->_var['picture']['img_url']; ?>',largeimage: '<?php echo $this->_var['picture']['img_url']; ?>'}">
            <s></s>
            <img src='<?php echo $this->_var['picture']['thumb_url']; ?>' />
            </a>
            </li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>       
            </li>
             
        </ul>
    </div>
    
<script type="text/javascript">
    $('.gallery_nav a').click(function(){
        $('.videoplayer').hide();
        $('.colorImg').hide();
    });
    $('.btn_playvideo').click(function(){  
        var _vsrc='';
        if($(".videoplayer embed").length==0){
            $(".videoplayer").html('<embed src="'+_vsrc+'" allowFullScreen="true" quality="high" width="480" height="480" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>');
        }
        $('.colorImg').hide();
        $(".videoplayer").show();
   
    });
</script>
            <div class='dashed'></div>
            
            <p>
                <a href="javascript:collect(<?php echo $this->_var['goods']['goods_id']; ?>)" class='like fl'>收藏（<?php 
$k = array (
  'name' => 'goods_collect',
  'goods_id' => $this->_var['goods_id'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>）</a>
                
                <div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare">
                <span class="bds_more">分享到：</span>
                <a class="bds_qzone"></a>
                <a class="bds_tsina"></a>
                <a class="bds_tqq"></a>
                <a class="bds_renren"></a>
                <a class="bds_t163"></a>
                <a class="shareCount"></a>
                </div>
                <script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=0" ></script>
                <script type="text/javascript" id="bdshell_js"></script>
                <script type="text/javascript">
                document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000)
                </script>
                
            </p>
        </div>
        
        <form action="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)" method="post" name="ECS_FORMBUY" id="ECS_FORMBUY">
        <div class="gooods_detail_right">
        <div id="goods_detail_1" class='product_detail_info'>
            <h1 class="product_name">
            <span>            </span>
            <?php echo $this->_var['goods']['goods_style_name']; ?></br><em><?php echo $this->_var['goods']['goods_brief']; ?></em></h1>
           <ul> 
            <li>
             商品货号：&nbsp;&nbsp;<span class='code'><?php echo $this->_var['goods']['goods_sn']; ?></span>&nbsp;&nbsp;&nbsp;&nbsp;
                          售价：<del class='market_price'><?php echo $this->_var['goods']['market_price']; ?></del>
                        </li>
            <li class="li_relative">
                            促 销 价：
                <em class='promote_price <?php if ($this->_var['goods']['is_promote'] && $this->_var['goods']['gmt_end_time']): ?>ecymar<?php endif; ?>' id="ECS_SHOPPRICE"><?php echo $this->_var['goods']['shop_price_formated']; ?></em>
                <em class='discount'><?php echo $this->_var['goods']['cuxiao_zhekou_price']; ?>折</em>
             
                        
                          
              <span class="vipPrice">
                <span class="vipPrice_span1">会员专享价</span>
                <span class="vipPrice_span2"><img src="themes/hd/images/d.png" class="d_list"></span>
                  <div class="vipPriceMain" status="hide" >
                    <div class="vipPriceContent">
                                             <p><b>升级成为普通会员：</b>您只需要成功交易一笔订单，就可以享受正价商品<a>9.8</a>折优惠</p>
                        <p><b>升级成为黄金会员：</b>您只需要消费500.00元，就可以享受正价商品<a>9.5</a>折优惠</p>
                        <p><b>升级成为白金会员：</b>您只需要消费2000.00元，就可以享受正价商品<a>8.5</a>折优惠</p>
                        <p><b >升级成为钻石会员：</b>您只需要消费5000.00元，就可以享受正价商品<a >8</a>折优惠</p> 
                           <div class="vipPriceMore"><a href="#"><span class="vipPrice_more fr"></span><span class="fr">更多</span></a></div>
                    </div>
                  </div>
              </span>
              
           </li>
             <?php if ($this->_var['goods']['is_promote'] && $this->_var['goods']['gmt_end_time']): ?>
      <?php echo $this->smarty_insert_scripts(array('files'=>'lefttime.js')); ?>
           <li>促　　销：&nbsp;<font class="ecycx"><?php echo $this->_var['goods']['promote_price']; ?></font> <?php echo $this->_var['lang']['residual_time']; ?> <font class="f4" id="leftTime"><?php echo $this->_var['lang']['please_waiting']; ?></font> </li>
            <?php endif; ?>
            <li>销　　量：&nbsp;&nbsp;<span class='sale_count'><?php 
$k = array (
  'name' => 'goods_sells',
  'goods_id' => $this->_var['goods_id'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>件</span> </li>
            <li>用户评分： 
                  <span class="star-off"><img src="themes/hd/images/stars<?php echo $this->_var['goods']['comment_rank']; ?>.gif" alt="comment rank <?php echo $this->_var['goods']['comment_rank']; ?>" /></span>
                  <span class='comment_num'>(共有<a href='#goods_comments_a' onclick="$('#goods_comments_a').parent().click()"><?php 
$k = array (
  'name' => 'pl_sum',
  'goods_id' => $this->_var['goods_id'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></a>条评论)</span>
           
            </li>
            </ul>   
        </div> 
        
        <div id="goods_detail_2" class='product_detail_info'>  
             
      <?php $_from = $this->_var['specification']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('spec_key', 'spec');if (count($_from)):
    foreach ($_from AS $this->_var['spec_key'] => $this->_var['spec']):
?>
     <div class="cattlist"> <div class="fl te"><?php echo $this->_var['spec']['name']; ?>：</div> 
        
                    <?php if ($this->_var['spec']['attr_type'] == 1): ?>
                      <?php if ($this->_var['cfg']['goodsattr_style'] == 1): ?>
                        <div class="catt">
                        <ul>
                        <li style="border:0px;">
                        <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
?>
<a <?php if ($this->_var['key'] == 0): ?>class="selected"<?php endif; ?> onclick="changeAtt(this,<?php echo $this->_var['value']['id']; ?>,<?php echo $this->_var['goods']['goods_id']; ?>)" href="javascript:;" name="<?php echo $this->_var['value']['id']; ?>" title="[<?php if ($this->_var['value']['price'] > 0): ?><?php echo $this->_var['lang']['plus']; ?><?php elseif ($this->_var['value']['price'] < 0): ?><?php echo $this->_var['lang']['minus']; ?><?php endif; ?> <?php echo $this->_var['value']['format_price']; ?>]"><?php echo $this->_var['value']['label']; ?><input style="display:none" id="spec_value_<?php echo $this->_var['value']['id']; ?>" type="radio" name="spec_<?php echo $this->_var['spec_key']; ?>" value="<?php echo $this->_var['value']['id']; ?>" <?php if ($this->_var['key'] == 0): ?>checked<?php endif; ?> /></a>
                      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                      </li>
                      </ul>
					  </div>
                        <input type="hidden" name="spec_list" value="<?php echo $this->_var['key']; ?>" />
                        <?php else: ?>
                        <select name="spec_<?php echo $this->_var['spec_key']; ?>" onchange="changePrice()">
                          <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
?>
                          <option label="<?php echo $this->_var['value']['label']; ?>" value="<?php echo $this->_var['value']['id']; ?>"><?php echo $this->_var['value']['label']; ?> <?php if ($this->_var['value']['price'] > 0): ?><?php echo $this->_var['lang']['plus']; ?><?php elseif ($this->_var['value']['price'] < 0): ?><?php echo $this->_var['lang']['minus']; ?><?php endif; ?><?php if ($this->_var['value']['price'] != 0): ?><?php echo $this->_var['value']['format_price']; ?><?php endif; ?></option>
                          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </select>
                        <input type="hidden" name="spec_list" value="<?php echo $this->_var['key']; ?>" />
                      <?php endif; ?>
                    <?php else: ?>
                      <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
?>
                      <label for="spec_value_<?php echo $this->_var['value']['id']; ?>">
                      <input type="checkbox" name="spec_<?php echo $this->_var['spec_key']; ?>" value="<?php echo $this->_var['value']['id']; ?>" id="spec_value_<?php echo $this->_var['value']['id']; ?>" onclick="changePrice()" />
                      <?php echo $this->_var['value']['label']; ?> [<?php if ($this->_var['value']['price'] > 0): ?><?php echo $this->_var['lang']['plus']; ?><?php elseif ($this->_var['value']['price'] < 0): ?><?php echo $this->_var['lang']['minus']; ?><?php endif; ?> <?php echo $this->_var['value']['format_price']; ?>] </label><br />
                      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                      <input type="hidden" name="spec_list" value="<?php echo $this->_var['key']; ?>" />
                    <?php endif; ?> 
                    </div>
                 <div style="clear:both"></div>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                     
              <div class="set">
              <span class="label">数　　量：</span>
              
              <span class="amount-widget">
                  <span class="increase" onclick="goods_add();changePrice()">+</span>
                  <span class="decrease" onclick="goods_cut();changePrice()">-</span>
                  <input onblur="changePrice();get_shipping_list(forms['ECS_FORMBUY'],<?php echo $this->_var['goods']['goods_id']; ?>);" name="number" id="number" class="text" value="1" maxlength="3" title="请输入购买量" type="text"> 
              </span>
              商品总价：<font id="ECS_GOODS_AMOUNT" class="shop"></font> 库存：<font id="shows_number"><?php echo $this->_var['goods']['goods_number']; ?>件</font>
              <script language="javascript" type="text/javascript">
			function goods_cut(){
				var num_val=document.getElementById('number');
				var new_num=num_val.value;
				 if(isNaN(new_num)){alert('请输入数字');return false}
				var Num = parseInt(new_num);
				if(Num>1)Num=Num-1;
				num_val.value=Num;
			}
			function goods_add(){
				var num_val=document.getElementById('number');
				var new_num=num_val.value;
				 if(isNaN(new_num)){alert('请输入数字');return false}
				var Num = parseInt(new_num);
				Num=Num+1;
				num_val.value=Num;
			}
	    </script>
            </div>  
           
              <p class='detail_btn_set'>
                 <a class='fl detail_btn buy' href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)" title='点击购买'></a>
                 <a class='fl detail_btn addCart' href="javascript:addToCart_choose(<?php echo $this->_var['goods']['goods_id']; ?>)" title='加入购物车'></a>
                </p>
               <div style="clear:both"></div> 
        </div>
        <div class='product_detail_info' style='border-bottom:none;'>       
            
            <div class='extra_info'>
                <ul class="line1">  
                   <li class="et_qqonline">
                    <a href="http://wpa.qq.com/msgrd?v=3&amp;uin=77898852&amp;site=qq&amp;menu=yes">
                      <span >在线客服:</span>
                      <span class="payway3"></span>
                    </a>
                    </li>
                    <li class="et_weixian" >
                        <a id="zzy_weixin">
                            <span>官方微信:</span>
                            <img src="themes/hd/images/weixin.png" alt="官方微信" id="weixin">
                           <img src="themes/hd/images/qrcode.jpg" class="qrcode"> 
                        </a>
                    <img src="themes/hd/images/sys.gif" style="position: absolute;top: -6px;left: 70px;">
                    </li>
                    <li class="et_shouji" style="">
                        <a><span >手机下单更优惠&nbsp;</span>
                           <img src="themes/hd/images/client_code.jpg" class="qrcode_thumb"alt="手机客户端" width="20">
                           <img src="themes/hd/images/client_code.jpg" class="qrcode"> 
                        </a>
                     </li>
                 
                  <div class="clear"></div>
                </ul>
                <div class="goods_payway line2">
                    <label >支付方式:</label>
                    <a href="#">
                      <span class="payway1 spanr"></span>
                      <span>支付宝</span>
                    </a>
                    <a href="#">
                      <span class="payway2 spanr"></span>
                      <span>网上银行付款</span>
                    </a>&nbsp;&nbsp;&nbsp;&nbsp;
                     <label for="">服务承诺：</label>
                     <img src="themes/hd/images/service.png" alt="100%正品保证 安全快速退款 7天无理由退换货" title="100%正品保证 安全快速退款 7天无理由退换货">
                    <div class="clear"></div>
                </div>
               
            </div>
            
        </div>
        </div>
        
        <br class="clear" />    
    </div>
     </form>
       <?php echo $this->fetch('library/goods_package_ecshop120.lbi'); ?>
  <div id='flat_tab' class='fr'>
        <div id="J_TabBarBox">
          <ul id="J_TabBar" class="tabbar tm-clear">
            <li class="current"><a href="javascript:void(0)" >商品详情</a></li>
            <li><a id="goods_comments_a" href="javascript:void(0)" >商品评价</a></li>
            <li><a href="javascript:void(0)" >尺码对照</a></li>
            <li><a href="javascript:void(0)" >常见问题</a></li>
 
          </ul>
          <a href="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)" id="quikAddCart" title="加入购物车" >加入购物车</a>
        </div>
        <ul class="flat_content">
           <li style="display: block;">
            
                <div class="attributes-list" id="J_AttrList">
                  <ul id="J_AttrUL">
                   <li>品牌: <?php echo $this->_var['goods']['goods_brand']; ?></li>   
         <li>货号: <?php echo $this->_var['goods']['goods_sn']; ?></li>          
      <?php $_from = $this->_var['properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'property_group');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['property_group']):
?>
        <?php $_from = $this->_var['property_group']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'property');if (count($_from)):
    foreach ($_from AS $this->_var['property']):
?>
        <li><?php echo htmlspecialchars($this->_var['property']['name']); ?>：<?php echo $this->_var['property']['value']; ?></li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
                                      </ul>
                </div>
   
                <div class="goods_desc">
                   
<?php echo $this->_var['goods']['goods_desc']; ?>
</div>
                              
				<div style="margin-top:30px;">
					<img src="themes/hd/images/suggest.png" style="vertical-align: -2px;"/> 如果您在韩都衣舍发现任何问题，欢迎<a href="message.php" target="_blank" style="color: #35a;margin-left: 3px;">提点建议</a>
				</div>
            </li>
            <li>
            <?php echo $this->fetch('library/comments.lbi'); ?>

</li>

            <li id="goods_size_table">
               <center style="padding: 20px 0;"><table style="border-bottom: #e6e6e6 1px solid; text-align: center; border-left: #e6e6e6 1px solid; border-top: #e6e6e6 1px solid; border-right: #e6e6e6 1px solid" border="0" cellpadding="0" cellspacing="0" height="109" width="749">
                <tbody>
                    <tr style="font-family: 微软雅黑; color: #ffffff; font-size: 12px" bgcolor="#484848">
                        <td height="25" width="93">尺码</td>
                        <td height="25" width="93">衣长</td>
                        <td height="25" width="93">肩宽</td>
                        <td height="25" width="93">胸围</td>
                        <td height="25" width="93">袖长</td>
                        <td height="25" width="93">领宽</td>
                        <td height="25" width="93">袖口围</td>
                        <td height="25" width="93">下摆围</td>
                    </tr>
                    <tr style="font-family: 微软雅黑; color: #5c5c5c; font-size: 12px">
                        <td height="28" width="93">S</td>
                        <td height="28" width="93">65</td>
                        <td height="28" width="93">37</td>
                        <td height="28" width="93">106</td>
                        <td height="28" width="93">44</td>
                        <td height="28" width="93">24.5</td>
                        <td height="28" width="93">33</td>
                        <td height="28" width="93">98</td>
                    </tr>
                    <tr style="background-color: #f7f7f7; font-family: 微软雅黑; color: #5c5c5c; font-size: 12px">
                        <td height="28">M</td>
                        <td height="28">66</td>
                        <td height="28">38</td>
                        <td height="28">110</td>
                        <td height="28">45</td>
                        <td height="28">25</td>
                        <td height="28">34</td>
                        <td height="28">102</td>
                    </tr>
                </tbody>
            </table><table style="border-bottom: #e6e6e6 1px solid; text-align: center; border-left: #e6e6e6 1px solid; font-family: 微软雅黑; color: #5c5c5c; font-size: 12px; border-top: #e6e6e6 1px solid; border-right: #e6e6e6 1px solid" align="center" border="0" cellpadding="0" cellspacing="0" width="750">
    <tbody>
        <tr>
            <td style="font-family: 微软雅黑; color: #ffffff; font-size: 12px" bgcolor="#484848" height="28" width="110">试穿人</td>
            <td style="font-family: 微软雅黑; color: #ffffff; font-size: 12px" bgcolor="#484848" height="28" width="110">身高cm/体重kg</td>
            <td style="font-family: 微软雅黑; color: #ffffff; font-size: 12px" bgcolor="#484848" height="28" width="110">三围cm</td>
            <td style="font-family: 微软雅黑; color: #ffffff; font-size: 12px" bgcolor="#484848" height="28" width="110">试穿尺码</td>
            <td style="font-family: 微软雅黑; color: #ffffff; font-size: 12px" bgcolor="#484848" height="28" width="309">试穿感受</td>
        </tr>
        <tr>
            <td height="28" width="110">ELEN</td>
            <td height="28" width="110">165/48</td>
            <td height="28" width="110">80/72/83</td>
            <td height="28" width="110">M</td>
            <td height="28" width="309">上身时尚有范，面料厚实保暖。</td>
        </tr>
    </tbody>
</table></center>
            </li>
            <li class="goods_wenti">
                <center>
                <h4>支付方式</h4>
                <p>韩都衣舍为您提供在线支付、网上银行、货到付款等多种支付方式，可满足您不同的支付需求。</p>
                <h5>1.在线支付</h5>
                <p>韩都衣舍为您提供支付宝支付、网上银行两种在线支付方式，几乎涵盖所有大中型银行发行的银行卡，覆盖率达98%。选择在线支付，您的银行卡需要开通相应的网上银行业务。</p>
                <img src="themes/hd/images/zhifu_list.png" alt="" />
                <h5>2.货到付款</h5>
                <p>货到付款是韩都衣舍配送员送货上门，客户收单验货后，直接将货款交给配送员的一种结算方式。</p>
                <strong>更多支付方式问题请查看 <a href="#" target="_blank">在线支付</a></strong>　
                <br />
                <br />
                <h4>退换货政策</h4>
                    
                <img src="themes/hd/images/goods_tuihuozhengce.png" alt="" />
                <strong>更多售后服务问题请查看 <a href="#" target="_blank">售后服务</a></strong>
                </center>
            </li>
           
        </ul>
    </div>
    
    </div>
</div>
<?php echo $this->fetch('library/page_footer.lbi'); ?>
<link href="themes/hd/cart.css" rel="stylesheet" type="text/css"  charset="utf-8"> 
<?php echo $this->fetch('library/add_cat.lbi'); ?>
</body>
<script type="text/javascript" src="themes/hd/js/jquery_002.js"></script>
<script type="text/javascript">
    
    var _jqzoom_conf={'zoomType': 'standard', 'preloadImages': false,  'alwaysOn':false,'preloadText':'正在载入',
        'zoomWidth': 480, 'zoomHeight': 480,'lens':true,hideEffect:'fadeout','showEffect':'fadein'
        ,fadeoutSpeed:'2000'};
    $('.jqzoom').jqzoom(_jqzoom_conf);
    /* detail  infomation control*/
    $('#J_TabBar li').click(function(){
        var index = $(this).index();
        $(this).addClass('current').siblings().removeClass('current');
        $('.flat_content>li').eq(index).show().siblings().hide();
    })


$(function(){
     changeGoodsAttr(false);
    var barh=$('#J_TabBarBox').offset().top;
    var tab_l = $('#J_TabBar').offset().left;
    $(window).scroll(function(){
        var t=0;
        var scrollTop = document.documentElement.scrollTop||document.body.scrollTop;
        if(scrollTop>barh){
            $('#J_TabBarBox').addClass('fixed');
            $('#quikAddCart').show();
            if($.browser.msie&&($.browser.version == "7.0")) {
                $("#J_TabBarBox").css('left',tab_l);
            }
        }
        else{
            $('#J_TabBarBox').removeClass('fixed');
            $('#quikAddCart').hide();
            if($.browser.msie&&($.browser.version == "7.0")) {
                $("#J_TabBarBox").removeAttr('style');
            }
        }
    })
      //会员价     
    $(".vipPrice").hover(function(){
      $(".vipPriceMain").show();
    },function(){
      $(".vipPriceMain").hide();
    });
});
</script>
<script type="text/javascript">
function changeGoodsAttr(show_error,which){
    if(typeof(show_error)=='undefined'){
        show_error=true;
    }
}
</script>
<script type="text/javascript">
var goods_id = <?php echo $this->_var['goods_id']; ?>;
var goodsattr_style = <?php echo empty($this->_var['cfg']['goodsattr_style']) ? '1' : $this->_var['cfg']['goodsattr_style']; ?>;
var gmt_end_time = <?php echo empty($this->_var['promote_end_time']) ? '0' : $this->_var['promote_end_time']; ?>;
<?php $_from = $this->_var['lang']['goods_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
var goodsId = <?php echo $this->_var['goods_id']; ?>;
var now_time = <?php echo $this->_var['now_time']; ?>;


onload = function(){
  changePrice();
  try {onload_leftTime();}
  catch (e) {}
}

/**
 * 点选可选属性或改变数量时修改商品价格的函数
 */
function changePrice()
{
  var attr = getSelectedAttributes(document.forms['ECS_FORMBUY']);
  var qty = document.forms['ECS_FORMBUY'].elements['number'].value;

  Ajax.call('goods.php', 'act=price&id=' + goodsId + '&attr=' + attr + '&number=' + qty, changePriceResponse, 'GET', 'JSON');
}

/**
 * 接收返回的信息
 */
function changePriceResponse(res)
{
  if (res.err_msg.length > 0)
  {
    alert(res.err_msg);
  }
  else
  {
    document.forms['ECS_FORMBUY'].elements['number'].value = res.qty;

    if (document.getElementById('ECS_GOODS_AMOUNT'))
      document.getElementById('ECS_GOODS_AMOUNT').innerHTML = res.result;
  }
}

</script>
</html>
