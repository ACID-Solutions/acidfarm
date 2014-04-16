<div id="admin_site">

	<div id="header">
		<div id="header_content">
			<h1>Administration <?php echo Acid::get('site:name'); ?></h1>
		</div>
	</div>

	<div id="admin_site_body">
		<div id="menu_bg">
			<?php echo $v['menu']; ?>
		</div>

		<div id="corps">
			<div id="content">
			<?php echo $v['content']; ?>

			<div class="clear"></div>
			</div>
		</div>
	</div>

</div>

<div id="footer">
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
</div>