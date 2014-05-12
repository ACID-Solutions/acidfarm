<div class="generic_nb_elts">

<div class="generic_nb_elts_infos"> <?php echo $v['txt_info']; ?> </div>

<div class="generic_nb_elts_form">
<form method="get" action="" id="<?php echo $v['prekey'].'custom_lp'; ?>">
	<div>
	<?php 
		foreach ($v['get'] as $k=>$val) {
	?>
			<input type="hidden" name="<?php echo $k; ?>" value="<?php echo $val; ?>" />
	<?php 
		}
	?>
	
	<?php 
		foreach ($v['cur_nav'] as $k=>$val) {
			if ($k != $v['prekey'].'ll') { 
	?>
			<input type="hidden" name="<?php echo $k; ?>" value="<?php echo $val; ?>" />
	<?php 
			}
		}
	?>	

	<?php echo Acid::trad('admin_list_pagination'); ?> 
	<input type="text" name="<?php echo $v['prekey'].'ll'; ?>" value="<?php echo $v['ll']; ?>" size="3"/>
	<input type="button" value="OK" onclick="window.document.getElementById('<?php echo $v['prekey'].'custom_lp'; ?>').submit();return false;" />

	</div>
</form>
</div>
				
<?php if (!$v['hide_filter_ind']) { ?>
<div class="generic_nb_elts_filter" style="text-align:center;">
	<a href="<?php echo AcidUrl::build(); ?>"><?php echo Acid::trad('admin_list_del_filter'); ?></a>
</div>
<?php }  ?>

<div class="clear"></div>
</div>