<?php  echo Acid::trad('user_welcom',array('__USER__'=>$v['username'])); ?><br />

<?php  echo Acid::trad('user_change_mail_ask_click'); ?> <br />
<a href="<?php echo $v['link']; ?>"><?php echo $v['link']; ?></a><br /><br />

<?php  echo Acid::trad('user_mail_footer',array('__SITE__'=>Acid::get('site:name'))); ?> <br />
<?php echo Acid::get('url:system'); ?>
