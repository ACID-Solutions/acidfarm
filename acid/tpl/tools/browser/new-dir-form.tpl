<form method="post" action="" id="<?php echo $v['key']; ?>_new_dir_form" style="display:none;">
<div>
	<input type="hidden" name="<?php echo $v['key']; ?>_do" value="new_dir" />
	<input type="hidden" name="name" value=""/>
	<input type="hidden" name="current_dir" value="<?php echo $v['cur_path']; ?>" />
</div>
</form>