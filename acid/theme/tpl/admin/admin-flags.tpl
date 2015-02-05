<div class="admin_flags">
<?php
	foreach ($v['langs'] as $l) {
?>
	<a href="#" onclick="AcidLang.selectLang('<?php echo $l; ?>'); return false;" >
		<img
			src="<?php echo Acid::themeUrl('img/langs/'.$l.'_sel.png'); ?>"
			alt="<?php echo $l; ?>"
			title="<?php echo $l; ?>"
			class="lang_flag lang_flag_<?php echo $l; ?>"
		/>
	</a>
<?php
	}
?>


<script type="text/javascript">
<!--
	if (AcidLang==undefined) {
		var AcidLang = {
			selectLang : function (lang) {
				$('tr.lang').hide();
				$('tr.lang').removeClass('selected');
				$('.lang_flag').fadeTo(0,0.3);
				$('tr.'+lang).fadeIn(1000);
				$('tr.'+lang).addClass('selected');
				$('.lang_flag_'+lang).fadeTo(0,1);
			}
		}

		$(document).ready( function() { AcidLang.selectLang('<?php echo $v['def_lang']; ?>'); } );
	}
-->
</script>

</div>