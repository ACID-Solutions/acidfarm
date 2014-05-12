<div id="connexion_inscription"> 
	<h2><?php echo Acid::trad('user_registrer_form'); ?></h2> 
	<form method="post" action=""> 
		<div> 
			<input type="hidden" name="connexion_do" value="inscription" /> 
			<?php echo Acid::trad('mail'); ?><br/><input type="text" name="email" value="<?php echo $v['email']; ?>" size="30"/><br/> 
			<?php echo Acid::trad('login'); ?><br/><input type="text" name="login" value="<?php echo $v['login']; ?>" /><br/> 
			<?php echo Acid::trad('password'); ?><br/><input type="password" name="pass" value="<?php echo $v['pass']; ?>" /><br/> 
			<?php echo Acid::trad('user_confirm_password'); ?><br/><input type="password" name="confirmation" value="<?php echo $v['pass']; ?>" /><br/> 
			<br/><input type="submit" name="submit" value="<?php echo Acid::trad('user_btn_register'); ?>" /><br/> 
		</div> 
	</form> 
</div> 
