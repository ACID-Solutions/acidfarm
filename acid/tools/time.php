<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tool
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */



/**
 * Outil AcidTime, Gestionnaire d'heure et date.
 * @package   Acidfarm\Tool
 */
class AcidTime {



	/**
	 * Retourne $from sous forme d'une date formatée
	 *
	 * @param string $from Texte à convertir.
	 * @param string $to Formatage
	 * @param string $if_null valeur à retourner si la date est considérée comme nulle
	 *
	 * @return string
	 */
	public static function conv($from,$to=null,$if_null='') {
		if ($to === null) {
			if (strlen($from) == 10) {
				$to = Acid::get('date_format:date','lang');
			} else {
				$to = Acid::get('date_format:datetime_small','lang');
			}
		}

		$d = strtotime($from);

		if (($d!=strtotime('')) && ($d!=strtotime('0000-00-00')) && ($d!=strtotime('0000-00-00 00:00:00')) ) {
			return date($to,$d);
		}

		return $if_null;
	}

	/**
	 * Retourne le jour renseigné par la date en entrée.
	 *
	 * @param string $date
	 * @param boolean $small
	 * @return string
	 */
	public static function dayName($date,$small=false) {
		$day = date('N',strtotime($date));
		$key = $small ? 'day_s' : 'day';
		return Acid::get('date:'.$key.':'.($day%7),'lang');
	}

	/**
	 * Retourne le mois renseigné par la date en entrée.
	 *
	 * @param string $date
	 * @param boolean $small
	 * @return string
	 */
	public static function monthName($date,$small=false) {
		$month = date('m',strtotime($date));
		$key = $small ? 'month_s' : 'month';
		return Acid::get('date:'.$key.':'.((int)$month),'lang');
	}

	/**
	 * Retourne une version formatée de la date en entrée.
	 *
	 * @param string $date
	 *
	 * @return string
	 */
	public static function dateName($date) {
		if ($date != '0000-00-00') {
			$parse = explode(' ',$date);
			list($year,$month,$day) = explode('-',$parse[0]);
			return AcidTime::dayName($date).' '.$day.' '.AcidTime::monthName($date).' '.$year;
		} else {
			return '00/00/00';
		}
	}

	/**
	 * Retourne la différence entre deux dates.
	 *
	 * @param string $date_a
	 * @param string $date_b
	 * @param string $format
	 * @param bool	 $need_negativity
	 * @param bool	 $no_reduce_day
	 *
	 * @return int
	 */
	public static function dateDiff ($date_a=null, $date_b=null, $format = 'day',$need_negativity=false,$no_reduce_day=false) {

		$f = 'day';

		$i = 0;


		$t['day'] = 60*60*24;
		$t['hour'] = 60*60;
		$t['minute'] = 60;
		$t['second'] = 1;



		$date_a = ($date_a!==null) ? $date_a : 'now';
		$date_b = ($date_b!==null) ? $date_b : 'now';

		$res = 0;

		$f = $format;
		if (isset($t[$f])) {
			if($no_reduce_day){
				$res = round((strtotime($date_a) - strtotime($date_b))/($t[$f]));
			}else{
				$res = round((strtotime($date_a) - strtotime($date_b))/($t[$f])-1);
			}
		}


		return $res > 0 ? $res : ($need_negativity)?$res:0;
	}


	/**
	 * Retourne une date à laquelle on a ajouté (ou soustré) du temps
	 *
	 * @param string $date
	 * @param string $nb
	 * @param string $interval
	 * @param string $to
	 *
	 * @return string
	 */
	public static function addToDate ($date, $nb, $interval = 'day',$to=null) {
		$pas = 0;
		$time = strtotime($date);

		if ($to===null) {
			$to = (strlen($date) > 10) ? 'Y-m-d H:i:s' : 'Y-m-d';
		}

		switch ($interval) {
			case 'day' :
				$pas = 24*60*60;
				$new_time = ($time + ($nb*$pas));

				$old_hour = date('H:i:s',$time);
				$new_hour = date('H:i:s',$new_time);

				if ($old_hour!=$new_hour) {
					$n = date('H',$new_time);
					if ($n==23) {
						$new_time += 60*60;
					}
				}

				return date($to,$new_time);
			break;
			case 'year' :
				$new_time =strtotime("+".$nb." years", $time);
				return date($to,$new_time);
			break;
			case 'month' :
				$new_time =strtotime("+".$nb." months", $time);
				return date($to,$new_time);
			break;
			case 'week' :
				$pas = 7*24*60*60 ;
				$new_time = ($time + ($nb*$pas));
				return date($to,$new_time);
			break;
			case 'hour':
				$pas = 60*60;
				$new_time = ($time+($nb*$pas));
				return date($to,$new_time);
			break;
			case 'minute':
				$pas = 60;
				$new_time = ($time+($nb*$pas));
				return date($to,$new_time);
			break;
			case 'second':
				$pas = 1;
				$new_time = ($time+($nb*$pas));
				return date($to,$new_time);
			break;
		}
        
        return date($to,$time);
	}
}



