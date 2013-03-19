<?php echo Acid::trad('user_password_ask_enter_mail'); ?> 
<form method="get" action=""> 
	<div> 
		<input type="text" name="pass_oublie" value="<?php echo $v['email']; ?>" size="30" /> 
		 <input type="submit" name="submit" value="Valider" class="input_btn" /><br/> 
	</div> 
</form>
