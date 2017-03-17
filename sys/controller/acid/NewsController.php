<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur des Actualités
 * @package   Acidfarm\Controller
 */
class NewsController {

	/**
	 *  Liste les actualités si aucun argument ou un numéro de page est donné à l'url, sinon affiche l'actualité ciblée par l'url
	 */
    public function index(){
  		Conf::addToAriane(AcidRouter::getName('news'),News::buildUrl());

        //On autorise l'affichage d'une actualité
        $enable_news_view = true;

        //Si l'url correspond à la route de la liste des actualités
        if((AcidRouter::getPartialParamById(0)===AcidRouter::getKey('pagination_key'))||count(AcidRouter::getPartialParams())==0){

            $page = 1;
            if(AcidRouter::getPartialParamById(1)&&ctype_digit(AcidRouter::getPartialParamById(1))){
                 $page = AcidRouter::getPartialParamById(1);
            }

            if (count(AcidRouter::getPartialParams()) && ($page==1)) {
                AcidUrl::redirection301(News::buildUrlList(1));
            }

            if (News::validatePageList($page)) {

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

             //Conf::setCanonicalUrl(AcidUrl::absolute(News::buildUrlList()));



                if ($prev_url = News::buildUrlListPrev($page)) {
                    Conf::setPrevUrl(AcidUrl::absolute($prev_url));
                }

                if ($next_url = News::buildUrlListNext($page)) {
                    Conf::setNextUrl(AcidUrl::absolute($next_url));
                }

                //add to HTML
                Conf::addToContent(News::printList($page));

            }else{
                AcidUrl::error404();
            }

        }
        //sinon
        else {

            //Si activée, on passe le relai à la méthode d'affichage d'une actualité
            if ($enable_news_view && count(AcidRouter::getPartialParams()) <= 2) {
                return $this->news();
            }

            AcidUrl::error404();

        }
    }

    /**
     *  Affichage d'une actualité
     */
    public function news(){
        $id_news = AcidRouter::getParam('id_news');

        if (!$id_news) {
            if (AcidRouter::getPartialParamById(0) && ctype_digit(AcidRouter::getPartialParamById(0))) {
                $id_news = AcidRouter::getPartialParamById(0);
            }
        }

        $news = new News($id_news);
        Acid::set('tmp_current_object',$news);

        if ($news->getId() && $news->active()) {

            if (AcidUrl::requestURI() != $news->url()) {
                AcidUrl::redirection301($news->url());
            }

            //add to ariane
            Conf::addToAriane($news->trad('title'),$news->url());

            //set meta tags
            Conf::setPageTitle($news->trad('seo_title') ? $news->hscTrad('seo_title') : $news->hscTrad('title'));
            Conf::addToMetaKeys($news->trad('seo_keys') ? explode(',',$news->trad('seo_keys')) : $news->hscTrad('title'));
            Conf::setMetadesc($news->trad('seo_desc') ? $news->hscTrad('seo_desc')  : (AcidVarString::split($news->trad('content'),100) . ' - '. Conf::getMetaDesc()));
            if ($news->get('src')) {
                Conf::setMetaImage($news->urlSrc('diapo'));
            }

            //add to HTML
            Conf::addToContent($news->printNews());

        }else{
            AcidUrl::error404();
        }
    }

    /**
	 *  Liste les actualités
	 */
    public function accueil(){
        //add to HTML
        Conf::addToContent(News::printList(0));
    }

}
