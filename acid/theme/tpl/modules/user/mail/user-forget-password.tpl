<?php  echo Acid::trad('user_welcom',array('__USER__'=>$v['username'])); ?><br /><br />

<?php  echo Acid::trad('user_forget_password_site',array('__SITE__'=>Acid::get('site:name'))); ?> <br /><br />

<?php  echo Acid::trad('user_forget_password_ask_click'); ?><br />
<a href="<?php echo $v['link']; ?>" ><?php echo $v['link']; ?></a><br /><br />

<?php  echo Acid::trad('user_mail_footer',array('__SITE__'=>Acid::get('site:name'))); ?><br />
<?php echo Acid::get('url:system'); ?>