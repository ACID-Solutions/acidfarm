<?php 
	$attrs = '';
	$v['attr']['class'] = isset($v['attr']['class']) ? $v['attr']['class'].' col' : 'col'; 
	if (($v['attr']) && is_array($v['attr'])) {
		foreach ($v['attr'] as $key => $val) {
			$attrs .= ' '.$key.'="'.$val.'" ';
		}
	}
?>
<th <?php echo $attrs; ?> ><?php echo $v['cont']?></th>
