<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Core
 * @version   0.4
 * @since     Version 0.4
 * @copyright 2012 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Interface de Routage
 * @package   Acidfarm\Core
 */
 interface Acid_Router_Interface{
     
     /**
      * Renvoi vers le controller/action si match
      * Sinon Renvoi une erreur
      * @param Request = URI de requete soumis.
      */
     public static function route($request);
     
     /**
      * Défini les parametres à injecté lors de l'appel 
      * @param array $params Array(name=>value)
      */
     public static function setParams($params=array());
     
     /** 
      * Défini un parametre à injecté lors de l'appel 
      * @param string $name nom du parametre
      * @param $value valeur du parametre
      */
     public static function setParam($name,$value);
     
     /**
      * Renvoi un tableau des parametres URI
      * array(key=>value)
      */
     public static function getParams();
     
     /**
      * Retourne la valeur sinon une Exception
      * @param string $name = nom du parametre
      */
     public static function getParam($name);
     
     /**
      * Retourne la valeur sinon une Exception
      * @param $id = position du parametre voulu
      */
     public static function getParamById($id);
     
     /**
      * Si un name est entré, suppression et clear du parametre URI
      * Sinon Clear de tous les parametres
      * @param $name
      */
     public static function clearParams($name=null);
 }
