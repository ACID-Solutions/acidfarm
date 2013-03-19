<ul>
<?php 
		foreach ($v['vars'] as $key => $var) {
?>
	<li><?php echo $key; ?>  :  <?php echo $var->getVal(); ?> </li>
	
<?php 
		}
?>
</ul>
