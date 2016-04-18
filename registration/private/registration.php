<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Registration
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

if (!file_exists(AcidRegistration::file())) {


	if (!empty($_POST['registration'])) {

		AcidRegistration::executeRegistration();
		AcidUrl::redirection(AcidUrl::build());

	}else{
?>
	<form style="margin:30px 0px;"  action="" method="POST">
	<div>
		<input type="hidden"  name="dontreload" value="1" />
		<h4><?php echo Acid::trad('admin_registration_title'); ?></h4>
		<label><?php echo Acid::trad('admin_registration_check'); ?></span> <input type="checkbox" name="registration[allowed]"  value="1" checked="checked" /></label><br /><br />
		<span><?php echo Acid::trad('admin_registration_domain'); ?></span> <input size="30" type="text" id="registration_url"  name="registration[url]" value="<?php echo empty($_SERVER['HTTPS']) ? 'http' : 'https'; ?>://<?php echo $_SERVER['HTTP_HOST']; ?><?php echo implode('/',array_slice(explode('/',$_SERVER['PHP_SELF']),0,-1)); ?>/" />
		<br /><i><?php echo Acid::trad('admin_registration_sample'); ?> http://www.acid-solutions.fr</i><br /><br />
		<span><?php echo Acid::trad('admin_registration_contact'); ?></span> <input type="text" id="registration_contact" name="registration[contact]" value="" /><br />
		<span><?php echo Acid::trad('admin_registration_email'); ?></span> <input type="text" id="registration_email" name="registration[email]" value="" /><br />
		<span><?php echo Acid::trad('admin_registration_phone'); ?></span> <input type="text" id="registration_phone" name="registration[phone]" value="" /><br /><br />
		<input type="submit" value="<?php echo Acid::trad('admin_registration_submit'); ?>" />
	</div>
	</form>

<?php
	}

}else{


	if (AcidRegistration::datas('need_confirmation')) {

		Acid::log('REGISTRATION','need_confirmation...');
		if (!empty($_POST['registration_confirmation'])) {
			AcidRegistration::executeConfirmation();
			AcidUrl::redirection(AcidUrl::build());
		}
	}

	if (AcidRegistration::datas('allowed')) {

		$public = AcidRegistration::datas('public');
		$version = AcidRegistration::datas('version');
		$real_version = AcidRegistration::realversion();
		$client_id = AcidRegistration::datas('id_client');
		$url = AcidRegistration::datas('url');

		$maintenanceinfo = AcidRegistration::infoUrl();
		$downloadinfo = AcidRegistration::dlUrl('');

?>
	<div id="maintenance_box" >
		<p>
		<?php echo Acid::trad('admin_board_version_v'); ?> <?php echo $version; ?><br />
		<?php echo Acid::trad('admin_board_version_vc'); ?> <?php echo $real_version; ?><br />
		<?php echo Acid::trad('admin_board_version_number'); ?> <?php echo $client_id; ?><br />
		<?php echo Acid::trad('admin_board_version_url'); ?> <?php echo $url; ?>
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
									msg = msg + '<p><?php echo Acid::trad('admin_board_version_maj_available'); ?> <a href="<?php echo $downloadinfo.$version; ?>.'+res.maj[0]+'"><?php echo Acid::trad('admin_board_version_maj_available_here'); ?></a>.</p>';
								}else{
									msg = msg + '<p><?php echo Acid::trad('admin_board_version_maj_unavailable'); ?></p>';
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