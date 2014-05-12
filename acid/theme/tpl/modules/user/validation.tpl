<div class="user_askvalid">
	<div id="attente_validation"><?php echo Acid::trad('user_need_mail_to_go_on'); ?></div>
	<?php echo Acid::trad('user_how_to_validate'); ?> 

	<form method="post" action="" id="form_send_mail_confirm"> 
		<div>
			<input type="hidden" name="connexion_do" value="send_mail_confirmation" />
		</div>
	</form>
	
	<ul>
		<li><a href="#" onclick="document.getElementById('form_send_mail_confirm').submit();"><?php echo Acid::trad('user_valid_mail_resend'); ?></a></li>
		<li><a href="<?php echo Acid::get('user:page'); ?>?change=email"><?php echo Acid::trad('user_change_mail'); ?></a></li> 
	</ul> 
</div>
			