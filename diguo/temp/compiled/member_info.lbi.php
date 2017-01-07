<span id="append_parent"></span>
<?php if ($this->_var['user_info']): ?>
<?php echo $this->_var['lang']['hello']; ?>，<font><?php echo $this->_var['user_info']['username']; ?></font>, <?php echo $this->_var['lang']['welcome_return']; ?>！<a href="user.php"><?php echo $this->_var['lang']['user_center']; ?></a><a href="user.php?act=logout"><?php echo $this->_var['lang']['user_logout']; ?></a>
<?php else: ?>   
<li id="append_parent">您好，<?php echo $this->_var['lang']['welcome']; ?>！</li>
<li><a href="user.php?act=login"  style="color:#c70a28">登录</a></li>
<li><a href="user.php?act=register" >注册</a></li>
<?php endif; ?>