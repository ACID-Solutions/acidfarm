<?php

if (!file_exists(AcidRegistration::file())) {


	if (!empty($_POST['registration'])) {

		AcidRegistration::executeRegistration();
		AcidUrl::redirection(AcidUrl::build());

	}else{
?>
	<form style="margin:30px 0px;"  action="" method="POST">
	<div>
		<input type="hidden"  name="dontreload" value="1" />
		<h2>Registration</h2>
		<label>Subscribe to Acidfarm's recording service <input type="checkbox" name="registration[allowed]"  value="1" checked="checked" /></label><br />
		registration domain : <input size="30" type="text" id="registration_url"  name="registration[url]" value="<?php echo empty($_SERVER['HTTPS']) ? 'http' : 'https'; ?>://<?php echo $_SERVER['HTTP_HOST']; ?><?php echo implode('/',array_slice(explode('/',$_SERVER['PHP_SELF']),0,-1)); ?>/" /> (ex : http://www.acid-solutions.fr)<br />
		registration contact : <input type="text" id="registration_contact" name="registration[contact]" value="" /><br />
		registration email : <input type="text" id="registration_email" name="registration[email]" value="" /><br />
		registration phone : <input type="text" id="registration_phone" name="registration[phone]" value="" /><br />
		<input type="submit" />
	</div>
	</form>

<?php
	}

}else{

	if (AcidRegistration::datas('allowed')) {

		$public = AcidRegistration::datas('public');
		$version = AcidRegistration::datas('version');
		$real_version = AcidRegistration::realversion();
		$client_id = AcidRegistration::datas('id_client');
		$url = AcidRegistration::datas('url');

		$maintenanceinfo = AcidRegistration::infoUrl();

?>
	<div id="maintenance_box" >
		<p>
		Version : <?php echo $version; ?><br />
		Version complète : <?php echo $real_version; ?><br />
		Numéro d'enregistrement : <?php echo $client_id; ?><br />
		Url enregistrée : <?php echo $url; ?>
		</p>
		<div id="maintenance_info" >

		</div>
		<script type="text/javascript">
		<!--
			var Maintenance = {
				get : function() {
					$('#maintenance_info').html('recherche de mise à jour...');
					$.get('<?php echo $maintenanceinfo; ?>',function(res) {
						if (res.response!=undefined) {
							if (res.response=='success') {
								var msg = '';

								if (res.maj.length) {
									msg = msg + '<p>Des mises à jour sont disponibles.</p>';
								}else{
									msg = msg + '<p>Aucune mise à jour disponible.</p>';
								}

								if (res.info_message) {
									msg = msg + res.info_message;
								}

								if (msg) {
									$('#maintenance_info').html(msg);
								}
							}
						}
					});
				}
			}
		$().ready(function() {
			Maintenance.get();
		});
		-->
		</script>

	</div>
	<div class="clear"></div>

<?php
	}
}
?>