<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\ModelView
 * @version   0.1
 * @since     Version 0.3
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Librairies de fonctions liées au thème du site
 * @package   Acidfarm\ModelView
 */
class FuncCore {

    /**
     * Retourne les drapeaux de langue en HTML
     * @return string
     */
    public static function getFlags() {
        return Acid::tpl('tools/flags.tpl');
    }

    /**
     * Retourne le menu du site en HTML en mode figé
     * @return string
     */
    public static function getMenuSample() {
        $elts = array();

        //Start
        $elts['index'] = array('url'=>Route::buildUrl(),'name'=>AcidRouter::getName('index'));
        $elts['news'] = array('url'=>Actu::buildUrl(),'name'=>AcidRouter::getName('news'));
        $elts['gallery'] = array('url'=>Photo::buildUrl(),'name'=>AcidRouter::getName('gallery'));

        //Middle

        //--getting pages using session
        //$pages = AcidSession::tmpGet('active_pages');
        //if ($pages === null) {
        $pages = Page::dbList(array(array('active','=',1)));
        //AcidSession::tmpSet('active_pages',$pages,600);
        //}

        //--adding pages to menu
        foreach ($pages as $key =>$elt) {
            $p = new Page($elt);
            $elts[$elt[$p->langKey('ident','default')]] = array('url'=>$p->url(),'name'=>$p->hscTrad('title'));
        }

        //Stop
        $elts['contact'] = array('url'=>Route::buildUrl('contact'),'name'=>AcidRouter::getName('contact'));
        $elts['search'] = array('url'=>Route::buildUrl('search'),'name'=>AcidRouter::getName('search'));
        $elts['siteadmin.php'] = array('url'=>Route::buildUrl('admin'),'name'=>'Admin');

        $vars = array('elts' => $elts);
        return Acid::tpl('menu.tpl',$vars);
    }

    /**
     * Retourne le menu du site en HTML
     * @return string
     */
    public static function getMenu() {

        return static::getMenuSample();

       /*
        $elts = array();

        if ($menus = Menu::dbList(array(array('active','=',1)),array('pos'=>'ASC'))) {
            foreach ($menus as $key =>$elt) {
                $m = new menu($elt);
                $elts[$m->getId()] = array('url'=>$m->link(),'name'=>$m->name());
            }
        }

        $vars = array('elts' => $elts);
        return Acid::tpl('menu.tpl',$vars);
       */
    }

    /**
     * Retourne l'ariane en HTML à partir du tableau en entrée
     * @param array $ariane array('nameparent'=>'urlparent','namechild'=>'urlchild','namesubchild'=>'urlsubchild')
     * @return string
     */
    public static function getAriane($ariane) {
        global $acid;

        $l = '›';

        if (empty($ariane)) {
            $output = Acid::get('site:name');
        } elseif (is_array($ariane)) {
            $output = '<a href="'.Acid::get('url:system_lang').'" itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement">'.
                            '<span itemprop="name">'.Acid::get('site:name').'</span>'.
                            '<meta content="'.Acid::get('url:system_lang').'" itemprop="url">'.
                       '</a>';
            $deep = count($ariane);
            $i=1;
            foreach ($ariane as $page=>$url) {
                $_url = (!empty($url['url']))? $url['url']:null;
                $_name = (!empty($url['name']))? $url['name']:$page;
                $_key = $page;

                if ($i != $deep) {
                    if ($_url) {
                        $output .=  ' '.$l.'<a href="'.$_url.'" class="ariane_\'.$i.\'" itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement">'.
                            '<span itemprop="name">'.htmlspecialchars($_name).'</span>'.
                            '<meta content="'.$_url.'" itemprop="url">'.
                            '</a>';

                    }else{
                        $output .=  ' '.$l.' '.
                            '<span itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement">'.
                            '   <span itemprop="name">'.htmlspecialchars($_name).'</span>'.
                            '</span>';
                    }
                } else {
                    $output .=  ' '.$l.' '.
                        '<span itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement">'.
                        '   <span itemprop="name">'.htmlspecialchars($_name).'</span>'.
                        '</span>';
                }
                $i ++;
            }
        }



        return	'<div class="ariane" itemscope itemtype="http://schema.org/BreadcrumbList" >' . "\n" .
        $output .
        '</div>' . "\n";

    }

    /**
     * Appelle un picto en HTML
     * @param string $src url de l'image
     * @param string $alt texte alternatif
     * @param string $title titre
     * @param array $attrs attributs
     * @return string
     */
    public static function callPicto($src=null,$alt='',$title='',$attrs=array()) {
        $attrs['class'] = isset($attrs['class']) ? 'picto '.$attrs['class'] : 'picto';
        return self::callImg($src,$alt,$title,$attrs);
    }

    /**
     * Appelle une image en HTML
     * @param string $src url de l'image
     * @param string $alt texte alternatif
     * @param string $title titre
     * @param array $attrs attributs
     * @return string
     */
    public static function callImg($src=null,$alt='',$title='',$attrs=array()) {
        if ($title) {
            $attrs['title'] = $title;
        }

        $attrs['alt'] = $alt ? $alt : '';

        if (is_array($src)) {
            $roll = isset($src[1]) ?  $src[1] : '';
            $src = isset($src[0]) ?  $src[0] : '';
            if (is_float($roll)) {
                $rolling = "$(this).css('opacity',".$roll."); $(this).css('filter','alpha(opacity=".round($roll*100).")');";
                $rolling_off = "$(this).css('opacity',1); $(this).css('filter','alpha(opacity=100)');";
            }else{
                $rolling = "this.src='".$roll."';";
                $rolling_off = "this.src='".$src."';";
            }
            $attrs['onmouseover'] = isset($attrs['onmouseover']) ? $rolling.$attrs['onmouseover'] : $rolling;
            $attrs['onmouseout'] = isset($attrs['onmouseout']) ? $rolling_off.$attrs['onmouseout'] : $rolling_off;
        }

        $attrs['src'] = $src;


        $vars = array('attrs'=>$attrs);

        return Acid::tpl('tools/img.tpl',$vars);
    }
}



?>