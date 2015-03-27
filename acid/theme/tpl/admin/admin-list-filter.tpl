<?php
$current = isset($v['current']) ? $v['current'] : null;
?>
<div class="<?php echo $o::TBL_NAME; ?>_<?php echo AcidUrl::normalize($v['key']); ?>_filter admin_list_head_filter" >

<a class="<?php echo ($current===null ? 'selected':'unselected'); ?> admin_list_head_filter_all"
	href="<?php echo AcidUrl::build($o->getAdminCurNav(),array($o->preKey('fm_'.$v['key']),$o->preKey('fv_'.$v['key']))); ?>">
	<?php echo Acid::trad('filter_show_all'); ?>
</a>

<?php
if ($elts = $v['elts']) {
	foreach ($elts as $val=>$label) {
?>
<a class="<?php echo ( (isset($current) && ($current==$val)) ? 'selected':'unselected'); ?>"
 	href="<?php echo AcidUrl::build(array($o->preKey('fm_'.$v['key'])=>'is',$o->preKey('fv_'.$v['key'])=>$val)+$o->getAdminCurNav(),array()); ?>" >
	<?php echo htmlspecialchars($label); ?>
</a>

<?php
	}
}
?>

</div>
