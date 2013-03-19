<?php echo Acid::trad('user_error_email_dismatch'); ?> 
<form method="get" action=""> 
	<div> 
		<?php echo Acid::trad('mail'); ?><br/><input type="text" name="pass_oublie" value="<?php echo $v['email'] ?>" size="30" /><br/> 
		<br/><input type="submit" name="submit" value="<?php echo Acid::trad('user_btn_validate'); ?>" class="input_btn" /><br/> 
	</div> 
</form> 
