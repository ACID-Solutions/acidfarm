<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Tool
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Outil AcidUrl, Gestionnaire d'Url
 * @package   Tool
 */
class AcidUrl
{
    /**
     *  Procède à l'appel d'une erreur 403 (Accès non autorisé)
     */
    public static function error403() {
        header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
        Acid::log('url','AcidUrl::error403');

        include SITE_PATH . 'sys/pages/403.php';
        echo $GLOBALS['html'];

        include ACID_PATH . 'stop.php';
    }

    /**
     * Procède à l'appel d'une erreur 404 (Page non trouvée)
     * Appel du fichier 404
     */
    public static function error404() {
        header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
        Acid::log('url','AcidUrl::error404 ' . $_SERVER['REQUEST_URI']);

        include SITE_PATH . 'sys/pages/404.php';
    }

    /**
     * Procède à l'appel d'une erreur 503 (Service non disponible)
     * Appel du fichier 503
     */
    public static function error503() {
        header($_SERVER['SERVER_PROTOCOL'].' 503 Service Unavailable');
        Acid::log('url','AcidUrl::error503 ' . $_SERVER['REQUEST_URI']);

        include SITE_PATH . 'sys/pages/503.php';
		echo $GLOBALS['html'];

        include ACID_PATH . 'stop.php';
    }

    /**
     * Procède à l'appel d'une redirection 301 vers $url
     *
     * @param string $url
     */
    public static function redirection301($url,$max_age=86400) {
        header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
        header('Status: 301 Moved Permanently');
        if ($max_age) {
        	header("Cache-Control: max-age=".$max_age);
        }
        header('Location: '.$url);
        Acid::log('url','AcidUrl::redirection301 : ' . $url);
        include ACID_PATH . 'stop.php';
    }

    /**
     * Effectue une redirection vers $url.
     *
     * @param string $url
     */
    public static function redirection($url) {
		header('Location: '.$url);
        Acid::log('url','AcidUrl::redirection302 : ' . $url);
        include ACID_PATH . 'stop.php';
    }

 	/**
     * Redéfinit les paramètres pour une URL
     *
     * @param array $tab
     */
    public static function buildParams($tab=null) {
    	$tab = ($tab===null) ? $_GET : $tab;

    	$res = '';
    	if (count($tab)) {
    		foreach ($tab as $k => $v) {
    			$res .= $res ? '&' : '';
				$res .= $k.'='.urlencode($v);
			}
			$res = '?' . $res;
    	}
		return $res;
    }

	/**
     * Créer des champs hidden correspondant à la signature $_GET
     *
     * @param array $tab
     */
    public static function buildFields($tab=null) {
    	$tab = empty($_GET) ? array() : $_GET;

    	$res ='';
    	if (count($tab)) {
	    	foreach ($tab as $k =>$v) {
	    		$res .=  '<input type="hidden" value="'.urlencode($v).'" />';
	    	}
    	}

		return $res;
    }

    /**
     * Retourne une version normalisée pour une URL de la chaîne de carractère en entrée.
     *
     * @param string $str
     *
     * @return string
     */
    public static function normalize($str) {
        $output = $str;
        $output = str_replace( '&' , ' and ', $output);
        $output = preg_replace('`\[.*\]|`U','',$output);
        $output = htmlentities($output, ENT_COMPAT, 'utf-8');
        $output = preg_replace( '`&([a-z]+)(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', "\\1", $output );
        $output = preg_replace( array('`&(amp;)?#?[a-z0-9]+;`i', '`[^a-z0-9]`i') , '-', $output);
        $output = strtolower(trim($output, '-'));
        while (strpos($output,'--') !== false) {
            $output = str_replace('--','-',$output);
        }
        return $output;
    }

        /**
     * Retourne l'url relative si possible
     * @param url $url
     * @return mixed
     */
    public static function relative($url) {
    	if (strpos($url,Acid::get('url:system'))===0) {
			return Acid::get('url:folder').substr($url,strlen(Acid::get('url:system')));
    	}

		return $url;
    }

    /**
     * Retourne l'url relative si possible
     * @param url $url
     * @return mixed
     */
    public static function absolute($url) {
    	if (strpos($url,Acid::get('url:folder'))===0) {
    		return Acid::get('url:system').substr($url,strlen(Acid::get('url:folder')));
    	}

    	return $url;
    }

    /**
     * Retourne une URL en fonction des paramètres de $_GET, ainsi que les paramètres en entrée.
     *
     * @param array $params A ajouter.
     * @param array $without A enlever du GET.
     * @param string $url_src Si définit, on utilisera $url_src pour base, $_SERVER['REQUEST_URI'] sinon
     *
     * @return string
     */
	public static function build ($params=array(),$without=array(),$url_src=null) {

    	if ($url_src === null) {
    		$tab = $_GET;
    		$url_src = $_SERVER['REQUEST_URI'];
    	} else {
    		$tab = array();
            $url_query = str_replace('&amp;','&',$url_src);
            if(strpos('&', $url_query)!==false){
                $vals = explode('&',parse_url($url_query,PHP_URL_QUERY));
                if(is_array($vals)){
                    foreach ($vals as $val) {
                        list ($k,$v) = explode('=',$val);
                        $tab[$k] = $v;
                    }
                }
            }
    	}

    	$gets = array();

        if($tab) {
            foreach ($tab as $key => $val) {
                $gets[$key] = $key . '=' . $val;
            }
        }

        if($params) {
            foreach ($params as $key => $val) {
                $gets[$key] = $key . '=' . $val;
            }
        }

        if ($without) {
            foreach ($without as $key) {
                if (isset($gets[$key])) {
                    unset($gets[$key]);
                }
            }
        }

        $url_parsed = explode('?',$url_src);
        $next = empty($gets) ? '' : '?' . implode('&amp;',$gets);
        return $url_parsed[0] . $next;
    }

    /**
     * requestURI without allowed get params
     * @param string REQUEST_URI
     */
    public static function requestURI() {
        return AcidUrl::build($_GET,Acid::get('url:params:allowed'),$_SERVER['REQUEST_URI']);
    }

    /**
     * Détecte une URL et la retourne avec la balise <a>
     *
     * @param string $str Chaîne à vérifier.
     *
     * @return string
     */
    public static function linkurl($str) {
        return preg_replace("`http(s)?://[^<>[:space:]]+[[:alnum:]/]`","<a href=\"\\0\">\\0</a>", $str);
    }
}
