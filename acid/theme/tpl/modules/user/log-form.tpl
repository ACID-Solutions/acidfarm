<form method="post" action=""> 
	<div> 
		<input type="hidden" name="do" value="login" /> 
		<table> 
			<tr><td><?php echo Acid::trad('login'); ?></td><td><input type="text" name="login" value="" class="user_login_default_form"/></td></tr> 
			<tr><td><?php echo Acid::trad('password'); ?></td><td><input type="password" name="pass" value="" /></td></tr> 
			<tr><td>&nbsp;</td><td><input type="submit" value="<?php echo Acid::trad('user_btn_login'); ?>" /></td></tr> 
		</table> 
	</div> 
</form>
		
<?php if ($v['focus']) { ?> 

	<script type="text/javascript"> 
	<!-- 
		$(".user_login_default_form").focus(); 
	--> 
	</script>
	
<?php }	?>