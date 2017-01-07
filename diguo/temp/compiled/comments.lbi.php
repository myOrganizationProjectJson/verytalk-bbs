<?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
<div id="ECS_COMMENT" style="width:1100px;margin:0 auto;float:none;"><?php 
$k = array (
  'name' => 'comments',
  'type' => $this->_var['type'],
  'id' => $this->_var['id'],
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?></div>
