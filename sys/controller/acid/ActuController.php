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
                        Conf::setPageTitle($actu->hscTrad('title'));
                        Conf::addToMetaKeys($actu->hscTrad('title'));
                        Conf::setMetadesc(AcidVarString::split($actu->trad('content'),100) . ' - '. Conf::getMetaDesc());

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
