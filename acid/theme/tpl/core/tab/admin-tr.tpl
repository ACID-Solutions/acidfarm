<?php 
	$attrs = '';
	foreach ($v['attr'] as $key => $val) {
		$attrs .= ' '.$key.'="'.$val.'" ';
	}
?>

<tr <?php echo $attrs; ?> > 
<?php echo $v['line']; ?> 
</tr>
