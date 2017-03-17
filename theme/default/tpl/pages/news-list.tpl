<section>
    <header>
        <h1 class="block_content_title"><a href="<?php echo $o->buildUrlList(); ?>"><?php echo AcidRouter::getName('news'); ?></a></h1>
    </header>
    <?php
    $i = 0;
    //BOUCLE :: POUR CHAQUE ELEMENT
    foreach ($v['elts'] as $elt) {
        $mod = new News($elt);

        $url_news = $mod->url();

        echo $i ?  '<hr class="large news_hr" />' : '';
    ?>
        <article class="block_news_list block_content" id="news_<?php echo $elt['id_news']; ?>" itemscope itemtype="http://schema.org/Periodical">
            <header><h2 class="news_list_title" itemprop="name"><a href="<?php echo $url_news ; ?>"  ><?php echo $mod->hscTrad('title'); ?></a></h2></header>
          	<div class="clear"></div>
            <div class="news_list_image">
            	<?php if ($mod->get('src')) {?>
			    <div itemprop="image"  class="block_content_img"><?php echo Func::callImg($mod->urlSrc('mini'),Acid::trad('image'),$mod->hscTrad('title'));?></div>
			    <?php } ?>
            </div>
            <div class="news_list_content">
	            <div class="news_list_date">Le <?php echo AcidTime::conv($elt['adate']); ?></div>
	            <div itemprop="headline" class="news_list_head">
	                <?php echo $mod->hscTrad('head'); ?>
	            </div>
	            <nav class="news_list_next">
	                <ul>
	                    <li><a itemprop="url" href="<?php echo $url_news ; ?>"><?php echo Acid::trad('read_more'); ?></a></li>
	                </ul>
	            </nav>
            </div>
            <div class="clear"></div>
            <meta itemprop="datePublished" content="<?php echo AcidTime::conv($elt['adate'],'c'); ?>"  />
            <meta itemprop="publisher" content="<?php echo Acid::get('site:name'); ?>" />
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

