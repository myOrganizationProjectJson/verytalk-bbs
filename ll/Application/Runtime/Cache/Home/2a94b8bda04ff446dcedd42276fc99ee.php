<?php if (!defined('THINK_PATH')) exit();?> <html>
 <div>
     <span>新增随机回复入口</span>
	 <form action="<?php echo U('index/start_post');?>" method="post">
		 <p><input type="radio" value="rand" name="type" checked="checked">随机</p> 
		  <span style="margin:0 31">member   </span><input type="text" name="member"/></br></br></br>
		 <p><input type="radio" value="no_rand" name="type">不随机</p> 
		  <span style="margin:0 14">随机文章type</span> <select name='btype'>
	                    <option value="">不选择</option>
						<option value="1">随机回复</option>
						<option value="2">随机帖子</option>
						<option value="3">情感专题</option>
						<option value="4">幽默笑话</option>
						<option value="5">长篇别论</option>
						<option value="6">其他</option>
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
 </html>