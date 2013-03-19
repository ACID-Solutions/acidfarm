<div id="admin_form" >
	<form method="post" action=""> 
		<div>
			<input type="hidden" name="connexion_do" value="forget_pass" /> 
			<?php if (!empty($v['src_page'])) { ?>
				<input type="hidden" name="next_page" value="<?php echo $v['src_page']; ?>" /> 
			<?php }?>
			<div class="title" >
			<img style="display:inline; vertical-align:middle;" src="<?php echo Acid::get('url:img'); ?>admin/logo.png" alt="<?php echo Acid::get('site:name'); ?>" />
			</div>
			<div class="admin_alert_content" ><?php echo Acid::trad('admin_form_forget_ask_password'); ?></div>
			<table>
				<tr><td class="label" ><?php echo Acid::trad('user_new_password'); ?> </td><td><input class="focus_field" type="password" name="pass" value="" id="form_new_pass" /></td></tr>
				<tr><td class="label" ><?php echo Acid::trad('user_confirm_password'); ?> </td><td><input type="password" name="confirmation" value="" /></td></tr>
				<tr><td>&nbsp;</td>
				<td><input type="submit" name="submit" value="<?php echo Acid::trad('user_btn_validate'); ?>" class="input_btn" /></td>
				</tr>
			</table>
			<div class="subform_div"></div>
		</div>
	</form>
</div>

<script type="text/javascript"> 
<!-- 
	$(".focus_field").focus(); 
--> 
</script>
