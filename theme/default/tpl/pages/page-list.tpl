<section>
    <header>
        <h1 class="block_content_title"><a href="<?php echo $o->buildUrlList(); ?>"><?php echo AcidRouter::getName('allpage'); ?></a></h1>
    </header>
    <div id="page-list">
        <div class="block_content" id="page_content_list">
            <div class="block_content_text">
        <?php
        $i = 0;
        //BOUCLE :: POUR CHAQUE ELEMENT
        foreach ($v['elts'] as $elt) {
            $mod = new Page($elt);

            $url_page = $mod->url();

            echo $i ?  '<hr class="large actu_hr" />' : '';
        ?>

            <article class="block_page_list block_content" id="page_<?php echo $elt['id_page']; ?>">
                <header><h2 class="page_list_title"><a href="<?php echo $url_page ; ?>"><?php echo $mod->hscTrad('title'); ?></a></h2></header>
                <div class="clear"></div>
                <div class="page_list_date">Le <?php echo AcidTime::conv($elt['adate']); ?></div>
                <div class="page_list_head">
                    <?php echo $mod->splitTrad('content',100); ?>
                </div>
                <div class="page_list_next">
                    <a href="<?php echo $url_page ; ?>"><?php echo Acid::trad('read_more'); ?></a>
                </div>
                <div class="clear"></div>
            </article>

        <?php
            $i++;
        }
        //FIN BOUCLE
        ?>

            </div>
        </div>

    </div>
</section>


