<div id="home_diaporama">

<?php 
	if (count($v['elts'])) {
?>

  <div class=" jcarousel-skin-tango">
  <div style="position: relative; display: block;" class="jcarousel-container jcarousel-container-horizontal">
  <div style="position: relative;" class="jcarousel-clip jcarousel-clip-horizontal">
		
		<?php 
  		$add_js = '';
  		
  		?>
  		
		<ul style="overflow: hidden; position: relative; top: 0px; margin: 0px; padding: 0px; left: 0px; width: 950px;" id="mycarousel" class="jcarousel-list jcarousel-list-horizontal">
			<?php 
			$num = 0; 
			foreach ($v['elts'] as $key => $elt) { 
				$mod = new PhotoHome($elt);
				if ($mod->get('src')) {
				$num++;
			?>
		
			<li id="jcarousel_index_<?php echo $key; ?>"  
				style="float: left; list-style: none outside none;" 
				class="jcarousel-item jcarousel-item-horizontal jcarousel-item-<?php echo $key; ?> jcarousel-item-<?php echo $key; ?>-horizontal">
			<!-- 	<a href="<?php //echo Photo::genUrlSrc(null,$elt['src']); ?>" onclick="return false;" title="Echantillon <?php //echo $num; ?> de la Collection  <?php //echo $g['acid']['site']['name']; ?>">-->
					<img  title="<?php echo $mod->trad('name'); ?>" src="<?php echo $mod->urlSrc('diapo'); ?>" alt="Photo <?php echo $num; ?>" />
			<!--	</a>-->
			
				<?php $add_js .= "$('#jcarousel_index_".$key."').attr('jcarouselindex','".$key."');" . "\n" ; ?>
			</li>
			
			<?php 
				}
			}
			?>
		</ul>
	
	</div>
		<div style="display: block;" class="jcarousel-prev jcarousel-prev-horizontal pCarouselPrev"></div>
		<div style="display: block;" class="jcarousel-next jcarousel-next-horizontal pCarouselNext"></div>
	</div>
  </div>




<script type="text/javascript">
<?php $add_js; ?>

	var carousel_auto = 0.001;
	var carousel_time = 3000;
	var carousel_stop = false;
	var carousel_init_width = jQuery('#mycarousel').width();
	
	var pCarousel = {
			animationId: new Array(),
			animationDir: 'right',
			
			//time in miliseconds to wait before scrolling
			animationTimeout: 0.001,
			
			//time in miliseconds for the scrolling transition
			animationSpeed: 750,
			
			animationOffset: 0,
			animationOffsetBack: 0,

			//after each step 
			easing_max_width : 90000,
			easing_init_width : <?php echo count($v['elts']); ?>*carousel_init_width,

			onBefore: function (carousel, o, i, s) {
			    var JCcontainerID = carousel.clip.context.id;
			    var src = $('.jcarousel-item-'+carousel.first).find('img').attr('src'); 
			    $('#' + JCcontainerID).parent().css('background-image',"url("+src+")");
			    $('#' + JCcontainerID).fadeOut();
			},
			onAfter: function mycarousel_fadeIn(carousel) {
		        var JCcontainerID = carousel.clip.context.id;
		        $('#' + JCcontainerID).fadeIn();
		    },
				    
			outCallback : function() { 
				var w = jQuery('#mycarousel').width();
				if (w > pCarousel.easing_max_width) { 
					jQuery('#mycarousel').width(pCarousel.easing_init_width); 
				}
			},

			//on init
			init : function() {}
	};

	jQuery(document).ready(function() {
		jQuery('#mycarousel').jcarousel({
			wrap: 'circular',
			scroll: 1,
			visible: 1,
			auto : 3, 
			initCallback: pCarousel.init,
			itemLastOutCallback : pCarousel.outCallback,
			easing: 'linear',
			animation: pCarousel.animationSpeed, 
			itemLoadCallback: {
				onBeforeAnimation : pCarousel.onBefore,
				onAfterAnimation : pCarousel.onAfter
			},
			buttonPrevHTML: null,
			buttonNextHTML: null
		});
	});
	
</script>

<?php } ?>

</div>