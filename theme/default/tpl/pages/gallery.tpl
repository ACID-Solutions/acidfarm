<section id="gallery" class="block_content">
	<header id="gallery_head">
		<h1><a href="<?php echo Route::buildUrl('gallery'); ?>"><?php echo AcidRouter::getName('gallery');?></a></h1>
	</header>
	<div id="page_content" class="content_body" >
		 <?php echo Acid::tpl('tools/wall.tpl',$v,$o); ?>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</section>
