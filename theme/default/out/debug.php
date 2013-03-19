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
 * @since     Version 0.3
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Outil de debug/maquettage 
 * @package   Model / View
 */
class Debug {
	
	/**
	 * Processus de mise à jour de debug
	 */
	public static function exePost() {
		
			
		if (isset($_POST['debug_do'])) {
		
			switch ($_POST['debug_do']) {
				
				case 'change_mode' :
						Acid::sessSet('debug_mode', $_POST['debug_mode']);
				break; 
				
			}
		
		}
	}
	
	/**
	 * Retourne l'outil de debug/maquettage 
	 * @param unknown_type $auto_actif
	 * @return string
	 */
	public static function templateTools($auto_actif=false) {
		
		self::exePost();
		
		$maquette_folder = Acid::get('keys:theme').'/'. Acid::get('theme').'/img/maquettes/';
		
		switch ($GLOBALS['nav'][0]) {

			default :
				$maquette = 'maquette.png';
				$style='';
			break;
			
		}
	
		$maquette_dir = $maquette_folder.$maquette;
		$maquette_path = SITE_PATH.$maquette_folder.$maquette;
		
		list($def_w,$def_h)=getimagesize($maquette_path);

		$cur_mode = Acid::sessExist('debug_mode') ? Acid::sessGet('debug_mode') : $auto_actif;

		$tool = self::toolForm($cur_mode);
		
		$maquette_div = '<div id="debug_maquette" style="display:none; background-repeat:no-repeat; top:0px; left:0px; width:100%; position:absolute;">'. "\n" .
						'<img src="'.$maquette_dir.'" alt="" style="position:relative; margin:auto; '.$style.'" />' . "\n" .
						'</div>';
		
		$js = 	self::jsForm() .	

				($cur_mode ? 'setTimeout(function() {activerDebug();},1000);' : '$("#debug_others").hide();' ) .
					
				'' . "\n" ;
		
		return $maquette_div.$tool.Lib::getJsCaller($js);
		
	}
	
	/**
	 * Retourne le formulaire d'administration de l'outil de debug/maquettage 
	 * @param mixed $cur_mode
	 * @return string
	 */
	public static function toolForm($cur_mode) {
		
		$selected_a = $cur_mode;
		$selected_b=!$selected_a;
		
		$tool =	'<div style="position:absolute; top:10px; left:10px;">'.
				'<form action="'.$_SERVER['REQUEST_URI'].'" method="post" id="debug_form" ><div>'.
					'<input type="hidden" name="debug_do" value="change_mode"  />'.
					'<input id="debug_form_mode" type="hidden" name="debug_mode" value="'.($cur_mode? 1:0).'" />'.
					'<input type="hidden" name="dontreload" value="do_not" />'.
				'</div></form>'.
				'Activer : <select id="maquette_active" onchange="debugSubmit();" >
				<option value="0"  '.($selected_b? 'selected="selected"':'').'>non</option>
				<option value="1" '.($selected_a? 'selected="selected"':'').' >oui</option>
				</select><br /><br />'.
				'<div id="debug_others">' .
				'	Opacity : <input size="5" id="maquette_opacity" type="text"  value="1" /><br />'.
				'	<input type="button" onclick="setDebugOpacity(1);" value="100 %" />'.
				'	<input type="button" onclick="setDebugOpacity(0.5);" value="50 %" />'.
				'	<input type="button" onclick="setDebugOpacity(0);" value="0 %" />'.
				'	<br />'.
				'	<input type="button" onclick="debugShow();" value="show" />'.
				'	<input type="button" onclick="debugHide();" value="hide" />'.
				'	<br />' . 
				'	<input type="button" onclick="setDebug();" value="apply" />' .
				'</div>'.
				
				'</div>';
		
		return $tool;
	}
	
	/**
	 * Retourne la portion de code javascript de l'outil de debug/maquettage 
	 * @return string
	 */
	public static function jsForm() {
		
				return 
					'
					function debugSubmit() {
						$("#debug_form_mode").val($("#maquette_active").val());
						$("#debug_form").submit();
					}
					
					function debugActif() {
						return ($("#maquette_active").val()==1);
					}
					
					function activerDebug() {			
						if (debugActif()) {
							initDebug();
							$("#debug_others").show();
						}else{
							clearDebug(true);
							$("#debug_others").hide();
						}
					}
					
					function setDebug() {
						if (debugActif()) {
							$("#debug_maquette").fadeTo(0,$(\'#maquette_opacity\').val());
						}
					}
					
					function setDebugOpacity(val) {
						if (debugActif()) {
							$(\'#maquette_opacity\').val(val);
							setDebug();
						}
					}
					
					function debugHide() {
						$("#debug_maquette").hide();
					}
					
					function debugShow() {
						$("#debug_maquette").show();
					}

					function initDebug() {
						if (debugActif()) {
							setDebug();
							debugShow()
						}
					}
					
					';
	}

}
	