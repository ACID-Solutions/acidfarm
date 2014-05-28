<div id="header" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div id="header_content" class="container">
		<div class="row">
			<div class="col-md-12  text-center">
				<a id="header_logo_link" href="<?php echo Acid::get('url:folder_lang'); ?>">
					<img id="header_logo" src="<?php echo Acid::themeUrl('img/site/logo.png'); ?>" alt="<?php echo Acid::get('site:name'); ?>" />
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div id="menu_block"><?php echo Func::getMenu(); ?></div>
			</div>
		</div>
	</div>
</div>

<div id="ariane" class="jumbotron">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?php echo Func::getAriane(Acid::get('ariane')); ?>
			</div>
		</div>
	</div>
</div>