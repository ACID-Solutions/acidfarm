<article id="actu" class="block_content">

    <header>
        <h1 class="block_content_title"><a href="<?php echo $o->url() ; ?>"><?php echo $o->hscTrad('title');?></a></h1>
        <div class="block_content_date">Le <?php echo AcidTime::conv($o->get('adate'));?></div>
        <div class="block_content_head"><?php echo $o->trad('head');?></div>
    </header>

    <?php if ($o->get('src')) {?>
    <div class="block_content_img"><?php echo Func::callImg($o->urlSrc('large'),Acid::trad('image'),$o->hscTrad('title'));?></div>
    <?php } ?>

    <div class="block_content_text content_body">
    	<?php echo $o->trad('content');?>
    	<div class="clear"></div
    </div>

    <?php /*
    <footer>
	    <nav id="pagination" >
	    	<ul class="pagination">
	    		<?php if (($prev = Lib::getIn('prev',$v)) && ($prev->getId())) { ?>
	    		<li class="nav_prev" ><a href="<?php echo $prev->url(); ?>"><?php echo Acid::trad('previous'); ?> </a></li>
	    		<?php } ?>
	    		<li><a href="<?php echo Actu::buildUrl(); ?>"><?php echo Acid::trad('all_news'); ?></a></li>
	    		<?php if (($next = Lib::getIn('next',$v)) && ($next->getId())) { ?>
	    		<li class="nav_next" ><a href="<?php echo $next->url(); ?>"><?php echo Acid::trad('next'); ?> </a></li>
	    		<?php } ?>
	    	</ul>
	    </nav>
    </footer>
	*/ ?>

</article>
