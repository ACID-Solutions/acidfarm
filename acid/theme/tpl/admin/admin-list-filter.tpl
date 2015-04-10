<?php
$current = isset($v['current']) ? $v['current'] : null;
$key = isset($v['key']) ? $v['key'] : null;

$showall_title = $o->getConfig('admin:list:head_filters_showall:'.$key);
$head_title = $o->getConfig('admin:list:head_filters_title:'.$key);

?>

<div class="<?php echo $o::TBL_NAME; ?>_<?php echo AcidUrl::normalize($key); ?>_filter admin_list_head_filter" >

<?php if ($head_title) { ?>
<b class="admin_list_head_filter_title" ><?php echo $head_title; ?> : </b>
<?php } ?>

<?php if ($showall_title!==false) { ?>
<a class="<?php echo ($current===null ? 'selected':'unselected'); ?> admin_list_head_filter_all"
	href="<?php echo AcidUrl::build($o->getAdminCurNav(),array($o->preKey('fm_'.$key),$o->preKey('fv_'.$key))); ?>">
	<?php echo $showall_title ? $showall_title : Acid::trad('filter_show_all'); ?>
</a>
<?php } ?>

<?php
if ($elts = $v['elts']) {
	foreach ($elts as $val=>$label) {
?>
<a class="<?php echo ( (isset($current) && ($current==$val)) ? 'selected':'unselected'); ?>"
 	href="<?php echo AcidUrl::build(array($o->preKey('fm_'.$key)=>'is',$o->preKey('fv_'.$key)=>$val)+$o->getAdminCurNav(),array()); ?>" >
	<?php echo htmlspecialchars($label); ?>
</a>

<?php
	}
}
?>

</div>
