<?php if (!empty($v['onglets'])): ?>
<ul class="nav nav-tabs">
	<?php foreach ($v['onglets'] as $onglet): ?>
	<li <?php echo $onglet->isSelected ? 'class="selected active"' : ''; ?> >
		<a href="<?php echo $onglet->url; ?>"><?php echo $onglet->name; ?></a>		
	</li>
	<?php endforeach ?>
</ul>
<?php endif ?>