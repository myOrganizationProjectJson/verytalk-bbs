<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />

<title><?php echo $this->_var['page_title']; ?></title>

<link rel="shortcut icon" href="favicon.ico" />
<link rel="icon" href="animated_favicon.gif" type="image/gif" />
<link href="themes/hd/handu_base.css" rel="stylesheet" type="text/css" />
<link href="themes/hd/handu_style.css" rel="stylesheet" type="text/css" />
<link href="themes/hd/handu_nivoslider.css" rel="stylesheet" type="text/css" />
<link href="themes/hd/handu_flex.css" rel="stylesheet" type="text/css" />
<link href="themes/hd/zonghe_list.css" rel="stylesheet" type="text/css" />
<?php if ($this->_var['cat_style']): ?>
<link href="<?php echo $this->_var['cat_style']; ?>" rel="stylesheet" type="text/css" />
<?php endif; ?>

<?php echo $this->smarty_insert_scripts(array('files'=>'common.js,compare.js')); ?>
<script type="text/javascript" src="themes/hd/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="themes/hd/js/jquery.pack.js"></script>
<script type="text/javascript" src="themes/hd/js/jQuery.blockUI.js"></script>
<script type="text/javascript" src="themes/hd/js/jquery.SuperSlide.js"></script>
<script type="text/javascript" src="themes/hd/js/jquery.hoverdelay.js"></script>
</head>
<body>
<?php echo $this->fetch('library/page_header.lbi'); ?>
<div style="width:100%;height:auto;overflow: hidden;">
	<div class="list_main">
		<div class="list_main_Left">
			
			
<style type="text/css">
.cate_menu div{
  float:none;
}
.cate_menu{
  background: #F9F9F9;
  width: 200px;
  height: auto;
  overflow: hidden;
  min-height: 400px;
  color: #444;
 
}
.cate_menu h1{
  width:100%;
  background: #DDD;
  font:bold 14px/24px 'Microsoft YaHei', sans-serif;
  padding: 4px 0;
  text-indent: 4px;
   margin-top:3px;
   cursor:pointer;
   overflow:hidden;
}
 
.cate_menu h1 a:hover{
  text-decoration:none;
}
.cate_menu h2{
  background: url(themes/hd/images/s.png) no-repeat 2px center;
  font:bold 14px/24px 'Microsoft YaHei', sans-serif;
  text-indent: 9px;
  margin: 5px 0 0;
}
.cate_menu ul{
  height: auto;
  overflow: hidden;
  width: 100%;
text-align: justify;
  }
.cate_menu>ul
{
  padding-left: 4px;
  width:83%;
}
.cate_menu li{
  float: left;
  font-size: 12px;
  line-height: 22px;
  height:22px;
  list-style: none;
  text-indent: 4px;
display: inline-block;
margin-right:8px;
 white-space: nowrap;
}
.cate_menu li a{
  padding-right: 5px;
}
.cate_menu a:hover{
  cursor: pointer;
  text-decoration: underline;
}
.treeright{
  font-size: 18px;
  font-weight: bolder;
  color: #FFF;
  float: right;
  padding-right: 5px;
  line-height: 22px;
  display: block;
  width: 22px;
  text-align: center;
}
.cate_menu li span{
  float:right;
  padding:0 2px;
}
#list_con:hover a,#list_con a{
  background: #DDD;
  text-decoration: none;
  padding:4px;
}
</style>
<div class='cate_menu'>
 
<?php $this->assign('categories', get_categories_tree());?>
 <?php $_from = $this->_var['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat');$this->_foreach['childnum'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['childnum']['total'] > 0):
    foreach ($_from AS $this->_var['cat']):
        $this->_foreach['childnum']['iteration']++;
?> 
    <h1><a style="float:left;" href="<?php echo $this->_var['cat']['url']; ?>"><?php echo htmlspecialchars($this->_var['cat']['name']); ?></a><span class="treeright">+</span></h1>
   <ul>		
        <?php $_from = $this->_var['cat']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');$this->_foreach['childcur'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['childcur']['total'] > 0):
    foreach ($_from AS $this->_var['child']):
        $this->_foreach['childcur']['iteration']++;
?>         
     <h2><a href="<?php echo $this->_var['child']['url']; ?>"><?php echo htmlspecialchars($this->_var['child']['name']); ?></a></h2>
   		<ul>
     	 <?php $_from = $this->_var['child']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'childer');if (count($_from)):
    foreach ($_from AS $this->_var['childer']):
?>
		 <li><a href="<?php echo $this->_var['childer']['url']; ?>"<?php if ($this->_var['category'] == $this->_var['childer']['id']): ?> class="list_con"<?php endif; ?>><?php echo htmlspecialchars($this->_var['childer']['name']); ?></a></li>
		 <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
   		</ul>
 		 <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>      
   </ul>
 <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>  
            	
</div>
<script type="text/javascript">
var categoryParentIdKey = parseInt('0');
$(function (){
	
	var menuH1 =$("div.cate_menu>h1");
	var menuH1span2 =$("div.cate_menu>h1>span.treeright");
	var menuUl = $("div.cate_menu>ul");
	menuUl.hide();
	menuH1.each(function(i){
	
		
		var h1Obj = $(this);
		h1Obj.click(function(){
			menuUl.hide();
			menuH1span2.html('+');
			var hideOrShow = h1Obj.find(">span.treeright").html();
			if(hideOrShow == '+'){
				h1Obj.find(">span.treeright").html('-');
				menuUl.eq(i).show();
			}else{
				h1Obj.find(">span.treeright").html('+');
				menuUl.eq(i).hide();
			}
		});
		
			menuH1.eq(categoryParentIdKey).trigger("click");
	});
	
});
</script>			
			<div class="category_top10">
	<div class="category_tree_title category_paihang_title">热销排行榜</div>
	<ul class="category_paihang" style="padding-bottom:10px;">
     <?php $_from = $this->_var['hot_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['goodsnum'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['goodsnum']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['goodsnum']['iteration']++;
?>		
			<li>
			<div class="paihang_pic">
				<div class="paihang_num"><?php echo $this->_foreach['goodsnum']['iteration']; ?></div>
				<a href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" ><img src="<?php echo $this->_var['goods']['thumb']; ?>" alt="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" width="79" height="79" border="0" /></a>
			</div>
			<div class="paihang_nr">
				<span class="paihang_nrName"><a href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>"><?php echo htmlspecialchars($this->_var['goods']['name']); ?></a></span>
				<span class="paihang_nrmoney1"><del><?php echo $this->_var['goods']['market_price']; ?></del></span>
				<span class="paihang_nrmonery2">售价：<?php if ($this->_var['goods']['promote_price'] != ""): ?><?php echo $this->_var['goods']['promote_price']; ?><?php else: ?><?php echo $this->_var['goods']['shop_price']; ?><?php endif; ?></span>
			</div>
		</li>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>  
		</ul>
     <div style="height:10px; "></div>
</div>			
			
			
		</div>
		
		<div class="list_main_Right">
			<div class="weizhi"><?php echo $this->fetch('library/ur_here.lbi'); ?></div>

			<div class="paixu">
				<span class="fl">默认排序：</span>
				<span class="paixu_left">
				<form method="GET" class="sort" name="listform">
					
				  <a href="<?php echo $this->_var['script_name']; ?>.php?category=<?php echo $this->_var['category']; ?>&display=<?php echo $this->_var['pager']['display']; ?>&brand=<?php echo $this->_var['brand_id']; ?>&price_min=<?php echo $this->_var['price_min']; ?>&price_max=<?php echo $this->_var['price_max']; ?>&filter_attr=<?php echo $this->_var['filter_attr']; ?>&page=<?php echo $this->_var['pager']['page']; ?>&sort=goods_id&order=<?php if ($this->_var['pager']['sort'] == 'goods_id' && $this->_var['pager']['order'] == 'DESC'): ?>ASC<?php else: ?>DESC<?php endif; ?>#goods_list"><img src="themes/hd/images/goods_id_<?php if ($this->_var['pager']['sort'] == 'goods_id'): ?><?php echo $this->_var['pager']['order']; ?><?php else: ?>default<?php endif; ?>.jpg" alt="<?php echo $this->_var['lang']['sort']['goods_id']; ?>"></a>
  <a href="<?php echo $this->_var['script_name']; ?>.php?category=<?php echo $this->_var['category']; ?>&display=<?php echo $this->_var['pager']['display']; ?>&brand=<?php echo $this->_var['brand_id']; ?>&price_min=<?php echo $this->_var['price_min']; ?>&price_max=<?php echo $this->_var['price_max']; ?>&filter_attr=<?php echo $this->_var['filter_attr']; ?>&page=<?php echo $this->_var['pager']['page']; ?>&sort=shop_price&order=<?php if ($this->_var['pager']['sort'] == 'shop_price' && $this->_var['pager']['order'] == 'ASC'): ?>DESC<?php else: ?>ASC<?php endif; ?>#goods_list"><img src="themes/hd/images/shop_price_<?php if ($this->_var['pager']['sort'] == 'shop_price'): ?><?php echo $this->_var['pager']['order']; ?><?php else: ?>default<?php endif; ?>.jpg" alt="<?php echo $this->_var['lang']['sort']['shop_price']; ?>"></a>
  <a href="<?php echo $this->_var['script_name']; ?>.php?category=<?php echo $this->_var['category']; ?>&display=<?php echo $this->_var['pager']['display']; ?>&brand=<?php echo $this->_var['brand_id']; ?>&price_min=<?php echo $this->_var['price_min']; ?>&price_max=<?php echo $this->_var['price_max']; ?>&filter_attr=<?php echo $this->_var['filter_attr']; ?>&page=<?php echo $this->_var['pager']['page']; ?>&sort=salenum&order=<?php if ($this->_var['pager']['sort'] == 'salenum' && $this->_var['pager']['order'] == 'DESC'): ?>ASC<?php else: ?>DESC<?php endif; ?>#goods_list"><img src="themes/hd/images/salenum_<?php if ($this->_var['pager']['sort'] == 'salenum'): ?><?php echo $this->_var['pager']['order']; ?><?php else: ?>default<?php endif; ?>.jpg" alt="<?php echo $this->_var['lang']['sort']['last_update']; ?>"></a>

  <input type="hidden" name="category" value="<?php echo $this->_var['category']; ?>" />
  <input type="hidden" name="display" value="<?php echo $this->_var['pager']['display']; ?>" id="display" />
  <input type="hidden" name="brand" value="<?php echo $this->_var['brand_id']; ?>" />
  <input type="hidden" name="price_min" value="<?php echo $this->_var['price_min']; ?>" />
  <input type="hidden" name="price_max" value="<?php echo $this->_var['price_max']; ?>" />
  <input type="hidden" name="filter_attr" value="<?php echo $this->_var['filter_attr']; ?>" />
  <input type="hidden" name="page" value="<?php echo $this->_var['pager']['page']; ?>" />
  <input type="hidden" name="sort" value="<?php echo $this->_var['pager']['sort']; ?>" />
  <input type="hidden" name="order" value="<?php echo $this->_var['pager']['order']; ?>" />
				</form>
				</span>
				<span class="paixu_page">
  总计 <b><?php echo $this->_var['pager']['record_count']; ?></b>  个记录&nbsp;&nbsp;
     <?php if ($this->_var['pager']['page_first']): ?><a href="<?php echo $this->_var['pager']['page_first']; ?>"><?php echo $this->_var['lang']['page_first']; ?></a><?php endif; ?>
    <?php if ($this->_var['pager']['page_prev']): ?><a href="<?php echo $this->_var['pager']['page_prev']; ?>"><?php echo $this->_var['lang']['page_prev']; ?></a><?php else: ?><a href="javascript:void(0);"><?php echo $this->_var['lang']['page_prev']; ?></a><?php endif; ?>
	       
                        <?php if ($this->_var['pager']['page_count'] != 1): ?>
                            <?php $_from = $this->_var['pager']['page_number']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
                              <?php if ($this->_var['pager']['page'] == $this->_var['key']): ?>
                              <a class="page00"><?php echo $this->_var['key']; ?></a>
                              <?php else: ?>
                              <a href="<?php echo $this->_var['item']; ?>"><?php echo $this->_var['key']; ?></a>
                              <?php endif; ?>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            <?php endif; ?>
							   
            <?php if ($this->_var['pager']['page_next']): ?><a href="<?php echo $this->_var['pager']['page_next']; ?>"><?php echo $this->_var['lang']['page_next']; ?></a><?php else: ?><a href="javascript:void(0);"><?php echo $this->_var['lang']['page_next']; ?></a><?php endif; ?>
<?php if ($this->_var['pager']['page_last']): ?><a href="<?php echo $this->_var['pager']['page_last']; ?>"><?php echo $this->_var['lang']['page_last']; ?></a><?php endif; ?>
			</div>
			
				 <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
    <?php if ($this->_var['goods']['goods_id']): ?>	  	
				<div class="gallery">
				<div class="product">
					<ul>
                    <?php if ($this->_var['goods']['pics']): ?>
					<?php $_from = $this->_var['goods']['pics']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'pic');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['pic']):
        $this->_foreach['no']['iteration']++;
?>
						<li class="pro_imgli" style='position:relative'><a  href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" target="_blank" ><img src="<?php echo $this->_var['pic']['thumb_url']; ?>" alt="<?php echo $this->_var['pic']['goods_name']; ?>"  border="0" /></a></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    <?php else: ?>
                    <li class="pro_imgli" style='position:relative'><a  href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" target="_blank" ><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>" alt="<?php echo $this->_var['goods']['goods_name']; ?>" border="0" /></a></li>
                    <?php endif; ?>
					</ul>
				</div>
				<div class="hd">
					<ul>
                    <?php if ($this->_var['goods']['pics']): ?>
				 <?php $_from = $this->_var['goods']['pics']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'pic');$this->_foreach['no'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['no']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['pic']):
        $this->_foreach['no']['iteration']++;
?>		
				<li><a href="javascript:void(0);"><img src="<?php echo $this->_var['pic']['thumb_url']; ?>" width="42" border="0" title="<?php echo $this->_var['pic']['goods_name']; ?>" /></a></li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php else: ?>
                   <li><a href="javascript:void(0);"><img src="<?php echo $this->_var['goods']['goods_thumb']; ?>" alt="<?php echo $this->_var['goods']['goods_name']; ?>" width="42" border="0" /></a></li>
                    <?php endif; ?>
			</ul>
				</div>
				<div class="product_name"><a target="_blank" href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>"><?php echo $this->_var['goods']['goods_name']; ?></a></div>
				<div class="product_money">
					<span class="markmoney"><?php echo $this->_var['lang']['market_price']; ?><del><?php echo $this->_var['goods']['market_price']; ?></del></span>
					<span class="thmoney"><?php if ($this->_var['goods']['promote_price'] != ""): ?><?php echo $this->_var['lang']['promote_price']; ?><?php echo $this->_var['goods']['promote_price']; ?><?php else: ?><?php echo $this->_var['lang']['shop_price']; ?><?php echo $this->_var['goods']['shop_price']; ?><?php endif; ?></span>
				 </div>
			</div>
			    <?php endif; ?>
     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>    
 											
			  <?php echo $this->fetch('library/pages.lbi'); ?>			

            
		</div>
	</div>
</div>
<script type="text/javascript">
//jQuery(".gallery").slide({mainCell:".product ul",  delayTime:0, defaultIndex:4});
$(function(){
  $(".gallery .product").each(function(){
  	//$(this).find(".pro_imgli").eq(0).css("display","block");
  	$(this).find(".pro_imgli").eq(0).css("display","block").siblings().css("display","none");
  });
  $(".gallery .hd").each(function(index,obj){
  	$(this).find("li").eq(0).addClass("on");
  	$(this).find("li img").each(function(jdex,item){
  		$(item).hoverDelay({
  	  	 hoverDuring:200,
  	  	 hoverEvent:function(){
           $(".gallery .product").eq(index).find(".pro_imgli").css("display","none");
  	  	   $(".gallery .product").eq(index).find(".pro_imgli").eq(jdex).css("display","block");
  	  	   $(item).parent().parent().addClass("on").siblings().removeClass("on");
         }
  	  });
  	});
  });
});
</script>	
<?php echo $this->fetch('library/page_footer.lbi'); ?>
</body>
</html>
