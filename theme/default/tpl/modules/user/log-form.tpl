<?php 
	$name = isset($_COOKIE['user']['login']) ? htmlspecialchars($_COOKIE['user']['login']) : '';
?>
<form method="post" action="" > 
	<div> 
		<input type="hidden" name="do" value="login" /> 
		<!--  <input type="hidden" name="next_page" value="<?php  echo Acid::get('user:page'); ?>" />  -->
		<input type="hidden" name="auto" value="1" /> 
		<table> 
			<tr><td><?php echo Acid::trad('login'); ?></td><td><input type="text" name="login" value="<?php echo $name; ?>" class="user_login_default_form" size="20"/></td></tr> 
			<tr><td><?php echo Acid::trad('password'); ?></td><td><input type="password" name="pass" value="" size="20" class="user_login_default_form" /></td></tr> 
			<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="<?php echo Acid::trad('admin_form_log_btn'); ?>" class="input_btn"/></td></tr> 
		</table> 
	</div> 
</form> 
 
<br/><a href="<?php  echo Acid::get('user:page'); ?>?pass_oublie="><?php echo Acid::trad('admin_form_forgotten_password'); ?></a> 
	
<?php if ($v['focus']) { ?> 
	
	<script type="text/javascript"> 
	<!-- 
		if ($(".user_login_default_form[value='']").length) { 
			$(".user_login_default_form[value='']").first().focus(); 
		}else{
			$(".user_login_default_form").focus(); 
		}
	--> 
	</script>
	
<?php }	?>