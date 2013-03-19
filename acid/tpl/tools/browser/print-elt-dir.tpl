<div class="fsb_belt_dir">
	<div class="fsb_belt_img">
		<a href="<?php echo $v['link']; ?>">
			<img src="<?php echo $v['img_path']; ?>dossier.png" alt="" title="<?php echo $v['attrs']['name']; ?>" />
		</a>
	</div>
		<div class="fsb_belt_file_name">
			<a href="<?php echo $v['link']; ?>"> <?php echo AcidVarString::split($v['attrs']['name'],30); ?> </a>
		</div>
	<div class="fsb_belt_dir_action">
		<div class="fsb_belt_file_action">
			<a href="#" title="<?php echo Acid::trad('browser_delete'); ?>" onclick="fsbDelete('<?php echo $v['key']; ?>_delete_form','<?php echo $v['attrs']['path']; ?>','dir');return false;">
					<img src="<?php echo Acid::get('url:img');?>admin/fsb/delete_m.png" alt="" />
					<?php echo Acid::trad('browser_delete'); ?>
			 </a>
			 - 	
			 <a href="#" title="<?php echo Acid::trad('browser_change_name'); ?>" onclick="fsbChangeName('<?php echo $v['key']; ?>_new_name_form','<?php echo $v['attrs']['name']; ?>','dir');return false;">
				<img src="<?php echo Acid::get('url:img');?>admin/fsb/update_m.png" alt="" />
				<?php echo Acid::trad('browser_change_btn'); ?>
			</a>
		 </div>
	</div>
</div>			