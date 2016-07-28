<section  id="contact_content">
	<header>
		<h1 class="block_content_title"><a href="<?php echo Route::buildUrl('contact'); ?>"><?php echo AcidRouter::getName('contact'); ?></a></h1>
	</header>
	<div>
		<div id="contact_left" class="col-md-6">

			<?php echo Acid::tpl('pages/contact/form.tpl',$v,$o); ?>

			<div class="clear"></div>
		</div>

		<div id="contact_right"  class="col-md-6">

			<?php echo Acid::tpl('pages/contact/map.tpl',$v,$o); ?>

			<?php echo Acid::tpl('pages/contact/coords.tpl',$v,$o); ?>

			<div class="clear"></div>
		</div>
	</div>
</section>