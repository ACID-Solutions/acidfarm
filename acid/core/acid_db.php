<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Core
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */



/*****************************************************************************
 *
 *           Acid DB Class
 *
 *****************************************************************************/


/**
 * Utilitaire de  base de données
 * @package   Core
 *
 */
class AcidDB {

	/**
	 * @var object Instance
	 */
	private static $_db;


	// @see http://fr.php.net/manual/fr/book.pdo.php#93178

	/**
	 *  Exécute une instance SQL.
	 *
	 *
	 * @return PDO
	 */
	public static function getInstance()
	{

		if (!self::$_db) {
			Acid::log('sql','Initialazing connection ('.Acid::get('db:type').')');
			try
			{
				Acid::timerStart('db-connect');
				self::$_db = new PDO(Acid::get('db:type').
                                    ':host='.Acid::get('db:host').
                                    ';port='.Acid::get('db:port').
                                    ';dbname='.Acid::get('db:base'),
				Acid::get('db:user'), Acid::get('db:pass'));
				self::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$_db->exec("SET CHARACTER SET ".Acid::get('db:charset'));
				self::$_db->exec("SET sql_mode=".Acid::get('db:sql_mode'));

				Acid::timerStop('db-connect');
			}
			catch(PDOException $e)
			{
				if (Acid::get('debug')) {
					trigger_error('Acid : '.$e->getMessage(), E_USER_ERROR);
				} else {
					trigger_error('Une erreur de base de donnée est survenue',
					E_USER_ERROR);
				}
			}
		}
		return self::$_db;
	}

	/**
	 *  Début d'une transaction PDO.
	 *
	 *
	 *
	 */
	public static function beginTransaction()
	{
		return self::getInstance()->beginTransaction();
	}

	/**
	 *  Fin d'une transaction PDO.
	 *
	 *
	 *
	 */
	public static function commit()
	{
		return self::getInstance()->commit();
	}

	/**
	 *  Retourne le SQLSTATE associé avec la dernière opération sur la base de données.
	 *
	 *
	 * @return SQLSTATE
	 */
	public static function errorCode()
	{
		return self::getInstance()->errorCode();
	}

	/**
	 *  Retourne les informations associées à l'erreur lors de la dernière opération sur la base de données.
	 *
	 *
	 * @return array
	 */
	public static function errorInfo()
	{
		return self::getInstance()->errorInfo();
	}

	/**
	 *  Exécute une requête SQL et retourne le nombre de lignes affectées.
	 *
	 * @param object $statement
	 *
	 * @return int
	 */
	public static function exec($statement)
	{
		Acid::timerStart('db');
		Acid::log('sql','DB exec : '.$statement);
		$res = self::getInstance()->exec($statement);
		Acid::timerStop('db');
		return $res;
	}

	/**
	 *  Récupère un attribut d'une connexion à une base de données.
	 *
	 * @param mixed $attribute
	 *
	 * @return {PDO::constant | null}
	 */
	public static function getAttribute($attribute)
	{
		return self::getInstance()->getAttribute($attribute);
	}

	/**
	 *   Retourne la liste des pilotes PDO disponibles.
	 *
	 *
	 * @return array
	 */
	public static function getAvailableDrivers()
	{
		return PDO::getAvailableDrivers();
	}

	/**
	 *  Retourne l'identifiant de la dernière ligne insèrée ou la valeur d'une séquence.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public static function lastInsertId($name=null)
	{
		return self::getInstance()->lastInsertId($name);
	}

	/**
	 * Prépare une requête à l'exécution et retourne un objet.
	 *
	 * @param object $statement
	 * @param array $driverOptions
	 * @param boolean $no_log true pour desactiver le log acidfarm
	 *
	 * @return PDOStatement
	 */
	public static function prepare($statement,$driverOptions=array(),$no_log=false)
	{
		if (!$no_log) {
			Acid::log('sql','DB prepare : '.$statement);
		}
		return self::getInstance()->prepare($statement, $driverOptions);
	}

	/**
	 *  Exécute une requête SQL, retourne un jeu de résultats en tant qu'objet PDOStatement.
	 *
	 *	@param object $statement
	 *
	 * @return PDOStatement | bool
	 */
	public static function query($statement)
	{
		Acid::timerStart('db');
		Acid::log('sql','DB query : '.$statement);
		$return = self::getInstance()->query($statement);
		Acid::timerStop('db');
		return $return;
	}

	/**
	 * Protège une chaîne pour l'utiliser dans une requête SQL PDO.
	 *
	 * @param string $string
	 * @param PDO::constant $parameterType
	 *
	 * @return string
	 */
	public static function quote($string,$parameterType=PDO::PARAM_STR)
	{
		return self::getInstance()->quote($string, $parameterType);
	}

	/**
	 *  Annule une transaction.
	 *
	 *
	 * @return bool
	 */
	public static function rollBack()
	{
		return self::getInstance()->rollBack();
	}

	/**
	 * Configure un attribut PDO.
	 * @param mixed $attribute
	 * @param mixed $value
	 * @return boolean
	 */
	public static function setAttribute($attribute,$value)
	{
		return self::getInstance()->setAttribute($attribute, $value);
	}

}

