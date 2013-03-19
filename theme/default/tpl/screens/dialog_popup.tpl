<div id="dialog">
	<div id="dialog_bg" onclick="$('#dialog_box').hide();$('#dialog_bg').fadeOut();return false;"></div>
	<div id="dialog_box">
		<div id="dialog_titre">Information</div> 
		<div id="dialog_content">
			<?php echo $v['dialog'];?>
		</div> 
		<div id="dialog_btnok"><a href="" onclick="$('#dialog_box').hide();$('#dialog_bg').fadeOut();return false;">OK</a></div> 
	</div> 
</div> 
 
<script type="text/javascript"> 
<!-- 

	var Dialog = {
	
		close : function() {
			$('#dialog_box').hide();
			$('#dialog_bg').fadeOut();
			$(window).unbind("resize scroll", Dialog.init);
		},

		bind : function (event) {
			if (event.keyCode==27) {
				Dialog.close();
			}
		},
		
		init : function () {
			$("#dialog_box").css("top",($("#dialog_bg").height() - $("#dialog_box").height())/2+$(document).scrollTop());
		}
	}

	

	$(window).bind("resize scroll", Dialog.init);
	Dialog.init(); 

	$(document).bind(
			'keyup',
			function(event) {
				if (event.keyCode==27) {
					Dialog.close();
				}
			}
	);
	

--> 
</script> 
