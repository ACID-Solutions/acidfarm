<?php
/**
 * upload.php
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

#!! IMPORTANT: 
#!! this file is just an example, it doesn't incorporate any security checks and 
#!! is not recommended to be used in production environment as it is. Be sure to 
#!! revise it and customize to your needs.


// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$_POST['dontreload'] = 1;
$acid_page_type = 'upload';

include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/sys/glue.php';


if (!User::curLevel()) {
	if (Acid::get('plupload:restriction')=='logged') {
		Acid::log('hack','Uploading file without rights');
		exit();
	}
}

Acid::log('UPLOAD','User instance for uploading file is '.User::curUser()->getId());

function logAndDie($msg) {
	Acid::log('PLUPLOAD',$msg);
	die($msg);
}

// Uncomment this one to fake upload
/*
// Support CORS
header("Access-Control-Allow-Origin: *");
// other CORS headers if any...
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	exit; // finish preflight CORS requests here
}
*/

// 5 minutes execution time
//@set_time_limit(5 * 60);
$max_time = Acid::get('plupload:session_time');
@set_time_limit($max_time);


// Uncomment this one to fake upload time
if (Acid::get('plupload:fake_latence')) {
	usleep(Acid::get('plupload:fake_latence')*1000000);
}

// Settings
//$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
$targetDir = SITE_PATH . Acid::get('path:tmp');

//$targetDir = 'uploads';
$cleanupTargetDir = true; // Remove old files
//$maxFileAge = 5 * 3600; // Temp file age in seconds
$maxFileAge =Acid::get('plupload:file_tmp_age'); // Temp file age in seconds


// Create target dir
if (!file_exists($targetDir)) {
	@mkdir($targetDir);
}

// Get a file name
if (isset($_REQUEST["name"])) {
	$fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
	$fileName = $_FILES["file"]["name"];
} else {
	$fileName = uniqid("file_");
}

if ($extension = AcidFs::getExtension($fileName)) {
	if (in_array($extension, Acid::get('ext:files'))) {

		//$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
		$filePath = $targetDir . $fileName;

		// Chunking might be enabled
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

		Acid::log('UPLOAD', 'file : ' . $fileName . ', chunk : ' . $chunk . ', chunks : ' . $chunks);

		// Remove old temp files
		if ($cleanupTargetDir) {
			if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
				logAndDie('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
			}

			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

				// If temp file is current file proceed to the next
				if ($tmpfilePath == "{$filePath}.part") {
					continue;
				}

				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
					@unlink($tmpfilePath);
				}
			}
			closedir($dir);
		}


		// Open temp file
		if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
			logAndDie('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}

		if (!empty($_FILES)) {
			if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
				logAndDie('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			}

			// Read binary input stream and append it to temp file
			if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
				logAndDie('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		} else {
			if (!$in = @fopen("php://input", "rb")) {
				logAndDie('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
			}
		}

		while ($buff = fread($in, 4096)) {
			fwrite($out, $buff);
		}

		@fclose($out);
		@fclose($in);

		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);
		}

		// Return Success JSON-RPC response
		logAndDie('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

	}
}

logAndDie('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Permission denied."}, "id" : "id"}');