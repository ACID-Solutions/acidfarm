<article id="news" class="block_content" itemscope itemtype="http://schema.org/NewsArticle">

    <header>
        <h1 class="block_content_title" itemprop="name" >
            <a href="<?php echo $o->url() ; ?>"  itemprop="url" ><?php echo $o->hscTrad('title');?></a>
        </h1>
        <div class="block_content_date">Le <?php echo AcidTime::conv($o->get('adate'));?></div>
        <div itemprop="headline" class="block_content_head"><?php echo $o->trad('head');?></div>
    </header>

    <?php if ($o->get('src')) {?>
        <div class="block_content_img">
                <?php echo Func::callImg($o->urlSrc('large'),Acid::trad('image'),$o->hscTrad('title'));?>
        </div>
    <?php } ?>

    <div class="block_content_text content_body">
    	<?php echo $o->trad('content');?>
    	<div class="clear"></div>
    </div>

    <?php /*
    <footer>
	    <nav id="pagination" >
	    	<ul class="pagination">
	    		<?php if (($prev = Lib::getIn('prev',$v)) && ($prev->getId())) { ?>
	    		<li class="nav_prev" ><a href="<?php echo $prev->url(); ?>"><?php echo Acid::trad('previous'); ?> </a></li>
	    		<?php } ?>
	    		<li><a href="<?php echo News::buildUrlList(); ?>"><?php echo Acid::trad('all_news'); ?></a></li>
	    		<?php if (($next = Lib::getIn('next',$v)) && ($next->getId())) { ?>
	    		<li class="nav_next" ><a href="<?php echo $next->url(); ?>"><?php echo Acid::trad('next'); ?> </a></li>
	    		<?php } ?>
	    	</ul>
	    </nav>
    </footer>
	*/ ?>

    <!-- METAS only-->
    <meta itemscope itemprop="mainEntityOfPage"  itemType="https://schema.org/WebPage" itemid="<?php echo $o->url();  ?>"/>
    <meta itemprop="datePublished" content="<?php echo AcidTime::conv($o->get('adate'),'c'); ?>"  />
    <meta itemprop="dateModified" content="<?php echo AcidTime::conv($o->get('adate'),'c'); ?>" />
    <meta itemprop="dateCreated" content="<?php echo AcidTime::conv($o->get('adate'),'c'); ?>" />
    <meta itemprop="author" content="<?php echo Acid::get('site:name'); ?>" />
    <meta itemprop="description" content="<?php echo AcidVarString::split($o->trad('content'),80);?>" />

    <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
        <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
            <meta itemprop="url" content="<?php echo Acid::get('url:img_abs');  ?>site/logo.png">
        </div>
        <meta itemprop="name" content="<?php echo Acid::get('site:name'); ?>">
    </div>

    <?php if ($o->get('src')) {?>
        <div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
            <meta itemprop="url" content="<?php echo Acid::get('url:prefix').$o->urlSrc('diapo');  ?>">
            <meta itemprop="width" content="180">
            <meta itemprop="height" content="180">
        </div>
    <?php } ?>
    <!-- END METAS only-->

</article>