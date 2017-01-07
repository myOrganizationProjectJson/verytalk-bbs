<script type="text/javascript">
var process_request = "<?php echo $this->_var['lang']['process_request']; ?>";
</script>
<!--[if IE 6]>
<div id="yellowtip" style="width: 100%; background-color: rgb(254, 246, 227); border-top-width: 1px; border-bottom-width: 1px; border-style: solid none; border-top-color: rgb(229, 205, 150); border-bottom-color: rgb(229, 205, 150); height: 33px; min-width: 920px; z-index: 99999; background-position: initial initial; background-repeat: initial initial;">
    <div style="margin:0 auto;width:900px;line-height:33px;text-align:center;color:#82654D;">温馨提示：尊敬的用户，现在检测到您正在使用IE6浏览器。为了确保您的购物安全和更好的用户体验，请<a class="se6_download" style="color:#1C79A1" href="http://chrome.360.cn/" target="_blank">下载360极速浏览器</a>或者升级更高版本的IE浏览器</div>
    <div id="yellowtipclose" style="display:none;"></div>
  </div>
<![endif]-->
<div class='toolbar'>
    <div class="w">
        <ul class="fl lh">
            <li>
                <a href="index.php"  style='color:#c80a28' target="_blank">返回首页</a>
            </li>
            <li>
                <a href="javascript:window.external.addFavorite()" id='AddFavorite'rel="nofollow">收藏本站</a>
            </li>
      <?php $_from = $this->_var['navigator_list']['top']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');$this->_foreach['nav_top_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav_top_list']['total'] > 0):
    foreach ($_from AS $this->_var['nav']):
        $this->_foreach['nav_top_list']['iteration']++;
?>
      <li><a href="<?php echo $this->_var['nav']['url']; ?>" <?php if ($this->_var['nav']['opennew'] == 1): ?> target="_blank" <?php endif; ?>><?php echo $this->_var['nav']['name']; ?></a></li>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <li>
                <a href="http://wpa.qq.com/msgrd?v=3&amp;uin=77898852&amp;site=qq&amp;menu=yes" target="_blank">在线客服</a>
            </li>
            <li><a href="user.php" target="_blank" >会员俱乐部</a></li>
        </ul>
        
        <ul class="fr">
            <li class="fore ld" id="phone_icon">
                <a href="mobile/" target="_blank">
                    <img src="themes/hd/images/phone.png" alt="手机客户端" class="fl" style="margin-right: 4px;">
                    <span>手机版</span>
                </a>
            </li>
            <li class="fore ld" id='wireless_icon' style='padding:0;margin-right:-8px;'>
                <span> 关注我们</span>
                <img src='themes/hd/images/weixin.png' alt='官方微信' id='weixin' class='fl' style='margin:7px;margin-right:9px;'>
            </li>
        </ul>
        <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
        <ul class="fr" id="ECS_MEMBERZONE">
   <font><?php 
$k = array (
  'name' => 'member_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?> </font>
        </ul>
        <span class="clr"></span>
    </div>
</div>

<div id="header">
    <div class="head clearfix">
        <div class="hd">
            <a title="韩都衣舍 没空去韩国？就来韩都衣舍！" href="index.php" id='logo'>
                韩都衣舍 没空去韩国？就来韩都衣舍！
            </a>
        </div>
        
        <div class='tab_search' >
			<form id="searchForm" name="searchForm" method="get" action="search.php" onSubmit="return checkSearchForm()" >
                <input  onkeydown="if (event.keycode==13) {formsubmit()}"   id='searchinput' class='searchinput'  onfocus="if (this.value =='针织衫'){this.value=''}"   title='search' size='10'   value="针织衫"  name="keywords" >
                <input class='searchaction'   onclick="javascript:formsubmit()"  name="searchbtn"  border=0 hspace=2 alt=search src="themes/hd/images/search_btn1.png"  type='image'>
            </form>    
        </div>
        <div class='keyW'>
            <em>热门搜索:</em>
     <?php $_from = $this->_var['searchkeywords']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'val');if (count($_from)):
    foreach ($_from AS $this->_var['val']):
?>
   <a href="search.php?keywords=<?php echo urlencode($this->_var['val']); ?>"><?php echo $this->_var['val']; ?></a>
   <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
        <span id="ECS_CARTINFO"><?php 
$k = array (
  'name' => 'cart_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></span>
        <div class='brandlist' >
              <a href="#" id='a1' target="_blank"></a>
              <a href="#" id='a2' target="_blank"></a>
              <a href="#" id='a3' target="_blank"></a>
              <a href="#" id='a4' target="_blank"></a>
              <a href="#" id='a5' target="_blank"></a>
              <a href="#" id='a6' target="_blank"></a>
              <a href="#" id='a7' target="_blank"><img src="themes/hd/images/jp.gif" style="position: absolute;right: 4px;top: 13px;"></a>
        </div>
    </div>
    <div class="navbg" id="navbg">
        <div class="navbg_r"></div>
        <div class="nav_w clearfix">
            <ul class="bd">
                <li class="rbg">
                    <a class="goodnews fl" href="./" target="_blank">首页</a>
                </li>
                <?php $_from = $this->_var['navigator_list']['middle']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');$this->_foreach['nav_middle_list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['nav_middle_list']['total'] > 0):
    foreach ($_from AS $this->_var['nav']):
        $this->_foreach['nav_middle_list']['iteration']++;
?> 
                <li class="discovery">
                    <a class="showdiscovery fl" href="<?php echo $this->_var['nav']['url']; ?>" <?php if ($this->_var['nav']['opennew'] == 1): ?>target="_blank" <?php endif; ?>  target="_blank">
                        <i class='arrow1'></i><?php echo $this->_var['nav']['name']; ?>
                    </a>
             <?php $this->assign('catchild', get_child_tree($GLOBALS['smarty']->_var['nav']['cid']));?>  
         <?php if ($this->_var['nav']['cid']): ?>  
                    <div class="sub_layer"<?php if (($this->_foreach['nav_middle_list']['iteration'] <= 1)): ?> id='w980'<?php endif; ?> class='clearfix'>
                        <div id="hot_cate">                            
                      <?php $_from = $this->_var['catchild']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>
                          <ul class='menu_category'><h2><a href="<?php echo $this->_var['child']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['child']['name']); ?>" target="_blank"><?php echo htmlspecialchars($this->_var['child']['name']); ?></a></h2>
                            <?php $_from = $this->_var['child']['cat_id']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'childer');$this->_foreach['childnum'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['childnum']['total'] > 0):
    foreach ($_from AS $this->_var['childer']):
        $this->_foreach['childnum']['iteration']++;
?>
                          <li><a href='<?php echo $this->_var['childer']['url']; ?>' title="<?php echo htmlspecialchars($this->_var['childer']['name']); ?>" target="_blank"><?php echo htmlspecialchars($this->_var['childer']['name']); ?></a></li>
                          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
                          </ul>	        
                         <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
       
                        <h1>热门活动</h1>
                        <ul class='newsList'>
                           <li><a href="#" target="_blank">热卖TOP榜单</a></li><li><a href="#" target="_blank">秒杀专区</a></li><li><a href="#" target="_blank">韩都衣舍秋装新品上新</a></li>                        </ul>
                        <h1>抢购活动</h1>
                        <h1><a href="<?php echo $this->_var['nav']['url']; ?>" title="<?php echo $this->_var['nav']['name']; ?>首页" target="_blank" class="entry">进入<?php echo $this->_var['nav']['name']; ?>首页&gt;&gt;</a></h1>
                        </div>
                  </div>
                   <?php endif; ?>
                </li>
              <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
 
            </ul>
        </div>
    </div>
</div>
<?php 
   require_once("themes/".$GLOBALS['_CFG']['template']."/diyfile.php");
   $this->assign('TemplatePath','themes/'.$GLOBALS['_CFG']['template']);
?>
<script type="text/javascript" src="<?php echo $this->_var['TemplatePath']; ?>/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->_var['TemplatePath']; ?>/js/jquery-migrate-1.1.0.js"></script>
<script type="text/javascript" src='<?php echo $this->_var['TemplatePath']; ?>/js/jquery.hoverdelay.js'></script>
 
<script type="text/javascript">
$('.discovery').hover(function(){
        $(this).find('.sub_layer').show();
},function(){
        $(this).find('.sub_layer').hide();
})
var codeL = $('.w').offset().left+20;
$('#weixin').hover(function(){
        $('#code').css({'right':codeL}).show();
},function(){
        $('#code').hide();
})
$("#AddFavorite").on("click", function () { 
  var sURL = "http://www.yunmoban.cn/"; 
  sTitle = "云模板"; 
  try {window.external.addFavorite(sURL, sTitle);} 
  catch (e) { 
    try {window.sidebar.addPanel(sTitle, sURL, "slice");} 
    catch (e) {alert("您可以尝试通过快捷键 Ctrl + D 加入到收藏夹~")} 
  } 
}); 
function formsubmit()
{
	document.searchForm.submit()
}
</script>