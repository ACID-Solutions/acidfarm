<article id="actu" class="block_content">
    <header>
        <h1 class="block_content_title"><a href="<?php echo $o->url() ; ?>"><?php echo $o->hscTrad('title');?></a></h1>
        <div class="block_content_date">Le <?php echo AcidTime::conv($o->get('adate'));?></div>
        <div class="block_content_head"><?php echo $o->trad('head');?></div>
    </header>
    <?php if ($o->get('src')) {?>
    <div class="block_content_img"><?php echo Func::callImg($o->urlSrc('large'),Acid::trad('image'),$o->hscTrad('title'));?></div>
    <?php } ?>
    <div class="block_content_text content_body"><?php echo $o->trad('content');?></div>
    <div class="clear"></div>
</article>
