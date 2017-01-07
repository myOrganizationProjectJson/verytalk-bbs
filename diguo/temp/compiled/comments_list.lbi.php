

<div class="New_goodCONtitle" id="userConmentList" name="userConmentList">商品评论</div>
	<ul class="New_goodCONPLlist">
     <?php if ($this->_var['comments']): ?>
       <?php $_from = $this->_var['comments']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'comment');if (count($_from)):
    foreach ($_from AS $this->_var['comment']):
?>
		       			<li>
                    <div class="New_goodPLTime New_goodPLNR_star">
                                                                                                                    </div>
			<div class="New_goodPLNR_top">
				<div class="New_goodPLNR"><?php echo $this->_var['comment']['content']; ?></div>
				<div class="New_goodPLuserName"><?php if ($this->_var['comment']['username']): ?><?php echo htmlspecialchars($this->_var['comment']['username']); ?><?php else: ?><?php echo $this->_var['lang']['anonymous']; ?><?php endif; ?></div>
                <?php if ($this->_var['comment']['re_content']): ?>
        <p><font class="f1"><?php echo $this->_var['lang']['admin_username']; ?></font><?php echo $this->_var['comment']['re_content']; ?></p>
				<?php endif; ?>
			</div>
              <div class="New_goodPLTime New_goodPLNR_star">
                                
                  <span style="float:left; color:#7b7b7b"><strong>评分：</strong></span>
                                   <img src="themes/hd/images/stars<?php echo $this->_var['comment']['rank']; ?>.gif" alt="<?php echo $this->_var['comment']['comment_rank']; ?>" />
                                 </div>
			<div class="New_goodPLTime">[<?php echo $this->_var['comment']['add_time']; ?>]</div>
			<div  class="New_goodPLNR_top" style=" color:#7b7b7b; margin-left:5px;">
						   </div>
		</li>
       		  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        <?php else: ?>
        <li style="width:97%"><?php echo $this->_var['lang']['no_comments']; ?></li>
        <?php endif; ?>	
       	 </ul>

            
       
   
        <form name="selectPageForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
        <?php if ($this->_var['pager']['styleid'] == 0): ?> 
        <div class="New_goodPage">
          <span class="f_l f6" style="margin-right:10px; padding:5px 10px;">共 <b><?php echo $this->_var['pager']['record_count']; ?></b> 条评论</span>
        <a class="prev" href="<?php echo $this->_var['pager']['page_prev']; ?>"><?php echo $this->_var['lang']['page_prev']; ?></a>
        <a class="next" href="<?php echo $this->_var['pager']['page_next']; ?>"><?php echo $this->_var['lang']['page_next']; ?></a> 
            <?php $_from = $this->_var['pager']['search']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
            <input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['item']; ?>" />
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </div>
        <?php else: ?>

        
         <div id="pager" class="pagebar">
          <span class="f_l f6" style="margin-right:10px;"><?php echo $this->_var['lang']['total']; ?> <b><?php echo $this->_var['pager']['record_count']; ?></b> <?php echo $this->_var['lang']['user_comment_num']; ?></span>
          <?php if ($this->_var['pager']['page_first']): ?><a href="<?php echo $this->_var['pager']['page_first']; ?>">1 ...</a><?php endif; ?>
          <?php if ($this->_var['pager']['page_prev']): ?><a class="prev" href="<?php echo $this->_var['pager']['page_prev']; ?>"><?php echo $this->_var['lang']['page_prev']; ?></a><?php endif; ?>
          <?php $_from = $this->_var['pager']['page_number']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
                <?php if ($this->_var['pager']['page'] == $this->_var['key']): ?>
                <span class="page_now"><?php echo $this->_var['key']; ?></span>
                <?php else: ?>
                <a href="<?php echo $this->_var['item']; ?>">[<?php echo $this->_var['key']; ?>]</a>
                <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

          <?php if ($this->_var['pager']['page_next']): ?><a class="next" href="<?php echo $this->_var['pager']['page_next']; ?>"><?php echo $this->_var['lang']['page_next']; ?></a><?php endif; ?>
          <?php if ($this->_var['pager']['page_last']): ?><a class="last" href="<?php echo $this->_var['pager']['page_last']; ?>">...<?php echo $this->_var['pager']['page_count']; ?></a><?php endif; ?>
          <?php if ($this->_var['pager']['page_kbd']): ?>
            <?php $_from = $this->_var['pager']['search']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
            <input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['item']; ?>" />
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            <kbd style="float:left; margin-left:8px; position:relative; bottom:3px;"><input type="text" name="page" onkeydown="if(event.keyCode==13)selectPage(this)" size="3" class="B_blue" /></kbd>
            <?php endif; ?>
        </div>
        

        <?php endif; ?>
        </form>
        <script type="Text/Javascript" language="JavaScript">
        <!--
        
        function selectPage(sel)
        {
          sel.form.submit();
        }
        
        //-->
        </script>
      </div>
      
      <div class="blank5"></div>
      
      <div class="commentsList">
      <form action="javascript:;" onsubmit="submitComment(this)" method="post" name="commentForm" id="commentForm">
       <table width="710" border="0" cellspacing="5" cellpadding="0">
        <tr>
          <td width="64" align="right"><?php echo $this->_var['lang']['username']; ?>：</td>
          <td width="631"<?php if (! $this->_var['enabled_captcha']): ?><?php endif; ?>><?php if ($_SESSION['user_name']): ?><?php echo $_SESSION['user_name']; ?><?php else: ?><?php echo $this->_var['lang']['anonymous']; ?><?php endif; ?></td>
        </tr>
        <tr>
          <td align="right">E-mail：</td>
          <td>
          <input type="text" name="email" id="email"  maxlength="100" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" class="inputBorder"/>
          </td>
        </tr>
        <tr>
          <td align="right"><?php echo $this->_var['lang']['comment_rank']; ?>：</td>
          <td>
          <input name="comment_rank" type="radio" value="5" checked="checked" id="comment_rank5" /> <img src="themes/hd/images/stars5.gif" />
          <input name="comment_rank" type="radio" value="4" id="comment_rank4" /> <img src="themes/hd/images/stars4.gif" />
          
         
          <input name="comment_rank" type="radio" value="3" id="comment_rank3" /> <img src="themes/hd/images/stars3.gif" />
           <input name="comment_rank" type="radio" value="2" id="comment_rank2" /> <img src="themes/hd/images/stars2.gif" />
          <input name="comment_rank" type="radio" value="1" id="comment_rank1" /> <img src="themes/hd/images/stars1.gif" />
          </td>
        </tr>
        <tr>
          <td align="right" valign="top"><?php echo $this->_var['lang']['comment_content']; ?>：</td>
          <td>
          <textarea name="content" class="inputBorder" style="height:50px; width:620px;"></textarea>
          <input type="hidden" name="cmt_type" value="<?php echo $this->_var['comment_type']; ?>" />
          <input type="hidden" name="id" value="<?php echo $this->_var['id']; ?>" />
          </td>
        </tr>
        <tr>
        <td align="right" valign="top"></td>
          <td>
          <?php if ($this->_var['enabled_captcha']): ?>
          <div style="padding-left:15px; text-align:left; float:left;">
          <?php echo $this->_var['lang']['comment_captcha']; ?>：<input type="text" name="captcha"  class="inputBorder" style="width:50px; margin-left:5px;"/>
          <img src="captcha.php?<?php echo $this->_var['rand']; ?>" alt="captcha" onClick="this.src='captcha.php?'+Math.random()" class="captcha">
          </div>
          <?php endif; ?>
          <input name="" type="submit"  value="" class="f_r" style="border:none; background:url(themes/hd/images/commentsBnt.gif); width:61px; height:21px; margin-right:8px;">
          </td>
        </tr>
      </table>
      </form>
  
<script type="text/javascript">
//<![CDATA[
<?php $_from = $this->_var['lang']['cmt_lang']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

/**
 * 提交评论信息
*/
function submitComment(frm)
{
  var cmt = new Object;

  //cmt.username        = frm.elements['username'].value;
  cmt.email           = frm.elements['email'].value;
  cmt.content         = frm.elements['content'].value;
  cmt.type            = frm.elements['cmt_type'].value;
  cmt.id              = frm.elements['id'].value;
  cmt.enabled_captcha = frm.elements['enabled_captcha'] ? frm.elements['enabled_captcha'].value : '0';
  cmt.captcha         = frm.elements['captcha'] ? frm.elements['captcha'].value : '';
  cmt.rank            = 0;

  for (i = 0; i < frm.elements['comment_rank'].length; i++)
  {
    if (frm.elements['comment_rank'][i].checked)
    {
       cmt.rank = frm.elements['comment_rank'][i].value;
     }
  }

//  if (cmt.username.length == 0)
//  {
//     alert(cmt_empty_username);
//     return false;
//  }

  if (cmt.email.length > 0)
  {
     if (!(Utils.isEmail(cmt.email)))
     {
        alert(cmt_error_email);
        return false;
      }
   }
   else
   {
        alert(cmt_empty_email);
        return false;
   }

   if (cmt.content.length == 0)
   {
      alert(cmt_empty_content);
      return false;
   }

   if (cmt.enabled_captcha > 0 && cmt.captcha.length == 0 )
   {
      alert(captcha_not_null);
      return false;
   }

   Ajax.call('comment.php', 'cmt=' + obj2str(cmt), commentResponse, 'POST', 'JSON');
   return false;
}

/**
 * 处理提交评论的反馈信息
*/
  function commentResponse(result)
  {
    if (result.message)
    {
      alert(result.message);
    }

    if (result.error == 0)
    {
      var layer = document.getElementById('ECS_COMMENT');

      if (layer)
      {
        layer.innerHTML = result.content;
      }
    }
  }

//]]>
</script>