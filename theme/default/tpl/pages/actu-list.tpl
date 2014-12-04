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
        <article class="block_actu_list block_content" id="actu_<?php echo $elt['id_actu']; ?>">
            <header><h2 class="actu_list_title"><a href="<?php echo $url_actu ; ?>"><?php echo $mod->hscTrad('title'); ?></a></h2></header>
          	<div class="clear"></div>
            <div class="actu_list_image">
            	<?php if ($mod->get('src')) {?>
			    <div class="block_content_img"><?php echo Func::callImg($mod->urlSrc('mini'),Acid::trad('image'),$mod->hscTrad('title'));?></div>
			    <?php } ?>
            </div>
            <div class="actu_list_content">
	            <div class="actu_list_date">Le <?php echo AcidTime::conv($elt['adate']); ?></div>
	            <div class="actu_list_head">
	                <?php echo $mod->hscTrad('head'); ?>
	            </div>
	            <nav class="actu_list_next">
	                <ul>
	                    <li><a href="<?php echo $url_actu ; ?>"><?php echo Acid::trad('read_more'); ?></a></li>
	                </ul>
	            </nav>
            </div>
            <div class="clear"></div>
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