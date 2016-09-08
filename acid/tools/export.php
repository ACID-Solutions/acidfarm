<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tool
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Outils d'export
 */
class AcidExport {


	/**
	 * Effectue un export CSV d'un module
	 *
	 * @param $module Nom du module
	 * @param string $select Selection SQL
	 * @param string $filter Filtre SQL
	 * @param string $order Ordre SQL
	 * @param string $limit Limite SQL
	 * @param array $mods Liste des modules joints
	 * @param array $assoc tableau de remplacement de valeurs
	 * @param array $func tableau de fonctions pour remplacement de valeurs
	 * @param string $delimiter Délimiter CSV
	 * @param string $enclosure Enclosure CSV
	 */
	public static function sqlModule2CSV($module,$select='',$filter='',$order='',$limit='',$mods=array(),$assoc=array(),$func=array(),$delimiter=';',$enclosure='"') {

		//On peut appeler un objet en paramètres
		$mod = is_string($module) ? $module::build() : $module;

		//preparation particulières dans le cas d'une jointure
		$from = array();
		if ($mods) {

			//On inclut le module courant
			$mods_tab[$mod::getClass()] = false;
			foreach ($mods as $k=>$m) {
				$mods_tab[$k] = $m;
			}

			//On prépare le from et le potentiel select (si non défini)
            foreach ($mods_tab as $submod => $keys) {
                foreach (Acid::mod($submod)->getKeys() as $key) {
                    $new_select[] = array($key,false,Acid::mod($submod)->tblJoin(),Acid::mod($submod)->dbPref($key));
                }

                if ($keys) {
                    $my_from = array();
                    $my_from[] = $mod::tblMods($submod);
                    $my_from[] = $keys;
                    $from[] = $my_from;
                }
            }

			//Si pas de select prédéfini, on le format pour les jointures
			$select = $select ? $select : $new_select;
		}

		//génération de la requête
		$select_filter = $mod::dbGenerateSelect($select);
		$from_filter = $mod::dbGenerateFrom($from);
		$where_filter = $mod::dbGenerateFilter($filter,'AND',(!$mods));
		$order_filter = $mod::dbGenerateOrder($order);
		$limit_filter = $mod::dbGenerateLimit($limit);
		$request =  $select_filter .' '.$from_filter. ' ' .$where_filter . ' ' .$order_filter. ' ' .$limit_filter.' ';

		//nom du fichier
		$fileName = $mod->getClass().'_'.time().'.csv';

		//On prépare le document pour le stream
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header("Content-type: text/csv; charset=utf-8");
		header("Content-Disposition: attachment; filename={$fileName}");
		header("Expires: 0");
		header("Pragma: public");



		//Création d'une sortie php
		$my_output = 'php://output';
		$context = null;
		$handler = @fopen( $my_output, 'w',$context );

		//Requête SQL
		$results = AcidDB::query($request)->fetchAll(PDO::FETCH_ASSOC);

		if ($results) {

			//Les colonnes
			$head = array();
			foreach (array_keys($results[0]) as $key) {
				$head[] = $mod->getLabel($key);
			}
			fputcsv($handler, $head,$delimiter,$enclosure);

			//Si on a des valeurs fichiers
			$files_keys = array_keys($mod->getUploadVars());
			$date_keys = array_keys($mod->getVarsByType(array('AcidVarDate','AcidVarDateTime')));

			foreach ( $results as $data ) {

				ob_flush();
				flush();

				//On retravaille certaines valeurs pour plus de cohérence
				if ($files_keys) {
					foreach ($files_keys as $key) {
						$skey = isset($data[$key]) ? $key : (isset($data[$mod::dbPref($key)]) ? $mod::dbPref($key) : false);
						if ($skey) {
							if ($data[$skey]) {
								$data[$skey] = Acid::get('url:system').$data[$skey];
							}
						}
					}
				}

				//On retravaille les dates pour plus de lisibilité
				if ($date_keys) {
					foreach ($date_keys as $key) {
						$skey = isset($data[$key]) ? $key : (isset($data[$mod::dbPref($key)]) ? $mod::dbPref($key) : false);
						if ($skey) {
							if ($data[$skey]) {
								$data[$skey] = AcidTime::conv($data[$skey]);
							}
						}
					}
				}

				//Si des valeurs associatives sont renseignée, on les utilise
				if ($assoc) {
					foreach ($assoc as $key => $subassoc) {
						if (isset($data[$key])) {
							if (isset($subassoc[$data[$key]])) {
								$data[$key] = $subassoc[$data[$key]];
							}
						}
					}
				}

                //Si des valeurs doivent être générées par une function
                if ($func) {
                    foreach ($func as $key => $subfunc) {
                        if (isset($data[$key])) {
                            $name = isset($subfunc[0]) ? $subfunc[0] : '';
                            $args = isset($subfunc[1]) ? $subfunc[1] : array();
                            if ($name) {
                                if ($args) {
                                    foreach ($args as $k=>$a) {
                                        if ($a == '__VAL__') {
                                            $args[$k] = $data[$key];
                                        }elseif ($a == '__ELT__') {
                                            $args[$k] = $data;
                                        }
                                    }
                                }
                                $data[$key] = call_user_func_array($name, $args);
                            }
                        }
                    }
                }


				fputcsv($handler, $data, $delimiter, $enclosure);
			}

		}else{
			$subselect = ($select && is_array($select)) ?  $select : ($select ? array() : $mod->getKeys());

			$head = array();
			foreach ($subselect as $key) {
				$skey = is_array($key) ? $key[0] : $key;
				$head[] = $mod->getLabel($skey);
			}
			fputcsv($handler, $head,$delimiter,$enclosure);

		}

		fclose($handler);
		exit;

	}

}