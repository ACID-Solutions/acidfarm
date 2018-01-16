<?php
$ext = AcidFs::getExtension($v['attrs']['name']);
$ext = $ext ? '.'.$ext : $ext;
$name = AcidVarString::split(basename($v['attrs']['name'],$ext),30,'~');
$is_img = isset($v['attrs']['ext']) && ($v['attrs']['ext']=== 0);
?>

<div data-fsb-name="<?php echo $v['attrs']['name']; ?>" class="fsb_belt_file <?php echo $is_img ? 'fsb_belt_file_image' : '';  ?> col-xs-12 col-sm-4 col-md-1">
	<div class="fsb_belt_img">
		<a <?php if ($o->getPlugin()=='tinymce') { echo ' onclick="window.open(this.href); return false;" '; }?> href="<?php echo $v['link']; ?>" title="<?php echo $v['attrs']['name']; ?>">
			<img src="<?php echo $v['img_file']; ?>" alt="<?php echo $v['attrs']['name']; ?>" />
		</a>
	</div>
	<div class="fsb_belt_file_name">
		<a href="<?php echo $v['link']; ?>"> <?php echo $name.$ext; ?> </a>
	</div>
	<div class="fsb_belt_file_action">
		<div class="fsb_belt_file_action_eng">
			<a href="#" title="<?php echo Acid::trad('browser_delete'); ?>" onclick="fsbDelete('<?php echo $v['key']; ?>_delete_form','<?php echo $v['attrs']['path']; ?>','file'); return false;">
				<img src="<?php echo Acid::themeUrl('img/admin/fsb/delete_m.png'); ?>" alt="" />
				<?php echo Acid::trad('browser_delete'); ?>
			</a>
			 -
			<a href="#" title="<?php echo Acid::trad('browser_change_name'); ?>" onclick="fsbChangeName('<?php echo $v['key']; ?>_new_name_form','<?php echo $v['attrs']['name']; ?>','file');return false;">
				<img src="<?php echo Acid::themeUrl('img/admin/fsb/update_m.png'); ?>" alt="" />
				<?php echo Acid::trad('browser_change_btn'); ?>
			</a>
			<?php if ($o->getPlugin()=='tinymce') { ?>
			 -
			<a href="#" title="<?php echo Acid::trad('browser_choose_btn'); ?>" onclick="fsbChooseFile('<?php echo addslashes($v['link']); ?>');return false;">
				<img src="<?php echo Acid::themeUrl('img/admin/fsb/tick_m.png'); ?>" alt="" />
				<?php echo Acid::trad('browser_choose_btn'); ?>
			</a>
			<?php } ?>
		</div>
	</div>
</div>