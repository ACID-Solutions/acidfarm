<div id="admin_form" >
	<form method="get" action="" >
		<div>
			<div class="title" >
			<img style="display:inline; vertical-align:middle;" src="<?php echo Acid::get('url:img'); ?>admin/logo.png" alt="<?php echo Acid::get('site:name'); ?>" />
			</div>
			<div class="admin_alert_content" ><?php echo Acid::trad('admin_form_forget_ask_email'); ?></div>
			<table>
				<tr><td class="label" ><?php echo Acid::trad('admin_form_email');?> </td><td><input class="focus_field" type="text" name="pass_oublie" value="<?php echo $v['email']; ?>" size="30" /></td></tr>
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