<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Model / View
 * @version   0.1
 * @since     Version 0.7
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

require Acid::outPath('functions-core.php');

class Func extends FuncCore {

    public static function getMenu() {
        if (Acid::get('out')=='siteadmin') {
            $config = Acid::get('admin:menu_config');
            $menu_elts = array();

            foreach ($config['siteadmin_cat'] as $key => $cat) {
                if (User::curLevel(Lib::getIn('level',$cat,0))) {
                    $menu_elts[$key] = array('url'=>AcidUrl::build(array('page'=>$key)),'name'=>Lib::getIn('label',$cat),'unclickable'=>Lib::getIn('unclickable',$cat));
                    if ($elts = Lib::getIn('elts',$cat)) {
                        foreach ($elts as $skk => $sk)
                        {

                            $selt = Acid::get('admin:menu_config:controller:' . $sk);
                            if (User::curLevel(Lib::getIn('level',$selt,0))) {
                                $menu_elts[$key]['elts'][$sk] = array('url' => AcidUrl::build(array('page' => $sk)), 'name' => Lib::getIn('label', $selt));
                            }
                        }

                    }
                }
            }

            return Acid::tpl('admin/menu.tpl',array('elts'=>$menu_elts),User::curUser());
        }

        return parent::getMenu();
    }

}