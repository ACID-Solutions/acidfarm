<?php if (!empty($v['onglets'])) { ?>
<ul>
<?php 
	foreach ($v['onglets'] as $url_onglet => $conf_onglet) {
		
		$url = !is_numeric($url_onglet) ? $url_onglet : $conf_onglet['url'];
		$name_onglet = is_array($conf_onglet) ? $conf_onglet['name'] : $conf_onglet;
		$selector_onglets = is_array($conf_onglet) ? (isset($conf_onglet['selector']) ? $conf_onglet['selector'] : $url): $url_onglet;
		//$s = ($_SERVER['REQUEST_URI'] === html_entity_decode($url_onglet)) ? $selected : '';
		$s = $o->isSelectedOnglet($selector_onglets) ? 'class="selected"' : '';
?>
		<li <?php echo $s; ?>><a href="<?php echo $url; ?>"><?php echo $name_onglet; ?></a></li>
<?php 
	}
?>
</ul>
<?php } ?>