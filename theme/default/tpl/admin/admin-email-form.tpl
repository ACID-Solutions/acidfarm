<div class="user_change_form">
	<form method="post" action="" > 
		<div> 
			<input type="hidden" name="<?php echo User::preKey('do'); ?>" value="self_update" /> 
			<input type="hidden" name="<?php echo User::TBL_PRIMARY; ?>" value="<?php echo $o->getId(); ?>" size="30" /> 
			<input type="text" name="email" value="<?php echo $o->hsc('email'); ?>" size="30" /> 
			<input type="submit" name="submit" value="Changer" /> 
		</div> 
	</form> 
</div> 
