<?php  echo Acid::trad('user_welcom',array('__USER__'=>$v['username'])); ?>

<?php  echo Acid::trad('user_forget_password_site',array('__SITE__'=>Acid::get('site:name'))); ?> 

<?php  echo Acid::trad('user_forget_password_ask_click'); ?> 
<?php echo $v['link']; ?> 

<?php  echo Acid::trad('user_mail_footer',array('__SITE__'=>Acid::get('site:name'))); ?> 
<?php echo Acid::get('url:system'); ?> 