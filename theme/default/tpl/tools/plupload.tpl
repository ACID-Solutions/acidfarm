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
if(Conf::exist('plupload:selectors')) {
	$selectors = Conf::get('plupload:selectors');
	if(!is_array($selectors)) { 
		$selectors = array($selectors);
	}

	foreach($selectors as $sel) {
		if(is_array($sel)) {
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

		$pluploads[] = array('field'=>$field,'form'=>$form, 'ext'=>$ext, 'autosubmit'=>$autosubmit);

	}
}

/**
* On vérifie l'existence de selector name jQuery (ex : '[name=mon_name]') dans la config.
* Pour chaque selecteur, on va créer son élément plupload (selector jQuery, selecteur form, ext)
*/
if(Conf::exist('plupload:names')) {
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

<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<script type="text/javascript">

// Le tableau des uploaders
var plupload_uploader = new Array();
var plupload_list = new Array();
var plupload_ready = false;

<?php if(in_array('html5', Acid::get('plupload:runtimes'))): ?>

	if(Acid.Runtime.html5()) {
		plupload_ready = true;
	}

<?php endif; ?>

<?php if(in_array('flash', Acid::get('plupload:runtimes'))): ?>

	if(Acid.Runtime.flash()) {
		plupload_ready = true;
	}

<?php endif; ?>

// S'il y a des champs à mettre en forme plupload
<?php if ($pluploads): ?>
	<?php foreach ($pluploads as $eltkey => $elt): ?>

	// Identifiant unique qui sert pour le nom de variables (vu que nous sommes dans un foreach)
	<?php $ident = 'plupload_'.$eltkey; ?>
			
		if(plupload_ready) {
			$(document).ready( function() {
				
				/**
				* Le selecteur jQuery du champ <input type="file" />
				*/
				var $<?php echo $ident; ?>_selector = $('<?php echo $elt['field']; ?>');

				/**
				* Si l'élément existe dans le DOM de la page
				*/
				if($<?php echo $ident; ?>_selector.length) {

					// Le selecteur du formulaire
					var $<?php echo $ident; ?>_form = $('<?php echo $elt['form']; ?>');
					// Le selecteur du parent du input file
					var $<?php echo $ident; ?>_parent = $<?php echo $ident; ?>_selector.parent();
					// Le nom du champ input file (ex : 'src') 
					var <?php echo $ident; ?>_name = $<?php echo $ident; ?>_selector.attr('name');
					// Le nom du champ caché correspondant au path du fichier temporaire
					var <?php echo $ident; ?>_sel_tmp = 'tmp_'+<?php echo $ident; ?>_name;
					// Le nom du champ caché correspondant au vrai filename uploadé
					var <?php echo $ident; ?>_sel_tmp_name = 'tmp_'+<?php echo $ident; ?>_name+'_name';

					// On remplace les input files par plupload
					$<?php echo $ident; ?>_selector.next('br').remove();
					$<?php echo $ident; ?>_selector.remove();

					// On ajoute les liens pour ajouter et upload le fichier
					$<?php echo $ident; ?>_parent.append('<div id="container_<?php echo $ident; ?>" >');
					$<?php echo $ident; ?>_parent.append('<div style="float:left; margin-right:10px;" class="plupload_status" id="filelist_<?php echo $ident; ?>"></div>');
					$<?php echo $ident; ?>_parent.append('<div style="float:left;" class="plupload_buttons"><a id="pickfiles_<?php echo $ident; ?>" href="#">[<?php echo Acid::trad('plupload_select'); ?>]</a><a id="uploadfiles_<?php echo $ident; ?>" href="#">[<?php echo Acid::trad('plupload_upload'); ?>]</a></div>');
					$<?php echo $ident; ?>_parent.append('<div class="clear"></div>');
					$<?php echo $ident; ?>_parent.append('</div>');

					// Champ caché du path temporaire, s'il n'existe pas, on crée le input hidden, sinon on lui ajoute une classe pour le sélectionner
					// Sa valeur sera modifié par le callback de plupload après l'upload
					if(!$<?php echo $ident; ?>_form.find('[name=tmp_'+ (<?php echo $ident; ?>_name) +']').length) {
						$<?php echo $ident; ?>_parent.append('<input class="<?php echo $ident; ?>_tmp" type="hidden" name="tmp_'+ (<?php echo $ident; ?>_name) +'" value="" />');
					} else {
						$<?php echo $ident; ?>_form.find('[name=tmp_'+ (<?php echo $ident; ?>_name) +']').addClass('<?php echo $ident; ?>_tmp');
					}

					// Champ caché du vrai filename, s'il n'existe pas, on crée le input hidden, sinon on lui ajoute une classe pour le sélectionner
					// Sa valeur sera modifié par le callback de plupload après l'upload
					if(!$<?php echo $ident; ?>_form.find('[name=tmp_name_'+ (<?php echo $ident; ?>_name) +']').length) {
						$<?php echo $ident; ?>_parent.append('<input class="<?php echo $ident; ?>_tmp_name" type="hidden" name="tmp_name_'+ (<?php echo $ident; ?>_name) +'" value="" />');
					} else {
						$<?php echo $ident; ?>_form.find('[name=tmp_name_'+ (<?php echo $ident; ?>_name) +']').addClass('<?php echo $ident; ?>_tmp_name');
					}

					// Identifiant unique "plupload_" + id, pour instancier plusieurs plupload
					var cur_ident = "<?php echo $ident; ?>";

					plupload_uploader[cur_ident] = new plupload.Uploader({
						runtimes : '<?php echo implode(', ',Acid::get('plupload:runtimes')); ?>',
						browse_button : 'pickfiles_<?php echo $ident; ?>',
						container : 'container_<?php echo $ident; ?>',
						multiple_queues : false,
						max_file_size : '<?php echo Acid::get('plupload:max_size'); ?>mb',
						chunk_size : '<?php echo Acid::get('plupload:chunk_size'); ?>mb',
						url : url_upload,
						rename : true,
						unique_names : true,
						flash_swf_url : url_base + 'js/plupload/plupload.flash.swf'
						<?php if ($elt['ext']) { ?>
						, filters : [
							{title : "Extensions", extensions : "<?php echo implode(',', $elt['ext']); ?>"}
						]
						<?php } ?>
						
					});


					$('#pickfiles_<?php echo $ident; ?>').bind('click', function() {
						$('#container_<?php echo $ident; ?> .plupload.flash').css('top', '0px').css('left', '0px');
					});

					/**
					* Methode appelée lors de l'initialisation
					*/
					plupload_uploader[cur_ident].bind('Init', function(up, params) {

						setTimeout(function() {
							$('#container_<?php echo $ident; ?>').css('position','absolute').css('overflow','hidden').css('top','0px').css('left','0px').width(0).height(0);
							if ($('#container_<?php echo $ident; ?> .plupload.flash').length) {
								$('#container_<?php echo $ident; ?>').css('position','relative').css('overflow','visible').css('top','0px').css('left','0px').width('auto').height('auto');
								$('#container_<?php echo $ident; ?> .plupload.flash').css('top', '0px').css('left', '0px');
							}
						}, 100);
						//$('#filelist_<?php echo $ident; ?>').html("<div>Current runtime: " + params.runtime + "</div>");
						//$('.plupload.flash').css('top', '0px').css('left', '0px');
					});

					plupload_uploader[cur_ident].bind('Refresh', function(up, params) {
						$('#container_<?php echo $ident; ?> .plupload.flash').css('top', '0px').css('left', '0px');
					});

					/**
					* Methode appelée lors du click sur l'élément qui sert à commencer l'upload
					*/
					$('#uploadfiles_<?php echo $ident; ?>').click(function(e) {
						plupload_list[<?php echo $eltkey; ?>] = 2;
						plupload_uploader[cur_ident].start();
						e.preventDefault();
					});

					// On lance l'initialisation
					plupload_uploader[cur_ident].init();

					/**
					* Methode appelée lorsqu'un fichier est ajouté
					*/
					plupload_uploader[cur_ident].bind('FilesAdded', function(up, files) {
						$.each(files, function(i, file) {
							$('#filelist_<?php echo $ident; ?>').append(
								'<div id="' + file.id + '">' +
								file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
							'</div>');
						});

						<?php if(Acid::get('plupload:show_upload')): ?>
							$('#uploadfiles_<?php echo $ident; ?>').show();
						<?php endif; ?>

						plupload_uploader[cur_ident].stop();

						// On ajoute à la liste le fichier
						plupload_list[<?php echo $eltkey; ?>] = 1;

						up.refresh(); // Reposition Flash/Silverlight
					});

					/**
					* Methode appelée lors de l'avancement de l'upload (après chaque chunk)
					*/
					plupload_uploader[cur_ident].bind('UploadProgress', function(up, file) {
						$('#' + file.id + " b").html(file.percent + "%");
					});

					/**
					* Methode appelée lors du soulevement d'un erreur
					*/
					plupload_uploader[cur_ident].bind('Error', function(up, err) {
						/*
						$('#filelist_<?php echo $ident; ?>').append("<div>Error: " + err.code +
							", Message: " + err.message +
							(err.file ? ", File: " + err.file.name : "") +
							"</div>"
						);
						*/
						alert('File: ' + err.file.name + ' : ' +err.message);

						up.refresh(); // Reposition Flash/Silverlight
					});

					/**
					* Methode appelée lorsqu'un fichier a fini d'être uploadé
					*/
					plupload_uploader[cur_ident].bind('FileUploaded', function(up, file) {
						$('#' + file.id + " b").html("100%");
						var dest = '<?php echo addslashes(SITE_PATH) . Acid::get('path:tmp'); ?>' + file.target_name;
						var name = file.name;
						$('.<?php echo $ident; ?>_tmp').val(dest);
						$('.<?php echo $ident; ?>_tmp_name').val(name);

						plupload_list[<?php echo $eltkey; ?>] = 0;

						<?php if($elt['autosubmit']): ?>
							var go = true;
							for(i in plupload_list) {
								if(plupload_list[i] != 0) {
									go = false;
								}
							}

							if(go) {
								$<?php echo $ident; ?>_form.find('[type=submit]').click();
							}
						<?php endif; ?>

					});

					/**
					* Methode appelée lors du changement dans la liste d'attente, s'il y a déjà un fichier, il ne s'ajoutera pas mais prendra la place de l'autre
					*/
					plupload_uploader[cur_ident].bind('QueueChanged', function(up) {
					    var iter = 0;
					    while (iter < up.files.length)
					    {
					    	if(iter == 1) {
					    		$('#'+up.files[0].id).remove();
					    		up.splice(0,1); //remove the file from the queue
					    	}
							iter++;
					    }
					});

					$('#uploadfiles_<?php echo $ident; ?>').hide();

					$<?php echo $ident; ?>_form.bind('submit', function() {

						if( (plupload_list[<?php echo $eltkey; ?>] == undefined) || (plupload_list[<?php echo $eltkey; ?>] == 0) ) {
							return true;
						} else {
							if( (plupload_list[<?php echo $eltkey; ?>] == 1) ) {

								<?php if($elt['autosubmit']): ?>
									$('#uploadfiles_<?php echo $ident; ?>').click();
									$<?php echo $ident; ?>_form.find('div:first-child').hide();
									$<?php echo $ident; ?>_form.append('<div class="plupload_status_<?php echo $eltkey; ?>"></div>');
									$<?php echo $ident; ?>_form.find('.plupload_status').css('float', 'none');
									var myContent = $<?php echo $ident; ?>_form.find('.plupload_status').detach();
									myContent.appendTo('.plupload_status_<?php echo $eltkey; ?>');
									$<?php echo $ident; ?>_form.find('.plupload_status_<?php echo $eltkey; ?> div').show();
								<?php else: ?>
									return confirm("<?php echo Acid::trad('plupload_cancel_prepare'); ?>");
								<?php endif; ?>

							} else if ( (plupload_list[<?php echo $eltkey; ?>] == 2) ) {
								return confirm("<?php echo Acid::trad('plupload_cancel_upload'); ?>");
							}
							return false;
						}

					});

					
				}
			});
		}

	<?php endforeach ?>	// Fin de la boucle pour chaque élément à mettre en plupload
<?php endif ?> // Fin de s'il y a des éléments à mettre en plupload

</script>