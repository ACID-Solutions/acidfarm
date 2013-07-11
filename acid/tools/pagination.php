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
 * Outil AcidPagination, Gestionnaire de pagination.
 * @package   Tool
 */
class AcidPagination
{
	
	/**
	 * Retourne le numéro de page après l'avoir soumis aux contraintes de la pagination.
	 *
	 * @param int $page
	 * @param int $nb_elts
	 * @param int $nb_elts_page
	 * 
	 * @return int
	 */
	public static function getPage($page,$nb_elts,$nb_elts_page) {
		
		$page = (int) $page;
		
		$last_page = ceil($nb_elts/$nb_elts_page);
		
		if ($page > $last_page) {
			$page = $last_page;
		}
		
		if ($page < 1) {
			$page = 1;
		}
		return $page;
	}
	
	/**
	 * Retourne une chaine SQL à placer après LIMIT
	 *
	 * @param int $page
	 * @param int $nb_elts_page
	 *
	 * @return string
	 */
	public static function getLimitSQL($page,$nb_elts_per_page) {
		return ($page-1)*$nb_elts_per_page.', '.$nb_elts_per_page;
	}
	
	/**
	 * Retourne un navigateur de pagination.
	 *
	 *
	 * @param int $page Page courante.
	 * @param int $nb_elts Nombre d'éléments au total.
	 * @param int $nb_elts_page Nombre d'éléments par page.
	 * @param string $tpl chemin du tpl à utiliser si défini
	 * @param array $config
	 * 		  =>string $link_start Prefixe URL .
	 * 		  =>string $link_stop Suffixe URL.
	 * 		  =>string $link_middle Milieu d'URL.
	 * 		  =>int $navigation_max_pages Nombre de liens de navigation.
	 * 		  =>bool $unlink_current True si lien sur la page courante.
	 * 		  =>string $css_class Classe CSS. 
	 * 
	 * @return string
	 */
	public static function getNav($page,$nb_elts,$nb_elts_page,$tpl=null,$config=array()) {
		
		//PAGE URL FUNCTION
		$link_start=isset($config['link_start']) ? $config['link_start'] : '';
		$link_stop=isset($config['link_stop']) ? $config['link_stop'] : '';
		$link_middle=isset($config['link_middle']) ? $config['link_middle'] : '-page-';
		$force_one=isset($config['force_one']) ? $config['force_one'] : false;
		
		$def_function = array('func'=>'AcidPagination::getUrl','args'=>array('__PAGE__',$link_start,$link_stop,$link_middle,$force_one));
		$link_func=isset($config['link_func']) ? $config['link_func'] : $def_function;
		
		//CUSTOM PARAMS
		$navigation_max_pages=isset($config['navigation_max_pages'])  ? $config['navigation_max_pages'] : null;
		$unlink_current=isset($config['unlink_current']) ? $config['unlink_current'] : true;
		$css_class=isset($config['css_class']) ? $config['css_class'] : '';
		$sep=isset($config['sep']) ? $config['sep'] : '...';
		
		if ($navigation_max_pages === null) {
			$navigation_max_pages = Acid::get('pagination:max_nav_pages');
		}
		
		//CUR PAGE
		$page = self::getPage($page,$nb_elts,$nb_elts_page);
		
		//LAST PAGE
		$last_page = ceil($nb_elts/$nb_elts_page);
		
		//MAX PAGE
		if ($last_page > $navigation_max_pages) {
			$max_pages = $navigation_max_pages;
		}else{
			$max_pages = $last_page;
		}
		
		
		//CUR LAST PAGE
		$middle = ceil($max_pages / 2);
		
		if ($page < $middle || $max_pages == $last_page) {
			$start = 1;
			$cur_last_page = $navigation_max_pages;
		}elseif ($page > ($last_page-$middle)) {
			$start = $last_page - $navigation_max_pages +1 ;
			$cur_last_page = $start + $navigation_max_pages -1;
		}else {
			$start = $page - $middle + 1;
			$cur_last_page = $start + $navigation_max_pages -1;
		}
		
		
		//RESULT
		
		$output = '';
		$p=$start;
		$page_elts = array();
		
		
		if ($last_page > 1) {
			
			//TAB
			if ($p != 1) {
				$page_elts[] = array('url'=>self::genUrl(1,$link_func),'label'=>1,'page'=>1,'type'=>'first');
				if ($p > 2) {
					$page_elts[] = array('url'=>null,'label'=>$sep,'page'=>null,'class'=>'navigation_points','type'=>'sep');					
				}
			}
			
			while ( ($p <= $cur_last_page) && ($p <= $last_page) ) {
					
				$selected = ($p == $page && $unlink_current) ? 'navigation_selected' : 'navigation_unselected';
				$type = ($page == $p && $unlink_current) ? 'unlinked' : 'linked';
				$url = ($page == $p && $unlink_current) ? null : self::genUrl($p,$link_func);
				$page_elts[] = array('url'=>$url,'label'=>$p,'page'=>$p,'class'=>$selected,'type'=>$type);	
				$p++;
			}
			
			if ($p <= $last_page) {
				$selected = ($p == $page && $unlink_current) ? 'navigation_selected' : 'navigation_unselected';
				if ($p != $last_page) {
					$page_elts[] = array('page'=>null,'label'=>$sep,'page'=>null,'class'=>'navigation_points','type'=>'sep');
				}
				$page_elts[] = array('url'=>self::genUrl($last_page,$link_func),'label'=>$last_page,'page'=>$last_page,'class'=>$selected,'type'=>'last');
			}
			
			//TEMPLATE
			if ($tpl) {
				$vars = array(
							'page_elts'				=> $page_elts,
							'page'					=> $page,
							'last_page'				=> $last_page,
							'max_pages'				=> $max_pages,
							'cur_last_page'			=> $cur_last_page,
							'start_page'			=> $start,
							'navigation_max_pages'	=> $navigation_max_pages,
							'link_func'				=> $link_func,
							'config'				=> $config
						);
						
				$output .=  Acid::tpl($tpl,$vars);
				
			//DEFAULT TEMPLATE
			}else{
				
				foreach ($page_elts as $elt) {
					$url = isset($elt['url']) ? $elt['url'] : '';
					$label =  isset($elt['label']) ? $elt['label'] : '';
					$class = isset($elt['class']) ? ' class="'.$elt['class'].'" ' : '';
					$output .=  '<span>'.
								($url ? 
									   ('<a href="'.$url.'" '.$class.'>'.$label.'</a>')
									   : 
									   ('<span '.$class.'>'.$label.'</span>')
								) . 
								'</span>';
				}
				
				if ($output) {
					$output =	'<div class="navigation'.(empty($css_class)?'':' '.$css_class).'">' . "\n" . 
								$output . 
								'</div>' . "\n";
				}
				
			}
			
			return 	$output;
		}else{
			return '';
		}
	}
	
	/**
	 * Génère l'url de pagination
	 * @param int $page page pour l'url
	 * @param function $fun fonction pour l'url
	 * @return mixed
	 */
	public static function genUrl($page,$fun) {
		foreach ($fun['args'] as $key=>$val) {
			if ($val == '__PAGE__') {
				$fun['args'][$key] = $page;
			}
		}
		
		return call_user_func_array($fun['func'],$fun['args']);
	}
	
	/**
	 * Retourne une url de pagination par défaut
	 * @param int $page 
	 * @param string $link_start
	 * @param string $link_stop
	 * @param string $link_middle
	 * @param boolean $force_one
	 * @return string
	 */
	public static function getUrl($page,$link_start,$link_stop,$link_middle,$force_one) {
		if (($page == 1) && (!$force_one)) {
			return $link_start . $link_stop;
		}else{
			return $link_start . $link_middle . $page. $link_stop;
		}
	}
	
}
