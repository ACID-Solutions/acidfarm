<div id="gallery_wall" class="row-fluid">

<?php
	if (count($v['elts'])) {
		foreach ($v['elts'] as $elt) {
			if ($elt['src']) {
				$p = new Photo($elt);
?>

  <div class="wall_elt col-xs-12 col-md-3" >
 	 <div class="wall_elt_bg">
 	 	<div class="wall_elt_body">
 	 		<a class="wall_elt_link" href="<?php echo $p->urlSrc('large'); ?>" title="<?php echo  $p->hscTrad('name');?>" >
 	 			<img 	class="wall_elt_img"
 	 					src="<?php echo $p->urlSrc('diapo'); ?>"
 	 					title="<?php echo  $p->hscTrad('name');?>"
 	 					alt="<?php echo  $p->hscTrad('name');?>"
 	 			/>
 	 			<span class="wall_elt_overlay">
					<span class="wall_elt_overlay_content">
						<span class="wall_elt_overlay_body">
							<?php echo  $p->hscTrad('name');?>
						</span>
					<span>
 	 			</span>
 	 		</a>
 	 	</div>
 	  </div>
  </div>

<?php
			}
		}
	}
?>




<script type="text/javascript">

$('.wall_elt_link').lightBox();

</script>

<div class="clear"></div>
</div>