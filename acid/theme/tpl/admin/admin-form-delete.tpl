<form action="" method="post" id="<?php echo $o->preKey($v['id']); ?>_delform" >
	<div>
		<input type="hidden" name="<?php echo $o->preKey('do'); ?>" value="del" />
		<input type="hidden" name="module_do" value="<?php echo $o->getClass(); ?>" />
		<input type="hidden" name="next_page" value="<?php echo $v['next']; ?>" />
		<input type="hidden" name="<?php echo $o->tblId(); ?>" value="<?php echo $v['id']; ?>" />
	</div> 
</form>