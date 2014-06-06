<div id="home_diaporama">

<?php
	if (count($v['elts'])) {
?>

  <div class="carousel_container">

		<ul id="mycarousel" class="carousel_container_list"  >
			<?php
			$num = 0;
			foreach ($v['elts'] as $key => $elt) {
				$mod = new PhotoHome($elt);
				if ($mod->get('src')) {
				$num++;
			?>

			<li id="carousel_index_<?php echo $key; ?>"	class="carousel_item carousel-item-<?php echo $key; ?>">
			<!-- 	<a href="<?php //echo Photo::genUrlSrc(null,$elt['src']); ?>" onclick="return false;" title="Echantillon <?php //echo $num; ?> de la Collection  <?php //echo $g['acid']['site']['name']; ?>">-->
					<img  title="<?php echo $mod->trad('name'); ?>" src="<?php echo $mod->urlSrc('diapo'); ?>" alt="Photo <?php echo $num; ?>" />
			<!--	</a>-->
			</li>

			<?php
				}
			}
			?>
		</ul>
  </div>




<script type="text/javascript">

$('#mycarousel').slick({slide:'li', autoplay:true, dots:true });

</script>

<?php } ?>

</div>