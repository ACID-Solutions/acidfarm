<input id="bodystate" type="checkbox" value="1" style="display:none;" />
<div id="site" class="admin">

	<input id="menustate" type="checkbox" value="1" style="display:none;" />
	<div id="admin_site">

		<div id="header">
			<?php echo Acid::tpl('admin/siteadmin-header.tpl',$v,$o);?>
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
		<?php echo Acid::tpl('admin/siteadmin-footer.tpl',$v,$o);?>
	</div>
</div>