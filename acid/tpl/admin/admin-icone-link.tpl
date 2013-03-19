<?php 	$click_inside = $v['click'] ? ' onclick="'.$v['click'].'" ':''; 	?>		
			
<a href="<?php echo $v['link']; ?>"  <?php echo $click_inside; ?> >
<?php 	if (!empty($v['image'])) {	?>
	<img src="<?php echo $v['image']; ?>" alt="<?php echo $v['title']; ?>" title="<?php echo $v['title']; ?>" />
<?php }else{ ?>
	<span style="width:20px; height:20px; background-color:#EFEFEF; display:table-cell;"><?php echo $v['title']; ?></span>
<?php }	?>
</a>
					 	