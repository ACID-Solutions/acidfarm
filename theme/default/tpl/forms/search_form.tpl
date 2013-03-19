<div>
	<?php echo Acid::trad('search_ask_input'); ?><br />
	<form action="<?php echo Route::buildURL('route',array('route'=>'searchPage')); ?>" method="post">
	<div>
		<input type="text" name="search_form"  value=""/>
		<input id="search_submit"  type="submit" value="Search" />
	</div>
	</form>
</div>