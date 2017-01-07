 <?php
	$GLOBALS['smarty']->assign('child_cat',get_hot_cat_tree(16, 3));
?>
 <?php $_from = $this->_var['child_cat']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat1_0_32812500_1397790200');$this->_foreach['catspan1'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['catspan1']['total'] > 0):
    foreach ($_from AS $this->_var['cat1_0_32812500_1397790200']):
        $this->_foreach['catspan1']['iteration']++;
?> 
      <?php if ($this->_var['cat1_0_32812500_1397790200']['name']): ?>
		<a href="<?php echo $this->_var['cat1_0_32812500_1397790200']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['cat1_0_32812500_1397790200']['name']); ?>"><?php echo htmlspecialchars($this->_var['cat1_0_32812500_1397790200']['name']); ?></a>
		<?php endif; ?>  
       <?php $_from = $this->_var['cat1_0_32812500_1397790200']['child']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cat_child1_0_32812500_1397790200');$this->_foreach['catspan2'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['catspan2']['total'] > 0):
    foreach ($_from AS $this->_var['cat_child1_0_32812500_1397790200']):
        $this->_foreach['catspan2']['iteration']++;
?>
		<a href="<?php echo $this->_var['cat_child1_0_32812500_1397790200']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['cat_child1_0_32812500_1397790200']['name']); ?>"><?php echo htmlspecialchars($this->_var['cat_child1_0_32812500_1397790200']['name']); ?></a>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>	
 <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
 <a class='more' href="#" title="" target="_blank">去HSTYLE首页&gt;&gt;</a>