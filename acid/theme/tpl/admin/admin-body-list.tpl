<div class="generic_elts_list_bg">
	<?php echo $v['nav']; ?>

	<?php echo $v['head_links']; ?>
    
    <div class="generic_elts_list">
		<?php 
			if ($v['content']) { 
				echo $v['content'];
			}else{
		?>
			<div class="generic_elts_empty">
			<?php 
				echo Acid::trad('no_content_available');
			?>
			</div>
		<?php } ?>
	</div>
	
	<div class="generic_elts_footer">
	<?php echo $v['infos']; ?> 
	<?php echo $v['nav']; ?> 
	</div>
</div>