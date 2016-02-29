<?php

/**
* Tableau des éléments pour Plupload. Ce tableau sera sous la forme :
* $pluploads[i] = array(	'field' => Le selecteur jQuery du champ qui deviendra un plupload (à la place d'un input file)
*							'form' 	=> Le selecteur jQuery de son formulaire
*							'ext'	=> Le tableau d'extensions autorisées pour ce champ
*
* $pluploads[i+1] = ...
*/
$pluploads = array();

/**
* On vérifie l'existence de selector simple jQuery (ex : '#mon_id') dans la config.
* Pour chaque selecteur, on va créer son élément plupload (selector jQuery, selecteur form, ext)
*/
if(Conf::exists('plupload:selectors')) {
	$selectors = Conf::get('plupload:selectors');
	if(!is_array($selectors)) { 
		$selectors = array($selectors);
	}

	if($selectors) {
		foreach ($selectors as $sel) {
			if (is_array($sel)) {
				$field = isset($sel[0]) ? $sel[0] : '';
				$form = isset($sel[1]) ? $sel[1] : 'form';
				$ext = isset($sel[2]) ? $sel[2] : false;
				$autosubmit = isset($sel[3]) ? $sel[3] : false;
			} else {
				$field = $sel;
				$form = 'form';
				$ext = false;
				$autosubmit = Acid::get('plupload:autosubmit');
			}

			$pluploads[] = array('field' => $field, 'form' => $form, 'ext' => $ext, 'autosubmit' => $autosubmit);

		}
	}
}

/**
* On vérifie l'existence de selector name jQuery (ex : '[name=mon_name]') dans la config.
* Pour chaque selecteur, on va créer son élément plupload (selector jQuery, selecteur form, ext)
*/
if(Conf::get('plupload:names')) {
	foreach(Conf::get('plupload:names') as $name) {
		if(is_array($name)) {
			$field = isset($name[0]) ? $name[0] : '';
			$form = isset($name[1]) ? $name[1] : 'form';
			$ext = isset($name[2]) ? $name[2] : false;
			$autosubmit = isset($name[3]) ? $name[3] : false;
		} else {
			$field = $name;
			$form = 'form';
			$ext = false;
			$autosubmit = Acid::get('plupload:autosubmit');
		}

		$pluploads[] = array('field'=>"[name=".$field."]",'form'=>$form, 'ext'=>$ext, 'autosubmit'=>$autosubmit);
	}
}

?>

<?php echo Acid::tpl('tools/plupload/plupload-obj.tpl',$v, $o);  ?>

<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<script type="text/javascript">

// S'il y a des champs à mettre en forme plupload
<?php if ($pluploads) { ?>
	<?php
		foreach ($pluploads as $eltkey => $elt) {
			 $elt['ext'] = is_array( $elt['ext'] ) ? implode(',',$elt['ext']) : $elt['ext'];
			 $config = array('autosubmit'=>$autosubmit,'show_upload'=>!$autosubmit);
			 if ($elt['ext']) {
			 	$config['extensions'] = $elt['ext'];
			 }
	?>

		$(document).ready( function() {
			AcidPlupload.apply(
					'<?php echo $elt['field'] ?>',
					'<?php echo $elt['form'] ?>',
					<?php echo json_encode($config); ?>
			);
		});

	<?php } ?>	// Fin de la boucle pour chaque élément à mettre en plupload
<?php } ?> // Fin de s'il y a des éléments à mettre en plupload

</script>