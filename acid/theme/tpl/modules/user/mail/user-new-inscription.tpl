<?php  echo Acid::trad('user_welcom',array('__USER__'=>$v['username'])); ?>

<?php 
	if ($v['pass'] !== false) {
		echo Acid::trad('user_new_user_register_asked',array('__SITE__'=>Acid::get('site:name'))); 
	}else{ 
		echo Acid::trad('user_new_user_validation_asked',array('__SITE__'=>Acid::get('site:name'))); 
	} 
?>
<?php if (!empty($v['need_validation'])) { ?> 

<?php echo Acid::trad('user_subscribe_ask_click'); ?> 
<?php echo $v['link']; ?>

<?php } ?>
<?php if ($v['pass'] !== false) {?>
	 
<?php echo Acid::trad('user_new_user_print_infos'); ?> 
<?php echo Acid::trad('login'); ?> : <?php echo $v['email']; ?> 
<?php echo Acid::trad('password'); ?> : <?php echo $v['pass']; ?>
	
<?php } ?>

<?php  echo Acid::trad('user_mail_footer',array('__SITE__'=>Acid::get('site:name'))); ?> 
<?php echo Acid::get('url:system'); ?>
