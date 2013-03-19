<div class="user_change_form">
	<form method="post" action="" >
		<div>
			<input type="hidden" name="connexion_do" value="change_password" />
			<table>
				<tr><td><?php echo Acid::trad('user_cur_password'); ?></td><td><input type="password" name="old_pass" value="" size="20" /></td></tr>
				<tr><td><?php echo Acid::trad('user_new_password'); ?></td><td><input type="password" name="new_pass" value="" size="20" /></td></tr>
				<tr><td><?php echo Acid::trad('user_confirm_password'); ?></td><td><input type="password" name="confirmation" value="" size="20" /></td></tr>
				<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="<?php echo Acid::trad('user_btn_change'); ?>" /></td></tr>
			</table>
		</div>
	</form>
</div>	