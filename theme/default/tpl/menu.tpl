<nav id="nav">
    <ul id="menu" style="padding:0;margin:0;">
	<?php 
	$my_key = AcidRouter::searchKey(AcidRouter::getParamById(0));
	$my_key =  $my_key ? $my_key : AcidRouter::getParamById(0);
	$my_key = ($my_key == 'default') ? 'index' : $my_key;
		
	$i = 0;
	//BOUCLE :: POUR CHAQUE ELEMENT
	foreach ($v['elts'] as $key => $tab) { 
		$link  = $tab['url'];
		$title = $tab['name'];
			
		$selected = ($my_key == $key) ? ' selected' : 'unselected';
		$sep = $i ? ' - ' : '' ;
	?>
		<?php //echo $sep; ?>
		<li class="menu <?php echo $selected; ?>" style="display:inline;"><a href="<?php echo $link; ?>"><?php echo $title; ?></a></li>
	
	<?php 
		$i++;
	} 
	//FIN BOUCLE
	?>
	</ul>
</nav>