<?php if (!defined('THINK_PATH')) exit();?> <html>
 <div>
     <span>新增随机文章</span></br></br>
	 <form action="<?php echo U('admin/dotype');?>" method="post">
              <span style="margin:0 45">将type</span> 
                    <select name='btype'>
						<option value="1">随机回复</option>
						<option value="2">随机帖子</option>
						<option value="3">情感专题</option>
						<option value="4">幽默笑话</option>
						<option value="5">长篇别论</option>
						<option value="6">其他</option>
					</select></br></br>
					<span style="margin:0 29">转换为type</span> 
                    <select name='etype'>
						<option value="1">随机回复</option>
						<option value="2">随机帖子</option>
						<option value="3">情感专题</option>
						<option value="4">幽默笑话</option>
						<option value="5">长篇别论</option>
						<option value="6">其他</option>
					</select></br></br>
		 <input type="submit" value="确认"/>
	 </form>
 </div>
 </html>