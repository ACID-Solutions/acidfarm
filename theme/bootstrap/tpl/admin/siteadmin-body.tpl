<div id="admin_site" class="container-fluid">

	<div id="header"  class="row-fluid" >
		<div class="col-xs-12">
			<?php echo Acid::tpl('admin/siteadmin-header.tpl',$v,$o);?>
		</div>
	</div>

	<div id="menu"  class="row-fluid" >
		<div class="col-xs-12">
			<div class="breadcrumb clearfix">
			<?php echo Func::getMenu(); ?>
			</div>
		</div>
	</div>


	<div id="admin_site_body" class="row-fluid">
		<div id="corps" class="col-xs-12">
			<div id="content" class="row-fluid">
				<div class="panel panel-default">
					<div class="panel-body">
					<?php echo $v['content']; ?>
						<div class="clear"></div>
					</div>
				</div>


			</div>
		</div>
	</div>

</div>

<div id="footer" class="row-fluid">
	<div class="col-xs-12">
		<?php echo Acid::tpl('admin/siteadmin-footer.tpl',$v,$o);?>
	</div>
</div>