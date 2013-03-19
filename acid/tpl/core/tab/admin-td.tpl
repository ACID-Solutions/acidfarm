<?php 
	$attrs = '';
	foreach ($v['attr'] as $key => $val) {
		$attrs .= ' '.$key.'="'.$val.'" ';
	}
?>
<td <?php echo $attrs; ?> >
	<?php echo $v['cont']; ?> 
</td>
