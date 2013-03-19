<!--<div id="actu">

    <div class="block_content" id="actu_content">
        <h1 class="block_content_title"><a href="<?php echo $o->url() ; ?>"><?php echo $o->hscTrad('title');?></a></h1>
        <div class="block_content_date">Le <?php echo AcidTime::conv($o->get('adate'));?></div>
        <div class="block_content_head"><?php echo $o->trad('head');?></div>
        <div class="block_content_text"><?php echo $o->trad('content');?></div>
    </div> 
    
    <div class="clear"></div>
</div>-->
<article id="actu">
    <header>
        <h1 class="block_content_title"><a href="<?php echo $o->url() ; ?>"><?php echo $o->hscTrad('title');?></a></h1>
        <div class="block_content_date">Le <?php echo AcidTime::conv($o->get('adate'));?></div>
        <div class="block_content_head"><?php echo $o->trad('head');?></div>
    </header>
    <div class="block_content_text"><?php echo $o->trad('content');?></div> 
    <div class="clear"></div>
</article>
