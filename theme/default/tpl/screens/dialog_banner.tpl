<div id="dialog_banner">
	<div id="dialog_banner_bg" onclick="slideUpDialogBanner();" ></div>
	<div id="dialog_banner_box">
		<div id="dialog_banner_titre"  >
		Information
			<div id="dialog_banner_toggle"  class="opened" >
				[ <a id="dialog_banner_toggle_inner"  href="#" onclick="closeDialogBanner(); return false;" >Fermer</a> ]
			</div>
		</div> 
		<div id="dialog_banner_content">
			<?php echo $v['dialog'];?>
		</div> 
	</div> 
	
</div> 
 
<script type="text/javascript"> 
<!-- 

	var dialogBannerConfig = new Array();

	var slideDownDialogBanner = function() { 
		
 		if ($('#dialog_banner_toggle').attr('class')=='closed') {

 			$('#dialog_banner_toggle').addClass('opened');
 			$('#dialog_banner_toggle').removeClass('closed');

 			$('#dialog_banner_box').css('right', dialogBannerConfig['right']);
			$('#dialog_banner_box').css('position', dialogBannerConfig['position']);
			$('#dialog_banner_box').css('top', dialogBannerConfig['top']);
			$('#dialog_banner_box').css('background', dialogBannerConfig['background']);
			$('#dialog_banner_box').height(dialogBannerConfig['height']);
			$('#dialog_banner_box').width(dialogBannerConfig['width']);
			$('#dialog_banner_box').css('border', dialogBannerConfig['border']);
			$('#dialog_banner_titre').show();
			$('#dialog_banner_content').show();


			//slideDialogBanner(true);
 		}
	}

	var slideUpDialogBanner = function() { 
		if ($('#dialog_banner_toggle').attr('class')=='opened') {

			$('#dialog_banner_toggle').removeClass('opened');
			$('#dialog_banner_toggle').addClass('closed');

			dialogBannerConfig['right'] = $('#dialog_banner_box').css('right');
			dialogBannerConfig['position'] = $('#dialog_banner_box').css('position');
			dialogBannerConfig['top'] = $('#dialog_banner_box').css('top');
			dialogBannerConfig['background'] = $('#dialog_banner_box').css('background');
			dialogBannerConfig['height'] = $('#dialog_banner_box').height();
			dialogBannerConfig['width'] = $('#dialog_banner_box').width();
			dialogBannerConfig['border'] = $('#dialog_banner_box').css('border');

			$('#dialog_banner_box').css('right', '0px');
			$('#dialog_banner_box').css('position', 'absolute');
			$('#dialog_banner_box').css('top', '0px');
			$('#dialog_banner_box').height(50);
			$('#dialog_banner_box').width(50);
			$('#dialog_banner_box').css('background', 'transparent');
			$('#dialog_banner_box').css('border', '0px');
			$('#dialog_banner_titre').hide();
			$('#dialog_banner_content').hide();
			

			//slideDialogBanner(false);
		}
	}

	var slideDialogBanner = function(open) { 
		$('#dialog_banner_content').slideToggle();
	} 

	var closeDialogBanner = function() {
		$('#dialog_banner').hide();
	}  

	$('#dialog_banner_box').bind("mouseenter",  function() { slideDownDialogBanner(); } );
	//$('#dialog_banner_box').bind("mouseleave", function() { setTimeout(slideUpDialogBanner(),100); });
	
	$(window).bind("load", function() { setTimeout(slideUpDialogBanner,3000 ); });

--> 
</script> 