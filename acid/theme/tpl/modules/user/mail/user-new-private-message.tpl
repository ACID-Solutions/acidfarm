<?php echo Acid::trad('user_welcom',array('__USER__'=>$v['dest_user'])); ?> 
<?php echo Acid::trad('user_message_received',array('__USER__'=>$v['from_user'],'__SITE__'=>Acid::get('site:name'))); ?>  

<?php echo $v['title']; ?> 

<?php echo Acid::trad('user_message_read_it'); ?> 

<?php echo $v['link']; ?> 

<?php echo Acid::trad('user_mail_footer',array('__SITE__'=>Acid::get('site:name'))); ?> 
<?php echo Acid::get('url:system'); ?> 
