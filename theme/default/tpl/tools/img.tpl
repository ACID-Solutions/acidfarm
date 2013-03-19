<?php 
	$attrs = isset($v['attrs']) ? $v['attrs'] : array(); 
?>
<img <?php echo AcidForm::getParams($attrs); ?>  />