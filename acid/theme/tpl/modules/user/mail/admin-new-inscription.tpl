<?php  echo Acid::trad('user_new_user'); ?>,<br /><br />

<?php  echo Acid::trad('user_new_user_registered',array('__SITE__'=> Acid::get('site:name'),'__NAME__'=>$v['username'])); ?><br /><br />


<?php echo  Acid::get('site:name'); ?>
