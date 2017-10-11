<?php
$ident = Lib::getIn('ident',$v,time());
$comp = strtoupper($ident);
?>

<div id="dialog_notification_<?php echo $ident; ?>" class="dialog_notification bottom"  >
	<div class="dialog_notification_content">
		<?php echo Lib::getIn('content',$v); ?>
		<div class="clear"></div>
	</div>
	<a class="dialog_notification_close" onclick="Notif<?php echo $comp; ?>.close();" >X</a>
</div>



<script type="text/javascript">
<!--

	var Notif<?php echo $comp; ?> = {

		show : function() {
			$('#dialog_notification_<?php echo $ident; ?>').show(0,Notif<?php echo $comp; ?>.init);
		},

		close : function() {
			$('#dialog_notification_<?php echo $ident; ?>').hide();
		},

		call : function (json) {

			$('.bwin_content').html(json.content);

			Notif<?php echo $comp; ?>.show();
		},

		init : function () {

		}

	}



-->
</script>