<form method="post" action="" id="<?php echo $v['key']; ?>_new_name_form" style="display:none;">
<div>
	<input type="hidden" name="<?php echo $v['key']; ?>_do" value="new_name" />
	<input type="hidden" name="name" value=""/>
	<input type="hidden" name="current_name" value=""/>
	<input type="hidden" name="type" value=""/>
	<input type="hidden" name="current_dir" value="<?php echo $v['cur_path']; ?>" />
</div>
</form>