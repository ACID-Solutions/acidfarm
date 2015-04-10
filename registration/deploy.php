<?php

include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../sys/glue.php';

$maintenancefile = __DIR__.'/maintenance.json';

if (file_exists($maintenancefile)) {
	if (!Acid::get('deploy:disabled')) {
		if (!empty($_POST['deploy'])) {
			$deploy = $_POST['deploy'];
			$token = isset($deploy['token']) ? $deploy['token'] : '';
			$deploy_salt = isset($deploy['deploy_salt']) ? $deploy['deploy_salt'] : '';
			$config = json_decode(file_get_contents($maintenancefile),true);

			if ($token = md5($deploy_salt.$config['public'].$config['private'].$deploy_salt) ) {

				echo json_encode($_POST); exit();

			}else{
				AcidUrl::error403();
			}

		}
	}
}