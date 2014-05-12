<div class="user_change_form">
	<form method="post" action="" >
		<div>
			<input type="hidden" name="connexion_do" value="change_email" />
			<input type="text" name="email" value="<?php echo $v['user']['email']; ?>" size="30" />
			<input type="submit" name="submit" value="<?php echo Acid::trad('user_btn_change'); ?>" />
		</div>
	</form>
</div>
