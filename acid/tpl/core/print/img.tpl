<?php if ($v['src']) { ?>
<a href="<?php echo $o->genUrlKey($v['key'],$v['src'],$v['view']); ?>">
	<img src="<?php echo $o->genUrlKey($v['key'],$v['src'],$v['size']);  ?>" alt="<?php echo $v['src']; ?>" />
</a>
<?php  } ?>