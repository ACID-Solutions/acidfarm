<?php  echo Acid::trad('user_welcom',array('__USER__'=>$v['username'])); ?>

<?php  echo Acid::trad('user_change_mail_ask_click'); ?> 
<?php echo $v['link']; ?> 

<?php  echo Acid::trad('user_mail_footer',array('__SITE__'=>Acid::get('site:name'))); ?> 
<?php echo Acid::get('url:system'); ?> 
