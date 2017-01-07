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
<link rel="alternate" type="application/rss+xml" title="RSS|<?php echo $this->_var['page_title']; ?>" href="<?php echo $this->_var['feed_url']; ?>" />

<?php echo $this->smarty_insert_scripts(array('files'=>'common.js,index.js')); ?>
<script type="text/javascript" src="themes/hd/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="themes/hd/js/jquery.focus.js"></script>
<style type="text/css">
.seven{
	width: 100%;
	height:450px;
	background: url(themes/hd/images/taishanyading.jpg) no-repeat top center;
}
</style>
</head>
<body>
<a href='#' target='_blank'><div class='seven'></div></a>
<?php echo $this->fetch('library/page_header.lbi'); ?>
<div style='width:100%;overflow:hidden'>
	<div class='banner'>
	
	
<div id="banner">
    <div id="nivo-wrapper">
        <div class="nivoSlider" >
    <?php $this->assign('playerdb', get_flash_xml());?>
      <?php $_from = $this->_var['playerdb']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');$this->_foreach['fnum'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fnum']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
        $this->_foreach['fnum']['iteration']++;
?><a href="<?php echo $this->_var['item']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['item']['src']; ?>" alt="<?php echo $this->_var['item']['text']; ?>" title="<?php echo $this->_var['item']['text']; ?>" /></a><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
       </div>
    </div>
</div>
<script type="text/javascript" src="themes/hd/js/ss.js"></script>
<script type="text/javascript">
    $(function(){
        $('.nivoSlider').nivoSlider({
            effect:'fade'
        });
    });
</script>
	
	</div>
</div>
<div class='wrap' class='hide'>
     
     <div class='brand_collect'>
	             <div class="fl abrand"><?php 
$k = array (
  'name' => 'ads',
  'id' => '1',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
                 <div class="fl abrand"><?php 
$k = array (
  'name' => 'ads',
  'id' => '2',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
                 <div class="fl abrand"><?php 
$k = array (
  'name' => 'ads',
  'id' => '3',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
                 <div class="fl abrand"><?php 
$k = array (
  'name' => 'ads',
  'id' => '4',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
                 <div class="fl abrand"><?php 
$k = array (
  'name' => 'ads',
  'id' => '5',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
                 <div class="fl"><?php 
$k = array (
  'name' => 'ads',
  'id' => '6',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
	    	 </div>
     
  
    
	<div class='floor' id='floor1'>
		  <div class='nav_bar'>
		  	    <a href="#" title="棉服" target="_blank"></a>
		  	    <a href="#" title="毛衣针织衫" target="_blank"></a>
		  	    <a href="#" title="外套" target="_blank"></a>
		  	    <a href="#" title="裙装" target="_blank"></a>
		  	    <a href="#" title="裤装" target="_blank"></a>
		  	    <a href="#" title="鞋子" target="_blank"></a>
		  </div>
		  <div class='R fl'>
			  <h1>
			  	   <div class='keyWord fr'>
                   <?php echo $this->fetch('library/icat1.lbi'); ?>
			  	   </div>
			  </h1>
			  <div class='sad1 fl'>
			  	    <div class="longbox fl">
					    <ul class="longbox_content" > 
                          <li><?php 
$k = array (
  'name' => 'ads',
  'id' => '7',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></li>
					      <li><?php 
$k = array (
  'name' => 'ads',
  'id' => '8',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></li>
					      <li><?php 
$k = array (
  'name' => 'ads',
  'id' => '9',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></li>
					    </ul>
					</div>
			  </div>
			  
                  <div class='sad2 fr'>
                    	<?php 
$k = array (
  'name' => 'ads',
  'id' => '10',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
             	 	</div>
                    <div class='sad2 fr'>
                    	<?php 
$k = array (
  'name' => 'ads',
  'id' => '11',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
             	 	</div>

		  </div>
		  <ul>
        
<?php $this->assign('cat_goods',$this->_var['cat_goods_1']); ?><?php $this->assign('goods_cat',$this->_var['goods_cat_1']); ?><?php echo $this->fetch('library/cat_goods.lbi'); ?>
     
              			 
		  </ul>
	</div>
	
	 
	<div class='floor' id='floor2'>
		 <div class='nav_bar'>
		  	    <a href="#" title="外套" target="_blank"></a>
		  	    <a href="#" title="针织衫/毛衣" target="_blank"></a>
		  	    <a href="#" title="长袖T恤" target="_blank"></a>
		  	    <a href="#" title="裙装" target="_blank"></a>
		  	    <a href="#" title="雪纺衫" target="_blank"></a>
		  	    <a href="#" title="休闲裤" target="_blank"></a>
		  </div>
		  <div class='R fl'>
			  <h1>
			  	   <div class='keyWord fr'>
			  	   	    <?php echo $this->fetch('library/icat2.lbi'); ?>
			  	   </div>
			  </h1>
			  <div class='sad1 fl'>
              
                		 <div class="flex-container" id="sulv_banner">
				       <div class="flexslider" id="flexslider2">
				             <ul class="slides">
				              
				         <li><?php 
$k = array (
  'name' => 'ads',
  'id' => '12',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></li>
					      <li><?php 
$k = array (
  'name' => 'ads',
  'id' => '13',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></li>
			 					                                 
				            </ul>
				      </div>
					</div>
                
                
                </div>
			  
               			   <div class='sad2 fr'>
<?php 
$k = array (
  'name' => 'ads',
  'id' => '15',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
               </div>
                			   <div class='sad2 fr'>
              <?php 
$k = array (
  'name' => 'ads',
  'id' => '16',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
               </div>
                		  </div>
		  <ul>
          
          				        
<?php $this->assign('cat_goods',$this->_var['cat_goods_1']); ?><?php $this->assign('goods_cat',$this->_var['goods_cat_1']); ?><?php echo $this->fetch('library/cat_goods.lbi'); ?>
     
		  </ul>
	</div>

    
	<div class='floor' id='floor3'>
		 <div class='nav_bar'>
		  	    <a href="#" title="毛衣" target="_blank"></a>
		  	    <a href="#" title="外套" target="_blank"></a>
		  	    <a href="#" title="棉衣" target="_blank"></a>
		  	    <a href="#" title="长袖T恤" target="_blank"></a>
		  	    <a href="#" title="牛仔裤" target="_blank"></a>
		  	    <a href="#" title="男鞋" target="_blank"></a>
		  </div>
		  <div class='R fl'>
			  <h1>
			  	   <div class='keyWord fr'>
			  		<?php echo $this->fetch('library/icat3.lbi'); ?>
			  	   </div>
			  </h1>
			  <div class='sad1 fl'>
			  	
				<div class="focus fl" id='amh_banner'>
				          <ul>
				            <li><?php 
$k = array (
  'name' => 'ads',
  'id' => '17',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></li>
					        <li><?php 
$k = array (
  'name' => 'ads',
  'id' => '18',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></li>
					        <li><?php 
$k = array (
  'name' => 'ads',
  'id' => '19',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></li> 
					     </ul>
				</div>
				
			  </div>
			 
             	    <div class='sad3 fr'><?php 
$k = array (
  'name' => 'ads',
  'id' => '20',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
                    <div class='sad3 fr'><?php 
$k = array (
  'name' => 'ads',
  'id' => '21',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
                    <div class='sad3 fr'><?php 
$k = array (
  'name' => 'ads',
  'id' => '37',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>

		  </div>

		  <ul>
		 
<?php $this->assign('cat_goods',$this->_var['cat_goods_16']); ?><?php $this->assign('goods_cat',$this->_var['goods_cat_16']); ?><?php echo $this->fetch('library/cat_goods.lbi'); ?>
     
		  </ul>
   
	</div>
	
	
    
	<div class='floor' id='floor4'>
		 <div class='nav_bar'>
		  	    <a href="#" title="毛衣" target="_blank"></a>
		  	    <a href="#" title="外套" target="_blank"></a>
		  	    <a href="#" title="棉衣" target="_blank"></a>
		  	    <a href="#" title="套装" target="_blank"></a>
		  	    <a href="#" title="裤装" target="_blank"></a>
		  	    <a href="#" title="配饰" target="_blank"></a>
		  </div>
		
		 
		  <div class='R fl'>
			  <h1>
			  	   <div class='keyWord fr'>
			  	   	<?php echo $this->fetch('library/icat4.lbi'); ?>
			  	   </div>
			  </h1>
              
                
           
			  <div class='sad1 fl'> <?php 
$k = array (
  'name' => 'ads',
  'id' => '22',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?> </div>
            
              				  <div class='sad2 fr'> <?php 
$k = array (
  'name' => 'ads',
  'id' => '25',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?> </div>
			  				  <div class='sad2 fr'> <?php 
$k = array (
  'name' => 'ads',
  'id' => '26',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?> </div>
			  		  </div>
		  <ul>
      
<?php $this->assign('cat_goods',$this->_var['cat_goods_18']); ?><?php $this->assign('goods_cat',$this->_var['goods_cat_18']); ?><?php echo $this->fetch('library/cat_goods.lbi'); ?>
    
             
		  
		  </ul>
	</div>
	<div class='tonglan'><?php 
$k = array (
  'name' => 'ads',
  'id' => '48',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>

	<div class='floor' id='floor5'>
		 <div class='nav_bar'>
		  	    <a href="#" title="外套" target="_blank"></a>
		  	    <a href="#" title="针织衫/毛衣" target="_blank"></a>
		  	    <a href="#" title="西装" target="_blank"></a>
		  	    <a href="#" title="裙装" target="_blank"></a>
		  	    <a href="#" title="长袖T恤" target="_blank"></a>
		  	    <a href="#" title="长裤" target="_blank"></a>
		  </div>
		 
		  <div class='R fl'>
			  <h1>
			  	   <div class='keyWord fr'>
			  	   	     <?php echo $this->fetch('library/icat5.lbi'); ?>
			  	   </div>
			  </h1>
              
              
			  <div class='sad1 fl'> <?php 
$k = array (
  'name' => 'ads',
  'id' => '27',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?> </div>
			  
                				  <div class='sad2 fr'> <?php 
$k = array (
  'name' => 'ads',
  'id' => '30',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?> </div>
			  				  <div class='sad2 fr'> <?php 
$k = array (
  'name' => 'ads',
  'id' => '31',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?> </div>
			                
		  </div>
		  <ul>
          
 
<?php $this->assign('cat_goods',$this->_var['cat_goods_1']); ?><?php $this->assign('goods_cat',$this->_var['goods_cat_1']); ?><?php echo $this->fetch('library/cat_goods.lbi'); ?>
    
		  </ul>
		  
	</div>
	

	<div class='floor' id='floor6'>
		 <div class='nav_bar'>
		  	    <a href="#" title="外套" target="_blank"></a>
		  	    <a href="#" title="毛衣&针织" target="_blank"></a>
		  	    <a href="#" title="长袖T恤" target="_blank"></a>
		  	    <a href="#" title="裙装" target="_blank"></a>
		  	    <a href="#" title="长裤" target="_blank"></a>
		  	    <a href="#" title="配饰" target="_blank"></a>
		  </div>
 
		  <div class='R fl'>
			  <h1>
			  	   <div class='keyWord fr'>
			  	   	   <?php echo $this->fetch('library/icat6.lbi'); ?>
			  	   </div>
			  </h1>
			  <div class='sad1 fl'>
                
			  	<div class="flex-container" id="sulv_banner">
				       <div class="flexslider" id="flexslider2">
				             <ul class="slides">
				              <li> <?php 
$k = array (
  'name' => 'ads',
  'id' => '32',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?> </li>
			 					                                 
				            </ul>
				      </div>
				</div>
				
			  </div>
			 
                           <div class='sad3 fr'><?php 
$k = array (
  'name' => 'ads',
  'id' => '35',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
                           <div class='sad3 fr'><?php 
$k = array (
  'name' => 'ads',
  'id' => '36',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
                           <div class='sad3 fr'><?php 
$k = array (
  'name' => 'ads',
  'id' => '38',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
             			 
		  </div>
		  <ul>
	
<?php $this->assign('cat_goods',$this->_var['cat_goods_1']); ?><?php $this->assign('goods_cat',$this->_var['goods_cat_1']); ?><?php echo $this->fetch('library/cat_goods.lbi'); ?>
    
		     
		  </ul>
	</div>
   
    
    <div class='sale_floor fl'>
		<div class='news_section'>
			  <h1>新闻中心</h1>
              <div class='news_box news_box1 fl'><?php 
$k = array (
  'name' => 'ads',
  'id' => '39',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div> 
              
              <div class='news_box news_box2 fl'><?php 
$k = array (
  'name' => 'ads',
  'id' => '40',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div> 
                <div class='news_box news_box3 fl'>
              <?php 
$k = array (
  'name' => 'ads',
  'id' => '41',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
              </div> 
               <div class='news_box news_box3 fl'>
              	<?php 
$k = array (
  'name' => 'ads',
  'id' => '42',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
              </div> 
              		</div>
		<div class='coupon_section'>
			   <h1>精彩活动</h1>
                            <div class='coupon_box coupon_box1 fl'>
                  	<?php 
$k = array (
  'name' => 'ads',
  'id' => '43',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
              </div> 
                            <div class='coupon_box coupon_box1 fl'>
                  	<?php 
$k = array (
  'name' => 'ads',
  'id' => '44',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
              </div> 
                            <div class='coupon_box coupon_box1 fl'>
                  	<?php 
$k = array (
  'name' => 'ads',
  'id' => '45',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>
              </div> 
                           
              <div class='coupon_box coupon_box2 fl'><?php 
$k = array (
  'name' => 'ads',
  'id' => '46',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>  
              <div class='coupon_box coupon_box3 fl'><?php 
$k = array (
  'name' => 'ads',
  'id' => '47',
  'num' => '1',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>  
		</div>
    </div>

   
</div>
      
<div style="display:none"><a href="http://www.yunmoban.cn">ecshop模板</a></div>
<?php echo $this->fetch('library/page_footer.lbi'); ?>
</body>
<script type="text/javascript">
$('#sys .s1,#sys #a1').hover(function(){
	$('#sys #a1,#sys #a2,#sys #a3').hide();
	$('#sys #a1').show();
},function(){
	$('#sys #a1,#sys #a2,#sys #a3').hide();
})
$('#sys .s2,#sys #a2').hover(function(){
	$('#sys #a1,#sys #a2,#sys #a3').hide();
	$('#sys #a2').show();
},function(){
	$('#sys #a1,#sys #a2,#sys #a3').hide();
})
$('#sys .s3,#sys #a3').hover(function(){
	$('#sys #a1,#sys #a2,#sys #a3').hide();
	$('#sys #a3').show();
},function(){
	$('#sys #a1,#sys #a2,#sys #a3').hide();
})
$('body').click(function(){
	$('#sys #a1,#sys #a2,#sys #a3').hide();
})
</script>
<script type="text/javascript">
$(function(){
var i = 0;
var top = 0;
var hh = $('.longbox').height();
var n = $('.longbox_content li').size();
longbox_init();
$('.longbox').hover(function(){
      clearInterval(aa);
},function(){
       aa = setInterval(s,4000);
})
$('.longbox_nav li').mouseover(function(){
  clearInterval(aa);
      var index = $(this).index();
      i=index;
      top=hh*i;
      $(this).addClass('current').siblings().removeClass('current');
      $(this).parents('.longbox').children('.longbox_content').stop(false,true).animate({'margin-top':-top},300);
})
var s = function(){
  // $('.longbox_nav li').unbind('hover', top);
  top = i*hh;
  $('.longbox_nav li').eq(i).addClass('current').siblings().removeClass('current');
  $('.longbox_content').stop(false,true).animate({'margin-top':-top},300);
  i++;
 
  if(i==n)i=0;
}
function longbox_init(){
$('<ul class="longbox_nav"></ul>').appendTo('.longbox')
for(var j=0;j<n;j++){
  ii = j+1
  $('<li>'+ii+'</li>').appendTo('.longbox_nav');
}
$('.longbox_nav li:first').addClass('current');
}
//手表
if($('.longbox_content li').size()>1){
var aa = setInterval(s,4000);
}else{
$('.longbox_nav').hide();
clearInterval(s);
}
})
</script>
<script type="text/javascript" src="themes/hd/js/jquery.flexslider-min.js"></script>
<script type="text/javascript">
    $(window).load(function() {
        $('.flexslider').flexslider();
    });
</script>
<script type="text/javascript">
$(function(){
  $('.seven').delay(3000)
	 .animate({'height':90},1000,
      	function(){
      	  $('.seven').css({'background':'url(themes/hd/images/1920.jpg) no-repeat top center'})
  });
$('.store_nav li').each(function(){
        var index = $(this).index();
		var that = $(this);
		that.hoverDelay({
		outDuring: 120,
		hoverDuring:120,
		
		hoverEvent:function(){
			$(this).addClass('current').siblings().removeClass('current');
	        $('.store_content>li').eq(index).show().siblings().hide();
	   }
	});	
});
$('dt a').click(function(){
	$(this).addClass('current').siblings().removeClass('current');
	var step = $(this).index();
	$('.coupon_set').animate({'margin-top':-165*step},200);
})
})
</script>
</html>