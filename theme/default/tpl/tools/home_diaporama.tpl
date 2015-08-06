<div id="home_diaporama">

<?php
	$elts = Lib::getIn('elts',$v,array());
	$nb_elts = count($elts);

	//$parallax = ($nb_elts==1);
	$parallax = false;

	if ($elts) {
?>

  <div class="carousel_container">

		<ul id="mycarousel" class="carousel_container_list" >
			<?php
			$num = 0;
			foreach ($v['elts'] as $key => $elt) {
				$mod = new PhotoHome($elt);
				if ($mod->get('src')) {
				$num++;
			?>

			<li data-stellar-background-ratio="0.5" id="carousel_index_<?php echo $key; ?>"	class="carousel_item carousel-item-<?php echo $key; ?>">
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

<?php if ($parallax) { ?>
	$().ready(function(){

		var para = $('#mycarousel').find('li').each(function() {
			var img = $(this).find('img');
			$(this).css('background','url('+img.attr('src')+')');
			$(this).height(Math.round(img.height()*0.75));
			img.detach();
		});

		$.stellar({
			horizontalScrolling: false,
			responsive : true
		});

	});
<?php } else { ?>
	$('#mycarousel').slick({slide:'li', autoplay:true, dots:true });
<?php } ?>

</script>

<?php } ?>

</div>