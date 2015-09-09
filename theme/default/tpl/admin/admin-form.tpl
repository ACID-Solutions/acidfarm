<div id="admin_form" >
	<form method="post" action="" >
		<div>
			<input type="hidden" name="do" value="login" />
			<input type="hidden" name="auto" value="1" />
			<input type="hidden" name="no_redirect" value="1" />

			<div class="logo">
				<img style="display:inline; vertical-align:middle;" src="<?php echo Acid::themeUrl('img/admin/logo.png'); ?>" alt="<?php echo Acid::get('site:name'); ?>" />
			</div>

			<div class="title" >
			<?php echo Acid::trad('admin_form_title');?>
			</div>
			<table>
				<?php if  ($v['msg']) { ?>
				<tr><td class="admin_log_msg" colspan="2" ><?php echo $v['msg']; ?></td></tr>
				<?php } ?>
				<tr><td class="label" ><?php echo Acid::trad('admin_form_login');?> </td><td><input class=user_login_field type="text" name="login" value="" size="20"/></td></tr>
				<tr><td class="label" ><?php echo Acid::trad('admin_form_password');?> </td><td><input class="user_password_field" type="password" name="pass" value="" size="20"/></td></tr>
				<tr><td>&nbsp;</td>
				<td><input type="submit" name="submit" value="<?php echo Acid::trad('admin_form_log_btn');?>" class="input_btn adminbtn_connexion" /></td>
				</tr>
			</table>
			<div class="subform_div">
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?pass_oublie="><?php echo Acid::trad('admin_form_forgotten_password');?></a>
			</div>
			<div class="bottom" >
			<?php echo Acid::trad('admin_form_secure_footer');?>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript"> 
<!-- 
	$(".user_login_field").focus(); 
--> 
</script>