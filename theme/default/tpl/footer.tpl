<?php 
if (Acid::get('lang:use_nav_0')) {
	echo Func::getFlags();
}
?>

<a href="<?php echo AcidRouter::buildURL('sitemap',null,null,true,true);/*$g['conf']['url']['sitemap'];*/ ?>"> Site Map </a> © <?php echo date('Y'); ?> 
- <?php echo Acid::get('site:name') ?> 


