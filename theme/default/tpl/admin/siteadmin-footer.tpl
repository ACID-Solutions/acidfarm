<div id="footer_content">
	<?php if (Acid::get('admin:contact')) { ?>
		<div id="footer_contact">
			<?php echo Acid::get('admin:contact'); ?>
		</div>
	<?php } ?>

	<?php if (Acid::get('admin:website')) { ?>
		<div id="footer_website">
		<a href="<?php echo Acid::get('admin:website'); ?>">
			<?php echo parse_url(Acid::get('admin:website'),PHP_URL_HOST). parse_url(Acid::get('admin:website'),PHP_URL_PATH); ?>
		</a>
		</div>
	<?php } ?>

	<div class="clear"></div>
</div>