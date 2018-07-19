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
<div id="dialog_banner_info" onclick="slideDownDialogBanner();"></div>
 
<script type="text/javascript"> 
<!-- 

	var dialogBannerConfig = new Array();

	var slideDownDialogBanner = function() { 
		
 		if ($('#dialog_banner_toggle').attr('class')=='closed') {

 			$('#dialog_banner_toggle').addClass('opened');
 			$('#dialog_banner_toggle').removeClass('closed');

            $('#dialog_banner').show();
            $('#dialog_banner_info').hide();

			//slideDialogBanner(true);
 		}
	}

	var slideUpDialogBanner = function() { 
		if ($('#dialog_banner_toggle').attr('class')=='opened') {

			$('#dialog_banner_toggle').removeClass('opened');
			$('#dialog_banner_toggle').addClass('closed');
            
            $('#dialog_banner').hide();
            $('#dialog_banner_info').show();

			//slideDialogBanner(false);
		}
	}

	var slideDialogBanner = function(open) { 
		$('#dialog_banner_content').slideToggle();
	} 

	var closeDialogBanner = function() {
		$('#dialog_banner').hide();
	}  


	
	$(window).bind("load", function() { setTimeout(slideUpDialogBanner,3000 ); });

--> 
</script> 