<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur des Actualités
 * @package   Controller
 */
class ActuController {

	/**
	 *  Liste les actualités si aucun argument ou un numéro de page est donné à l'url, sinon affiche l'actualité ciblée par l'url
	 */
    public function index(){
  		Conf::addToAriane(AcidRouter::getName('news'),Actu::buildUrl());

         if((AcidRouter::getPartialParamById(0)===AcidRouter::getKey('pagination_key'))||count(AcidRouter::getPartialParams())==0){

         	$page = 1;
             if(AcidRouter::getPartialParamById(1)&&ctype_digit(AcidRouter::getPartialParamById(1))){
                 $page = AcidRouter::getPartialParamById(1);
             }

             // SEO settings
//              $title = array(
//              	'fr'=>'Actualités',
// 					'en'=>'News',
// 					'es'=>'Noticias',
// 					'it'=>'Notizie',
// 					'de'=>'Aktualitäten'
//              );
//              $desc = array(
//              		'fr' => 'Suivez toute l\'actualités de '.Acid::get('site:name').' et restez informés de nos actions.',
//              		'en' => 'Follow all the news '.Acid::get('site:name').' and stay informed of our actions.',
//              		'es' => 'Siga todas las noticias de '.Acid::get('site:name').' y mantente informado de nuestras acciones.',
//              		'it' => 'Segui tutte le notizie '.Acid::get('site:name').' e rimanere informati delle nostre azioni.',
//              		'de' => 'Befolgen Sie alle Nachrichten '.Acid::get('site:name').' und bleiben Sie informiert unseres Handelns.'
//              );
//              $meta_img_url = Acid::get('url:img').'site/logo.png';
//              $use_default_kewords = true;
//              $added_keywords = array('keyword', 'ajouté');
//              $generate_keywords_from_text = 'ceci est une chaine de caractère test que l\'on ajoute manuellement pour faire le test et voir ce qui est retenu. Ici, on peut voir qu\'un mot présent deux fois est retenu. Tout mot indésirable est à ajouter dans le fichier "kw_excluded.txt"';
//              Conf::SEOGen($title, $desc, $meta_img_url, $use_default_kewords, $added_keywords, $generate_keywords_from_text);

             //add to HTML
             Conf::addToContent(Actu::printList($page));

        } else {
             if (count(AcidRouter::getPartialParams()) <= 2) {
                $page = null;
                if(AcidRouter::getPartialParamById(0)&&ctype_digit(AcidRouter::getPartialParamById(0))){
                     $page = AcidRouter::getPartialParamById(0);
                }

                $actu = new Actu($page);
                Acid::set('tmp_current_object',$actu);

                if ($actu->getId()) {
                    if ($actu->get('active')) {

                        if ($_SERVER['REQUEST_URI'] != $actu->url()) {
                            AcidUrl::redirection301($actu->url());
                        }

                        //add to ariane
                        Conf::addToAriane($actu->trad('title'),$actu->url());

                        //set meta tags
                        Conf::setPageTitle($actu->trad('seo_title') ? $actu->hscTrad('seo_title') : $actu->hscTrad('title'));
                        Conf::addToMetaKeys($actu->trad('seo_keys') ? explode(',',$actu->trad('seo_keys')) : $actu->hscTrad('title'));
                        Conf::setMetadesc($actu->trad('seo_desc') ? $actu->hscTrad('seo_desc')  : (AcidVarString::split($actu->trad('content'),100) . ' - '. Conf::getMetaDesc()));

                        //add to HTML
                        Conf::addToContent($actu->printActu());
                    }
                }
            }else{
                AcidUrl::error404();
            }
        }
    }


    /**
	 *  Liste les actualités
	 */
    public function accueil(){
        //add to HTML
        Conf::addToContent(Actu::printList(0));
    }

}
