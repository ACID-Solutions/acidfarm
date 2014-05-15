<?php echo Acid::trad('user_welcom',array('__USER__'=>$v['dest_user'])); ?> <br />
<?php echo Acid::trad('user_message_received',array('__USER__'=>$v['from_user'],'__SITE__'=>Acid::get('site:name'))); ?>  <br /><br />

<?php echo $v['title']; ?> <br /><br />

<?php echo Acid::trad('user_message_read_it'); ?> <br />

<a href="<?php echo $v['link']; ?>"><?php echo $v['link']; ?></a><br /><br />

<?php echo Acid::trad('user_mail_footer',array('__SITE__'=>Acid::get('site:name'))); ?> <br />
<?php echo Acid::get('url:system'); ?>
