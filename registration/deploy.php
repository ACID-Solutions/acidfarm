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

$acid_page_type = 'rest';
include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../sys/glue.php';



$maintenancefile = AcidRegistration::file();

//si on est enregistré
if (AcidRegistration::datas('allowed')) {

    //Si le mode deploy est actif
	if (Acid::get('deploy:allowed')) {

        //s'il y a un deploy
		if (!empty($_POST['deploy'])) {
			$deploy = $_POST;
			$token = urldecode(isset($deploy['token']) ? $deploy['token'] : '');
			$deploy_salt = urldecode(isset($deploy['deploy_salt']) ? $deploy['deploy_salt'] : '');
			$config = AcidRegistration::datas();

            $result = array();

            //si les tokens de sécurité sont ok
			if ($token == md5($deploy_salt.$config['public'].$config['private'].$deploy_salt) ) {

                //Si on a soummis des fichiers
                if ($files = Lib::getIn('files',$deploy)) {

                    //on créer le doccier de backup
                    if (!file_exists(AcidRegistration::backupPath())) {
                        mkdir(AcidRegistration::backupPath());
                    }

                    //le backup sera dans un dossier nommé en fonction timestamp courant
                    $bkpath = AcidRegistration::backupPath().time().'/';
                    mkdir($bkpath);

                    $bkpostpath = AcidRegistration::backupPath().time().'/post'.time().'/';
                    mkdir($bkpostpath);

                    //pour chaque fichier soummis
                    foreach ($files as $file) {

                        $info = '';
                        $postfile = '';

                        //si un chemin est défini
                        if ($path=urldecode(Lib::getIn('path',$file))) {

                            //preparation du backup
                            $bkfilepath = $bkpath.$path;
                            $bkfiledirpath = dirname($bkfilepath);
                            $bkfilepostpath = $bkpostpath.$path;
                            $bkfiledirpostpath = dirname($bkfilepostpath);
                            $destfilepath = SITE_PATH.$path;
                            $destfiledirpath = dirname($destfilepath);

                            //backup
                            if (file_exists($destfilepath)) {

                                //preparation du dossier de backup
                                if (!file_exists($bkfiledirpath)) {
                                    mkdir($bkfiledirpath,0777,true);
                                }

                                //copie du fichier
                                @copy($destfilepath, $bkfilepath);
                            }

                            //preparation du dossier de reception de la copie
                            if (!file_exists($bkfiledirpostpath)) {
                                mkdir($bkfiledirpostpath,0777,true);
                            }

                            //préparation du dossier de reception
                            if (!file_exists($destfiledirpath)) {
                                mkdir($destfiledirpath,0777,true);
                            }

                            //s'il y a un fichier en base64
                            if (isset($file['file'])) {
                                $postfile = base64_decode(urldecode(Lib::getIn('file',$file)));
                                file_put_contents($bkfilepostpath,$postfile);

                                //s'il n'existe pas ou que le mode est override
                               if ( (!file_exists($destfilepath)) || Lib::getIn('override',$file) ) {
                                   //création du fichier
                                   file_put_contents($destfilepath,$postfile);
                               }else{
                                   $info .= ' (skipped)';
                               }
                            }

                            $result[] = $destfilepath;
                        }
                    }
                }

				file_put_contents($bkpath.'bk'.time().'.json',json_encode($_POST));
                AcidMail::send(Acid::get('site:name'),Acid::get('site:email'),Acid::get('admin:email'),'Mise à jour automatique '.Acid::get('site:name'),implode("\n",$result));

			}else{
				AcidUrl::error403();
			}

		}
	}
}