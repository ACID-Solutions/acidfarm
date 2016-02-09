<?php if ($elts = Lib::getIn('elts',$v,array())) { ?>
	<aside>
		<ul class="sitemap">

			<?php foreach ($elts as $elt) { ?>
			<li class="sitemapline <?php echo Lib::getIn('class',$elt,''); ?>">
				<a	href="<?php echo Lib::getIn('url',$elt); ?>">
				<?php echo Lib::getIn('title',$elt); ?>
				</a>
			</li>
			<?php } ?>

		</ul>
	</aside>
<?php } ?>
