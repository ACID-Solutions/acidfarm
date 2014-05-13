<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  Acidfarm
 * @package   Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur des Recherches
 * @package   Controller
 */

class SearchController {

	/**
	 * Affiche la page des résultats de recherche
	 */
    public function index(){

        Conf::addToAriane(AcidRouter::getName('search'),Acid::get('url:folder_lang').AcidRouter::getParamById(0));

        $my_search = (AcidRouter::getPartialParamById(0)) ?AcidRouter::getPartialParamById(0)  : null;
        $allsearch = AcidRouter::getPartialParams();
        if(!empty($allsearch)) {
        	$first = true;
        	foreach($allsearch as $string) {
        		if(!$first)
        			$my_search .= "/".$string;
        		$first = false;
        	}
        }
        $my_search = urldecode($my_search);
        $my_search = addslashes($my_search);
        $my_search = htmlspecialchars($my_search);

        $s_modules = array(
                        'Page'=>array('fields'=>array('content','title'),'cond'=>"`active`='1'",'title_field'=>'title','head'=>AcidRouter::getName('page')),
                        'Actu'=>array('fields'=>array('content','head','title'),'cond'=>"`active`='1'",'title_field'=>'title','head'=>AcidRouter::getName('news'))
                    );

        $content = '';

        if ($my_search) {
            foreach ($s_modules as $mod => $config) {
                $req = '';
                $sub_content = '';

                foreach ($config['fields'] as $field) {
                    $req .= $req ? ' OR ' : '';
                    $req .= "`".Acid::mod($mod)->langKey($field)."` LIKE '%".$my_search."%' ";
                }

                $requete =  "SELECT * FROM ".Acid::mod($mod)->tbl()." WHERE ".
                            ( (!empty($config['cond'])) ? $config['cond'] . ' AND ' : '' ) .
                            '( '.($req ? $req : '1').' )';

                $res = AcidDB::query($requete)->fetchAll(PDO::FETCH_ASSOC);
                foreach ($res as $elt) {
                    $my_mod = new $mod();
                    $my_mod->initVars($elt);
                    $sub_content .= '<li><a href="'.$my_mod->url().'">'.$my_mod->trad($config['title_field']).'</a></li>'. "\n" ;
                }
                if ($sub_content) {
                    $content .=     '<h2>'.$config['head'].'</h2>'. "\n" .
                                    '<ul>'.$sub_content.'</ul>' . "\n" ;
                }

            }
        }

        if ($my_search) {
            if ($content) {
                Conf::addToContent($content);
            }else{
                Conf::addToContent(Acid::trad('no_result_found_for',array('__SEARCH__'=> stripslashes($my_search))));
            }
        }else{
             Conf::addToContent(Acid::tpl('forms/search_form.tpl'));
        }
    }
}

