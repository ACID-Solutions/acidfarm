<div id="footer_content">
	<div class="row">
		<div class="col-md-8">
			<a href="<?php echo AcidRouter::buildURL('sitemap',null,null,true,true);/*$g['conf']['url']['sitemap'];*/ ?>"> Site Map </a> © <?php echo date('Y'); ?>
			- <?php echo Acid::get('site:name') ?>
		</div>
		<div class="col-md-4 text-right"">
			<?php
			if (Acid::get('lang:use_nav_0')) {
				echo Func::getFlags();
			}
			?>
		</div>
	</div>
</div>