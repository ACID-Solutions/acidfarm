<form action="" method="post" enctype="multipart/form-data" id="<?php echo $v['key']; ?>_upload_file" style="display:inline;">
<div>
	<input type="hidden" name="<?php echo $v['key']; ?>_do" value="add" />
	<input type="hidden" name="path" value="<?php echo $v['dst_dir']; ?>" />
	<input type="file" name="fichier" value="" onchange="document.getElementById('<?php echo $v['key']; ?>_upload_file').submit();"/>
	<input type="button" name="cancel" value="<?php echo Acid::trad('browser_cancel'); ?>" onclick="changeDisplay('fsb_upload_form')" />
<!--  <input type="submit" name="upload" value="upload" /> -->
</div>
</form>