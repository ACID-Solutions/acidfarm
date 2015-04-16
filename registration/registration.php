<?php
$maintenancebaseurl = 'http://10.11.1.61/plateforme-acidfarm/';
$maintenanceurl = $maintenancebaseurl.'rest/registration';
$maintenancefile = __DIR__.'/maintenance.json';
$acid_path = __DIR__.'/../acid';
$version_path = __DIR__.'/../version.txt';

function md5_dir($dir) {
	if (is_dir($dir)) {
		$sum = '';
		if ($tree = scandir($dir)) {
			foreach ($tree as $file) {
				if (!in_array($file, array('.','..'))) {
					$filepath = realpath($dir.'/'.$file);
					if (!is_link($filepath)) {
						if (is_dir($filepath)) {
							$sum.= md5_dir($filepath).'<br />';
						}else{
							$sum.= md5_file($filepath).'<br />';
						}
					}
				}
			}
		}
		return md5($sum);
	}
}

if (!file_exists($maintenancefile)) {
	if (!empty($_POST['registration'])) {

		if (!empty($_POST['registration']['allowed'])) {


			$fields = array();
			$fields_string = '';

			foreach ($_POST['registration'] as $key =>$val) {	$fields[$key] = $val;}

			$fields['public'] = md5((rand(0,1000)*rand(0,1000)).(rand(0,1000)*rand(0,1000)).(rand(0,1000)*rand(0,1000)).(rand(0,1000)*rand(0,1000)));

			if (is_dir($acid_path)) {
				$fields['acid_path_code'] = md5_dir($acid_path);
			}

			if (file_exists($version_path)) {
				$fields['version'] = file_get_contents($version_path);
			}

			//url-ify the data for the POST
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.urlencode($value).'&'; }
			rtrim($fields_string, '&');

			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $maintenanceurl);
			curl_setopt($ch,CURLOPT_POST, count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

			//execute post
			$result = curl_exec($ch);
			$return = json_decode($result,true);

			$return['init_request'] = $fields;
			$return['allowed'] = true;

			file_put_contents($maintenancefile,json_encode($return));


			//close connection
			curl_close($ch);


		}else{
			file_put_contents($maintenancefile,json_encode(array('allowed'=>false)));
		}

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
	$maintenance = json_decode(file_get_contents($maintenancefile),true);
	$maintenanceinfo = $maintenancebaseurl.'rest/information/'.$maintenance['id_client'].'/'.$maintenance['public'];
	if ($maintenance['allowed']) {

		try {
			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $maintenanceinfo);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

			//execute post
			$result = curl_exec($ch);
			$returninfo = json_decode($result,true);

		}catch(Exception $e) {

		}
?>
	<div id="maintenance_box" style="float:right; padding:30px; height:100%; border:1px solid #000000;"  >

		<p>
		Version : <?php echo $maintenance['version']; ?><br />
		Numéro d'enregistrement : <?php echo $maintenance['id_client']; ?><br />
		Url enregistrée : <?php echo $maintenance['url']; ?>
		</p>
		<div id="maintenance_info" >

		</div>
		<script type="text/javascript">
		<!--
			var Maintenance = {
				get : function() {
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