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
 * @since     Version 0.3
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Gestionnaire Gmap
 * @package   Acidfarm\Tool
 */
class AcidGMap {

	/**
	 * Retourne une portion de code html appelant l'api GoogleMap
	 * @return string
	 */
	public static function apiCall($key=null) {
	    $keyquery= $key ? ('&key='.$key) : '';
		return '<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false'.$keyquery.'"></script>'. "\n" ;
	}

	/**
	 * Ajoute un marker à la map
	 * @param string $content_div identifiant DOM de la div contenant le gmap
	 * @param array $config configurations diverses
	 * @param string $alias identifiant du marker
	 * @return string
	 */
	public static function addMarker($content_div,$config=array(),$alias='default') {
		$title = isset($config['title'])? $config['title'] : Acid::get('site:name');
		$position = isset($config['position'])? $config['position'] : $content_div.'_latlng';
		$marker_coords = isset($config['marker_coords'])? $config['marker_coords'] : null;
		$no_inner_content = isset($config['no_inner_content'])? $config['no_inner_content'] : false;
		$no_marker = isset($config['no_marker'])? $config['no_marker'] : false;
		$infos = $no_inner_content ? '' : (isset($config['infos']) ? $config['infos'] : Acid::get('site:name'));
		$icon = isset($config['icon'])? $config['icon'] : '';

		$marker = '';

		$position = $marker_coords ? 'new google.maps.LatLng('.$marker_coords.')' : $position;

		if (!$no_marker) {
			$marker = '
				var '.$content_div.'_marker_'.$alias.'_latlng = '.$position.';
				var '.$content_div.'_marker_'.$alias.' = new google.maps.Marker({
					map: '.$content_div.'_map,
					position: '.$content_div.'_marker_'.$alias.'_latlng,
					title : "'.$title.'"'.
					($icon ? ', icon:"'.$icon.'"':'').'
				});
			';

			$infos = str_replace("\n",'',$infos);

			if (!$no_inner_content) {
				$marker .= '
					var '.$content_div.'_myInfoWindow = new google.maps.InfoWindow({content : "'.addslashes($infos).'"});
					google.maps.event.addListener('.$content_div.'_marker_'.$alias.', "click", function() {
						'.$content_div.'_myInfoWindow.setContent("'.addslashes($infos).'");
						'.$content_div.'_myInfoWindow.open('.$content_div.'_map,'.$content_div.'_marker_'.$alias.');
					});
				';
			}
		}

		return $marker;
	}

	/**
	 * Initialise le Gmap à partir d'une adresse
	 * @param string $content_div identifiant DOM de la div contenant le gmap
	 * @param string $address
	 * @param array $config configuration
	 * @return string
	 */
	public static function initAddress($content_div,$address=null,$config=array()) {
		$init = '';

		$config['position'] = 'results[0].geometry.location';

		if ($address)  {
			$init = '
				var '.$content_div.'_geocoder = new google.maps.Geocoder();
	    		var '.$content_div.'_address = "'.$address.'";

				if (('.$content_div.'_geocoder)) {
					'.$content_div.'_geocoder.geocode(
						{"address": \''.addslashes($address).'\'},
						function(results,status) {
							if (results[0] != undefined) {
								'.$content_div.'_map.setCenter(results[0].geometry.location);
								'.self::addMarker($content_div,$config,'initaddress').'
							}
						}
					);
				}
			';
		}

		return $init;
	}

	/**
	 * Initialisation d'une Gmap
	 * @param string $content_div identifiant DOM de la div contenant le gmap
	 * @param array $config
	 * @param boolean $function si true, le code retourné génèrera une fonction qui pourra être appelée ailleur dans la page
 	 * @return string
	 */
	public static function initMap($content_div,$config=array(),$function = false) {

	   /*
		* Toutes les options GMAP sur :
		* https://developers.google.com/maps/documentation/javascript/reference?hl=fr#MapOptions
		*/

		$init_coord = isset($config['coords'])? $config['coords']:'47.235015,-1.558197';
		$init_adress = isset($config['init_address'])? $config['init_address']:'';

		$center = isset($config['center'])? $config['center']:null;
		$zoom = isset($config['zoom'])? $config['zoom']:'10';
		$scrollable = isset($config['scrollable'])? $config['scrollable']: true;
		$draggable = isset($config['draggable'])? $config['draggable']: true;

		$title = isset($config['title'])? $config['title'] : Acid::get('site:name');
		$address = isset($config['address'])? $config['address']:$init_adress;

		$contact = isset($config['contact'])? $config['contact']:'';

		$def_infos = $title . ($address ? '<br />'.$address : '') . ($contact ? '<br />'.$contact : '');
		$infos = isset($config['infos']) ? $config['infos'] :  $def_infos;

		$no_inner_content = isset($config['no_inner_content'])? $config['no_inner_content'] : false;
		$no_marker = isset($config['no_marker'])? $config['no_marker'] : false;
		$marker_coords = isset($config['marker_coords'])? $config['marker_coords'] : null;

		$adress_info='<div class="gmap-block">'.$infos.'</div>';

		$icon = isset($config['icon'])? $config['icon'] : '';
		$style = isset($config['style'])? $config['style'] : '';

		$marker_config = array(
							'marker_coords'=>$marker_coords,
							'icon'=>$icon,
							'title'=>$title,
							'infos'=>$infos,
							'no_inner_content'=>$no_inner_content,
							'no_marker'=>$no_marker
		);

		$default_marker = $init_adress ?  '' : 	self::addMarker($content_div,$marker_config,'init');

		$map_type = isset($config['map_type']) ? $config['map_type'] :  'google.maps.MapTypeId.ROADMAP';

		$js=
		'<script type="text/javascript">
		<!--

		'.
		($function ? 'function '.$content_div.'_gmap_function() { ' : '' ).
		'

	    	var '.$content_div.'_div_id = "'.$content_div.'";

	    	var '.$content_div.'_latlng = new google.maps.LatLng('.$init_coord.');
			var '.$content_div.'_latlngpos = new google.maps.LatLng('.$center.');

			var '.$content_div.'_myStyle = '.($style ? ('['.json_encode($style).']') : 'null').';

			var '.$content_div.'_myOptions = {
			    zoom: '.$zoom.',
			    center: '.$content_div.($center ? '_latlngpos': '_latlng').',
			    mapTypeId: '.$map_type.',
			    draggable: '.($draggable ? 'true' : 'false') . ',
				scrollwheel: '.($scrollable ? 'true' : 'false') .'
		    }

			var '.$content_div.'_map = new google.maps.Map(document.getElementById('.$content_div.'_div_id), '.$content_div.'_myOptions);

			if ('.$content_div.'_myStyle) {
				'.$content_div.'_map.setOptions({"styles": '.$content_div.'_myStyle});
			}

			'.self::initAddress($content_div,$init_adress,$marker_config).'

    		'.$default_marker.'

            var center = block_gmap_map.getCenter();
            google.maps.event.addDomListener(window, \'resize\', function() {
                document.getElementById('.$content_div.'_div_id).style=\'\';
                block_gmap_map.setCenter(center);
            });
            
		'.
		($function ? '}' : '' ).
		'
		-->
		</script>'. "\n" ;


		return $js;
	}

	/**
	 * Initialise un googleDirection
	 * @param string $content_div identifiant DOM de la div contenant le gmap
	 * @param string $from_address adresse du point de départ
	 * @param string $to_address adresse du point d'arrivé
	 * @param array $config configuration
	 * @param boolean $function
	 * @return string si true, le code retourné génèrera une fonction qui pourra être appelée ailleur dans la page
	 */
	public static function initDirection($content_div,$from_address,$to_address,$config=array(),$function = false) {
		$new_config = $config;
		$new_config['no_marker'] = true;

		$panel_div = isset($new_config['panel_div']) ? $new_config['panel_div'] : '';
		$control_div = isset($new_config['control_div']) ? $new_config['control_div'] : '';


		$control =  (!$control_div) ? '' :
					' var '.$content_div.'_control = document.getElementById(\''.$control_div.'\');
			    	'.$content_div.'_control.style.display = \'block\';
			    	'.$content_div.'_map.controls[google.maps.ControlPosition.TOP].push('.$content_div.'_control);';

		$panel =  	$panel_div ? $content_div.'_directionsDisplay.setPanel(document.getElementById(\''.$panel_div.'\'));' : '';


		$direction = self::initMap($content_div,$new_config,$function);

		$direction .= '
			<script type="text/javascript">
			<!--

			 	var '.$content_div.'_service = null;
				function '.$content_div.'_calcRoute(start,end) {
			        var request = {
			          origin: start,
			          destination: end,
			          travelMode: google.maps.DirectionsTravelMode.DRIVING
			        };
			        '.$content_div.'_directionsService.route(request, function(response, status) {
			        	if (response.routes[0]!=undefined) {
			         	 '.$content_div.'_service = response.routes[0].legs[0];
				          if (status == google.maps.DirectionsStatus.OK) {
				            '.$content_div.'_directionsDisplay.setDirections(response);
				          }
				        }
			        });
			    }

			    function '.$content_div.'_initialize() {
			        '.$content_div.'_directionsDisplay = new google.maps.DirectionsRenderer();
			        '.$content_div.'_directionsDisplay.setMap('.$content_div.'_map);

			        '.$panel.'

			      	'.$control.'

			        '.$content_div.'_calcRoute("'.$from_address.'","'.$to_address.'");
		      }



			     var '.$content_div.'_directionDisplay;
		     	 var '.$content_div.'_directionsService = new google.maps.DirectionsService();

		     	 google.maps.event.addDomListener(window, \'load\', '.$content_div.'_initialize);

		     -->
			</script>'. "\n" ;


		return $direction;
	}

	/**
	 * Génère une image Gmap
	 * @param array $config configuration
	 * @param array $markers liste de configurations de markers
	 * @param array $onclick liste de functions onclick
	 * @return string
	 */
	public static function imageCaller($config=array(),$markers=array(),$onclick=array()) {

		$init_coord = isset($config['coords'])? $config['coords']:'-34.397, 150.644';
		$center = isset($config['center'])? $config['center']:null;
		$zoom = isset($config['zoom'])? $config['zoom']:'10';
		$width = isset($config['width'])? $config['width']:null;
		$height = isset($config['height'])? $config['height']:null;
		$type = isset($config['type'])? $config['type']:'roadmap';
		$ident = isset($config['ident'])? $config['ident']:'gmap';
		$alternative = isset($config['alternative'])? $config['alternative']:'GMAP';
		$title = isset($config['title'])? $config['title']:'GMAP';




		$size = ( (($width) && ($height)) ? '&amp;size='.$width.'x'.$height : '&amp;size=400x400' );

		$js = '';
		if ($markers) {
		$js .=
		'
		<script type="text/javascript">
		<!--

		var url_marker = "";

		';

		foreach ($markers as $conf) {

			$conf['ident'] = empty($conf['ident']) ? 'default' : $conf['ident'];
			$conf['color'] = empty($conf['color']) ? 'red' : $conf['color'];
			$conf['label'] = empty($conf['label']) ? '.' : $conf['label'];
			$conf['icon'] = empty($conf['icon']) ? '' : 'icon:'.$conf['icon'].'%7C';

			$js .='
					$().ready( function() {
						var marker_'.$conf['ident'].'_geocoder = new google.maps.Geocoder();
						var marker_'.$conf['ident'].'_address = "'.$conf['address'].'";

						marker_'.$conf['ident'].'_geocoder.geocode({"address": marker_'.$conf['ident'].'_address}, function(results,status) {
							if (results[0]!=undefined) {
								url_marker = url_marker + "&markers='.$conf['icon'].'color:'.$conf['color'].'%7Clabel:'.$conf['label'].'%7C"+results[0].geometry.location;
								$("#'.$ident.'").attr("src",$("#'.$ident.'").attr("src") + url_marker);
							}
						});
					});
				';
		}

		$js .=
		'




		-->
		</script>

		'. "\n" ;

		}

		$url = 'http://maps.googleapis.com/maps/api/staticmap?center='.$center.'&amp;zoom='.$zoom.$size.'&amp;maptype=roadmap&amp;sensor=false';

		$link_start = $link_stop = '';
		if (!empty($onclick)) {
			if (!empty($onclick['address'])) {
				$js .= self::initFromAddress($onclick['div'],$onclick['address'],$onclick['config'],true);
				$link_start = '<a href="#'.$ident.'" onclick=" '.$onclick['div'].'_gmap_function(); return false;" >';
				$link_stop = '</a>';
			}else{
				$link_start = $link_stop = '';
			}
		}

		return $link_start.'<img id="'.$ident.'" src="'.$url.'" alt="'.$alternative.'" title="'.$title.'" />'.$link_stop . $js ;

	}
}
?>