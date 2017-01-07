<?php if (!defined('THINK_PATH')) exit();?> <html>
 
 
  <?php if($login == 'yes'): ?><div>
	     <span>新增随机回复入口</span>
		 <form action="<?php echo U('index/start_post');?>" method="post">
			 <p><input type="radio" value="rand" name="type" checked="checked">随机</p> 
			  <span style="margin:0 31">member   </span><input type="text" name="member"/></br></br></br>
			 <p><input type="radio" value="no_rand" name="type">不随机</p> 
			  <span style="margin:0 14">随机文章type</span> 
			            <select name='btype'>
		                    <option value="">不选择</option>
	                        <?php if(is_array($SubjectType)): $i = 0; $__LIST__ = $SubjectType;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><option value="<?php echo ($vo["type_id"]); ?>"><?php echo ($vo["rename"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select></br></br></br>
			<span style="margin:0 10">author_post_id </span><input type="text" name="author_post_id"/></br></br>
			<span style="margin:0 49">fid   </span>         <input type="text" name="fid"/></br></br>
			<span style="margin:0 49">tid   </span>          <input type="text" name="tid" /></br></br>
			<span style="margin:0 35">subject   </span>      <input type="text" name="subject"/></br></br>
			<span style="margin:0 31">message   </span> <textarea name="message" clos="20" rows="5">
		    
			 </textarea></br></br>
			 <input type="submit" value="确认"/>
		 </form>
	 </div>
	 <?php else: ?>
	 <div>
      <span>新增随机回复入口登陆</span></br></br>
	  <form action="" method="post">
	  <span style="margin:0 30">username</span><input type="text" name="username"/></br></br></br>
	  <span style="margin:0 30">password</span><input type="password" name="password"/></br></br>
	  <input type="hidden" name="veryfy" value="veryfy"/>
	  <span style="padding:0 180"><input type="submit" value="登陆"/></span>
	  </form>
	 </div><?php endif; ?>
 </html>