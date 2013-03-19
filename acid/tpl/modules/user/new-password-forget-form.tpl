<form method="post" action=""> 
	<div> 
		<input type="hidden" name="connexion_do" value="forget_pass" /> 
		<?php if (!empty($v['src_page'])) { ?>
			<input type="hidden" name="next_page" value="<?php echo $v['src_page']; ?>" /> 
		<?php }?>
		<?php echo Acid::trad('user_new_password'); ?><br/><input type="password" name="pass" value="" id="form_new_pass"/><br/> 
		<?php echo Acid::trad('user_confirm_password'); ?><br/><input type="password" name="confirmation" value="" /><br/> 
		<br/><input type="submit" name="submit" value="<?php echo Acid::trad('user_btn_validate'); ?>" class="input_btn" /><br/> 
	</div> 
</form> 
<script type="text/javascript"> 
<!-- 
	$("#form_new_pass").focus(); 
--> 
</script> 
