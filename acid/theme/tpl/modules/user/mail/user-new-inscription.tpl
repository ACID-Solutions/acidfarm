<?php  echo Acid::trad('user_welcom',array('__USER__'=>$v['username'])); ?><br /><br />

<?php
	if ($v['pass'] !== false) {
		echo Acid::trad('user_new_user_register_asked',array('__SITE__'=>Acid::get('site:name')));
	}else{
		echo Acid::trad('user_new_user_validation_asked',array('__SITE__'=>Acid::get('site:name')));
	}
?>

<?php if (!empty($v['need_validation'])) { ?>
<br /><br />
<?php echo Acid::trad('user_subscribe_ask_click'); ?><br />
<a href="<?php echo $v['link']; ?>"><?php echo $v['link']; ?></a><br /><br />

<?php } ?>
<?php if ($v['pass'] !== false) {?>
<br />
<?php echo Acid::trad('user_new_user_print_infos'); ?><br />
<?php echo Acid::trad('login'); ?> : <?php echo $v['email']; ?><br />
<?php echo Acid::trad('password'); ?> : <?php echo $v['pass']; ?><br />
<br />
<?php } ?>

<?php  echo Acid::trad('user_mail_footer',array('__SITE__'=>Acid::get('site:name'))); ?><br />
<?php echo Acid::get('url:system'); ?>
