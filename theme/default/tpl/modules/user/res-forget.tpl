<div id="admin_form" >

	<div class="title" >
		<img style="display:inline; vertical-align:middle;" src="<?php echo Acid::get('url:img'); ?>admin/logo.png" alt="<?php echo Acid::get('site:name'); ?>" />
	</div>

	<div class="admin_alert_content" >
		<?php echo Acid::trad('user_mail_sent',array('__MAIL__'=>$v['email']));?><br/><br />
		<?php echo Acid::trad('admin_form_forget_ask_click');?>
		
	</div>

	<div class="subform_div"></div>

</div>