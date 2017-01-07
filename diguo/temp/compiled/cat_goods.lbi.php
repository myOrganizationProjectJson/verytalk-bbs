 <?php $_from = $this->_var['cat_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
 <?php if ($this->_var['goods_cat']['id'] == 16): ?> 
  <li class='amh_box'>
		        <a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['thumb']; ?>" alt="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" ></a>
		        <p>
            	    <a href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" target="_blank" class="tname"><?php echo htmlspecialchars($this->_var['goods']['short_name']); ?></a></a>
                    <span class="price fr"><?php if ($this->_var['goods']['promote_price'] != ""): ?>
          <?php echo $this->_var['goods']['promote_price']; ?>
          <?php else: ?>
          <?php echo $this->_var['goods']['shop_price']; ?>
          <?php endif; ?></span>
            	</p>
		     </li>    
   <?php elseif ($this->_var['goods_cat']['id'] == 18): ?> 
   	<li class="minizaru_box">
		        <a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['thumb']; ?>" alt="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" ></a>
		        <p>
		        	<span class="fl">原价<del><?php echo $this->_var['goods']['market_price']; ?></del></span>
		        	<span class="fr">热销价<ins>
                    <?php if ($this->_var['goods']['promote_price'] != ""): ?>
          <?php echo $this->_var['goods']['promote_price']; ?>
          <?php else: ?>
          <?php echo $this->_var['goods']['shop_price']; ?>
          <?php endif; ?></ins></span>
		        </p>
	</li>      
   <?php else: ?>                      
  <li class='nibbuns_box'>
		        <a href="<?php echo $this->_var['goods']['url']; ?>" target="_blank"><img src="<?php echo $this->_var['goods']['thumb']; ?>" alt="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" ></a>
		        <p><span class="price"> <?php if ($this->_var['goods']['promote_price'] != ""): ?>
          <?php echo $this->_var['goods']['promote_price']; ?>
          <?php else: ?>
          <?php echo $this->_var['goods']['shop_price']; ?>
          <?php endif; ?></span></p>
	       	    <p><a href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" target="_blank"><?php echo htmlspecialchars($this->_var['goods']['short_name']); ?></a></p>
	       	    <p><a class="qiang" href="<?php echo $this->_var['goods']['url']; ?>" title="<?php echo htmlspecialchars($this->_var['goods']['name']); ?>" target="_blank">立即购买</a></p>
		      </li>
        
   <?php endif; ?>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>