<div id="gallery_wall">

<?php 
	if (count($v['elts'])) {
		foreach ($v['elts'] as $elt) {
			if ($elt['src']) {
?>

  <div class="wall_elt">
 	 <div class="wall_elt_bg">
 	 	<div class="wall_elt_body">
 	 		<a class="wall_elt_link" href="<?php echo Photo::genUrlSrc($elt['src'],'large'); ?>">
 	 			<img 	class="wall_elt_img" 
 	 					src="<?php echo Photo::genUrlSrc($elt['src'],'diapo'); ?>" 
 	 					title="<?php echo htmlspecialchars($elt['name']);?>" 
 	 					alt="<?php echo htmlspecialchars($elt['name']);?>" 
 	 			/>
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