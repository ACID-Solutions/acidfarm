<div class="user-page">
<a href="<?php  echo Acid::get('user:page'); ?>?change=password"><?php echo Acid::trad('user_ask_change_pass'); ?></a><br/>
<a href="<?php  echo Acid::get('user:page'); ?>?change=email"><?php echo Acid::trad('user_ask_change_mail'); ?></a>
<?php 
	echo Acid::tpl('modules/user/logout-form.tpl',$v,$o); 
?>
</div>

<?php  if ($v['page']->getId()) { ?>
<div>
<?php  echo $v['page']->get('content'); ?>
</div>
<?php  } ?>