<!--<div id="actu-list">
	<div class="block_content" id="actu_content_list">
		<h1 class="block_content_title"><a href="<?php echo $o->buildUrlList(); ?>"><?php echo AcidRouter::getName('news'); ?></a></h1>
		<div class="block_content_text">	
	<?php 
	$i = 0;
	//BOUCLE :: POUR CHAQUE ELEMENT
	foreach ($v['elts'] as $elt) { 
		$mod = new Actu($elt);
		
		$url_actu = $mod->url();
		
		echo $i ?  '<hr class="large actu_hr" />' : '';
	?>
		<div class="block_actu_list" id="actu_<?php echo $elt['id_actu']; ?>">
			<h2 class="actu_list_title"><a href="<?php echo $url_actu ; ?>"><?php echo $mod->hscTrad('title'); ?></a></h2>
			<div class="actu_list_date">Le <?php echo AcidTime::conv($elt['adate']); ?></div>
			<div class="actu_list_head">
				<?php echo $mod->hscTrad('head'); ?>
			</div>
			<div class="actu_list_next">
				<a href="<?php echo $url_actu ; ?>"><?php echo Acid::trad('read_more'); ?></a>
			</div>
		</div>
	
	<?php 
		$i++;
	} 
	//FIN BOUCLE
	?>
	
		</div>
	</div>
	
	<?php  if ($v['pagination']) { ?>
		<div id="actu_pagination">
			<?php echo $v['pagination']; ?>
		</div>
	<?php  } ?>
</div>-->
<section>
    <header>
        <h1 class="block_content_title"><a href="<?php echo $o->buildUrlList(); ?>"><?php echo AcidRouter::getName('news'); ?></a></h1>
    </header>
    <?php 
    $i = 0;
    //BOUCLE :: POUR CHAQUE ELEMENT
    foreach ($v['elts'] as $elt) { 
        $mod = new Actu($elt);
        
        $url_actu = $mod->url();
        
        echo $i ?  '<hr class="large actu_hr" />' : '';
    ?>
        <article class="block_actu_list" id="actu_<?php echo $elt['id_actu']; ?>">
            <header><h2 class="actu_list_title"><a href="<?php echo $url_actu ; ?>"><?php echo $mod->hscTrad('title'); ?></a></h2></header>
            <div class="actu_list_date">Le <?php echo AcidTime::conv($elt['adate']); ?></div>
            <div class="actu_list_head">
                <?php echo $mod->hscTrad('head'); ?>
            </div>
            <nav class="actu_list_next">
                <ul>
                    <li><a href="<?php echo $url_actu ; ?>"><?php echo Acid::trad('read_more'); ?></a></li>
                </ul>
                
            </nav>
        </article>
    
    <?php 
        $i++;
    } 
    //FIN BOUCLE
    ?>
    <?php  if ($v['pagination']) {
         echo $v['pagination'];
    } ?>
</section>