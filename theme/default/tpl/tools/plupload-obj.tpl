<script type="text/javascript">
<!--
	var Plupload = {

		tmp_path : '<?php echo addslashes(SITE_PATH) . Acid::get('path:tmp'); ?>',

		ready : new Array(),
		
		uploader : new Array(),

		list : new Array(), 

		extensions : new Array(), 

		runtimes : new Array(), 

		callbacks : new Array(), 
		
		autosubmit : new Array(), 
		
		show_upload : new Array(), 
		
		instance : new Array(),
		
		init : function(field,form,config) {
			if (config==undefined) {
				var config = {};
			}

			var key = Plupload.instance.length;
			Plupload.instance[key] = new Array();
			Plupload.extensions[key] = config.extensions==undefined ? '' : config.extensions;
			Plupload.runtimes[key] = config.runtimes==undefined ? '<?php echo implode(',',Acid::get('plupload:runtimes')); ?>' : config.runtimes;
			Plupload.autosubmit[key] = config.autosubmit==undefined ? <?php echo (Acid::get('plupload:autosubmit') ? 'true':'false'); ?> : config.autosubmit;
			Plupload.show_upload[key] = config.show_upload==undefined ? <?php echo (Acid::get('plupload:show_upload') ? 'true':'false'); ?> : config.show_upload;
			Plupload.callbacks[key] = config.callbacks==undefined ? {} : config.callbacks;
			
			Plupload.ready[key] = Plupload.isReady(Plupload.runtimes[key]);
			
			return key;
		},
		
		apply : function(field,form,config) {

			
			var ident_key = Plupload.init(field,form,config);

			Plupload.list[ident_key] = new Array();
			
			
			// S'il y a des champs à mettre en forme plupload
			// Identifiant unique qui sert pour le nom de variables (vu que nous sommes dans un foreach)
			if(Plupload.ready[ident_key]) {
	
				/**
				* Le selecteur jQuery du champ <input type="file" />
				*/
				Plupload.instance[ident_key]['selector'] = $(field);
				/**
				* Si l'élément existe dans le DOM de la page
				*/
				if(Plupload.instance[ident_key]['selector'].length) {

					// Le selecteur du formulaire
					Plupload.instance[ident_key]['form'] = $(form);
					// Le selecteur du parent du input file
					Plupload.instance[ident_key]['parent'] = Plupload.instance[ident_key]['selector'].parent();
					// Le nom du champ input file (ex : 'src')
					Plupload.instance[ident_key]['name'] = Plupload.instance[ident_key]['selector'].attr('name');
					// Le nom du champ caché correspondant au path du fichier temporaire
					Plupload.instance[ident_key]['sel_tmp'] = 'tmp_'+Plupload.instance[ident_key]['name'];
					// Le nom du champ caché correspondant au vrai filename uploadé
					Plupload.instance[ident_key]['sel_tmp_name'] = 'tmp_'+Plupload.instance[ident_key]['name']+'_name';
					
					// On remplace les input files par plupload
					Plupload.instance[ident_key]['selector'].next('br').remove();
					Plupload.instance[ident_key]['selector'].remove();

					
					// On ajoute les liens pour ajouter et upload le fichier
					Plupload.instance[ident_key]['parent'].append('<div id="container_plupload_'+ident_key+'" >');
					Plupload.instance[ident_key]['parent'].append('<div style="float:left; margin-right:10px;" class="plupload_status" id="filelist_plupload_'+ident_key+'"></div>');
					Plupload.instance[ident_key]['parent'].append('<div style="float:left;" class="plupload_buttons"><a id="pickfiles_plupload_'+ident_key+'" href="#"><span>Choisir un fichier</span></a><a id="uploadfiles_plupload_'+ident_key+'" href="#"><span>Envoyer le fichier</span></a></div>');
					Plupload.instance[ident_key]['parent'].append('<div class="clear"></div>');
					Plupload.instance[ident_key]['parent'].append('</div>');

					
					// Champ caché du path temporaire, s'il n'existe pas, on crée le input hidden, sinon on lui ajoute une classe pour le sélectionner
					// Sa valeur sera modifié par le callback de plupload après l'upload
					if(!Plupload.instance[ident_key]['form'].find('[name=tmp_'+ (Plupload.instance[ident_key]['name']) +']').length) {
						Plupload.instance[ident_key]['parent'].append('<input class="plupload_'+ident_key+'_tmp" type="hidden" name="tmp_'+ (Plupload.instance[ident_key]['name']) +'" value="" />');
					} else {
						Plupload.instance[ident_key]['form'].find('[name=tmp_'+ (Plupload.instance[ident_key]['name']) +']').addClass('plupload_'+ident_key+'_tmp');
					}
					
					// Champ caché du vrai filename, s'il n'existe pas, on crée le input hidden, sinon on lui ajoute une classe pour le sélectionner
					// Sa valeur sera modifié par le callback de plupload après l'upload
					if(!Plupload.instance[ident_key]['form'].find('[name=tmp_name_'+ (Plupload.instance[ident_key]['name']) +']').length) {
						Plupload.instance[ident_key]['parent'].append('<input class="plupload_'+ident_key+'_tmp_name" type="hidden" name="tmp_name_'+ (Plupload.instance[ident_key]['name']) +'" value="" />');
					} else {
						$plupload_0_form.find('[name=tmp_name_'+ (Plupload.instance[ident_key]['name']) +']').addClass('plupload_'+ident_key+'_tmp_name');
					}
					
					// Identifiant unique "plupload_" + id, pour instancier plusieurs plupload
					var cur_ident = "plupload_"+ident_key;
	
	
					//Configuration du Plupload
					var pconfig = {
						runtimes : Plupload.runtimes[ident_key],
						browse_button : 'pickfiles_plupload_'+ident_key,
						container : 'container_plupload_'+ident_key,
						multiple_queues : false,
						max_file_size : '500mb',
						chunk_size : '2mb',
						url : url_upload,
						rename : true,
						unique_names : true,
						flash_swf_url : url_base + 'js/plupload/plupload.flash.swf'
					};
	
					//si un jeu d'extensions est défini, on l'ajoute à la configuration
					if (Plupload.extensions[ident_key]) {
						pconfig['filters'] = [{title : "Extensions", extensions : "jpg,jpeg,png,gif,bmp,psd,eps,tiff"}];
					}
	
					//On instancie le Plupload 
					Plupload.uploader[ident_key] = new plupload.Uploader(pconfig);
	
					//Positionnement initial des objets Flash 
					$('#pickfiles_plupload_'+ident_key).bind('click', function() {
						Plupload.uploader[ident_key].refresh();						
					});
	
					/**
					* Methode appelée lors de l'initialisation
					*/
					Plupload.uploader[ident_key].bind('Init', function(up, params) {

						Plupload.callback(ident_key,'InitBefore',arguments);
								
						setTimeout(function() {
							$('#container_plupload_'+ident_key).css('position','absolute').css('overflow','hidden').css('top','0px').css('left','0px').width(0).height(0);

							if ($('#container_plupload_'+ident_key+' .plupload.flash').length) {
								$('#container_plupload_'+ident_key).css('position','relative').css('overflow','visible').css('top','0px').css('left','0px').width('auto').height('auto');
								$('#container_plupload_'+ident_key+' .plupload.flash').css('top', '0px').css('left', '0px');
							}

							if ($('#container_plupload_'+ident_key+' .plupload.html5').length) {
								$('#container_plupload_'+ident_key).css('position','relative').css('overflow','visible').css('top','0px').css('left','0px').width('auto').height('auto');
								$('#container_plupload_'+ident_key+' .plupload.html5').css('top', '0px').css('left', '0px').css('cursor','pointer').css('z-index',parseInt($('#bwin').css('z-index'))+100);
								$('#container_plupload_'+ident_key+' .plupload.html5').height($('#pickfiles_plupload_'+ident_key).height());
								$('#container_plupload_'+ident_key+' .plupload.html5').width($('#pickfiles_plupload_'+ident_key).width());
								$('#pickfiles_plupload_'+ident_key).css('cursor','pointer');
							}
							
							Plupload.callback(ident_key,'Init',arguments);
									
						}, 100);			

					
								
					});
	
					/**
					* Methode appelée lors du Refresh
					*/
					Plupload.uploader[ident_key].bind('Refresh', function(up, params) {

						setTimeout(function () {

							var cont = $('#container_plupload_'+ident_key);
							var ref = Plupload.instance[ident_key]['form'].find('.plupload_buttons');
							var pick = $('#pickfiles_plupload_'+ident_key);
							
							var flash = Plupload.instance[ident_key]['form'].find('.plupload.flash')
							var html5 = Plupload.instance[ident_key]['form'].find('.plupload.html5')
							
						
							cont.css('position','absolute');
							cont.css('left',ref.position().left+'px');
							cont.css('top',ref.position().top+'px');
							cont.width(ref.width());
							cont.height(ref.height());
							
							
							//flash.css('background-color', 'green');
							flash.width(pick.width());
							flash.css('top',  pick.position().top+'px');
							flash.css('left', pick.position().left+'px');

				
							//html5.css('background-color', 'purple');
							html5.width(pick.width());
							html5.height(pick.height());
							html5.css('top', 0+'px');
							html5.css('left', 0+'px');
					
							
						},500);
						
						Plupload.callback(ident_key,'Refresh',arguments);
								
					});
					
					/**
					* Methode appelée lors du click sur l'élément qui sert à commencer l'upload
					*/
					$('#uploadfiles_plupload_'+ident_key).click(function(e) {
						Plupload.list[ident_key] = 2;
						Plupload.uploader[ident_key].start();
						e.preventDefault();
					});
	
					// On lance l'initialisation
					Plupload.uploader[ident_key].init();
			
					/**
					* Methode appelée lorsqu'un fichier est ajouté
					*/
					Plupload.uploader[ident_key].bind('FilesAdded', function(up, files) {

						Plupload.callback(ident_key,'FilesAddedBefore',arguments);
								
						$.each(files, function(i, file) {
							$('#filelist_plupload_'+ident_key).append(
							'<div id="' + file.id + '">' +
							file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
							'</div>');
						});
	
						if (Plupload.show_upload[ident_key]) { 
							$('#uploadfiles_plupload_'+ident_key).show();
						}
					
						Plupload.uploader[ident_key].stop();
						
						// On ajoute à la liste le fichier
						Plupload.list[ident_key] = 1;
	
						Plupload.uploader[ident_key].refresh(); // Reposition Flash/Silverlight

						Plupload.callback(ident_key,'FilesAdded',arguments);
					});
	
					
					/**
					* Methode appelée lors de l'avancement de l'upload (après chaque chunk)
					*/
					Plupload.uploader[ident_key].bind('UploadProgress', function(up, file) {
						Plupload.callback(ident_key,'UploadProgressBefore',arguments);
						$('#' + file.id + " b").html(file.percent + "%");
						Plupload.callback(ident_key,'UploadProgress',arguments);
					});
					
					/**
					* Methode appelée lors du soulevement d'un erreur
					*/
					Plupload.uploader[ident_key].bind('Error', function(up, err) {
						
						Plupload.callback(ident_key,'ErrorBefore',arguments);
						
						/*
						$('#filelist_plupload_0').append("<div>Error: " + err.code +
						", Message: " + err.message +
						(err.file ? ", File: " + err.file.name : "") +
						"</div>"
						);
						*/
						alert('File: ' + err.file.name + ' : ' +err.message);
						up.refresh(); // Reposition Flash/Silverlight
						
						Plupload.callback(ident_key,'Error',arguments);
						
					});
					
					/**
					* Methode appelée lorsqu'un fichier a fini d'être uploadé
					*/
					Plupload.uploader[ident_key].bind('FileUploaded', function(up, file) {

						Plupload.callback(ident_key,'FileUploadedBefore',arguments);
						
						$('#' + file.id + " b").html("100%");
	
						var dest = Plupload.tmp_path + file.target_name;
						var name = file.name;
	
						$('.plupload_'+ident_key+'_tmp').val(dest);
						$('.plupload_'+ident_key+'_tmp_name').val(name);
	
						Plupload.list[ident_key] = 0;
	
						if (Plupload.autosubmit[ident_key]) { 
							var go = true;
							for(i in Plupload.list[ident_key]) {
								if(Plupload.list[ident_key][i] != 0) {
									go = false;
								}
							}
		
							if(go) {
								Plupload.callback(ident_key,'AutoSubmitBefore',arguments);
								Plupload.instance[ident_key]['form'].find('[type=submit]').click();
							}
						}

						Plupload.callback(ident_key,'FileUploaded',arguments);
						
					});
					
					/**
					* Methode appelée lors du changement dans la liste d'attente, s'il y a déjà un fichier, il ne s'ajoutera pas mais prendra la place de l'autre
					*/
					Plupload.uploader[ident_key].bind('QueueChanged', function(up) {

						Plupload.callback(ident_key,'QueueChangedBefore',arguments);
						
						var iter = 0;
						while (iter < up.files.length) {
							if(iter == 1) {
								$('#'+up.files[0].id).remove();
								up.splice(0,1); //remove the file from the queue
							}
							iter++;
						}

						Plupload.callback(ident_key,'QueueChanged',arguments);
						
					});
	
					//On cache l'upload				
					$('#uploadfiles_plupload_'+ident_key).hide();
	
					/**
					* Methode appelée lors de la soumission du formulaire
					*/
					Plupload.instance[ident_key]['form'].bind('submit', function() {
						
						Plupload.callback(ident_key,'SubmitBefore',arguments);
						
						if( (Plupload.list[ident_key] == undefined) || (Plupload.list[ident_key] == 0) ) {
							if (Plupload.callback(ident_key,'SubmitBeforeState0',arguments)) {
								return true;
							}
						}else{
							
							if( (Plupload.list[ident_key] == 1) ) {

								if (Plupload.callback(ident_key,'SubmitBeforeState1',arguments)) {
								
									if (Plupload.autosubmit[ident_key]) { 
										$('#uploadfiles_plupload_'+ident_key).click();
		
										Plupload.instance[ident_key]['form'].find('div:first-child').hide();
										Plupload.instance[ident_key]['form'].append('<div class="plupload_status_'+ident_key+'"></div>');
										Plupload.instance[ident_key]['form'].find('.plupload_status').css('float', 'none');
		
										var myContent = Plupload.instance[ident_key]['form'].find('.plupload_status').detach();
										
										myContent.appendTo('.plupload_status_'+ident_key);
										Plupload.instance[ident_key]['form'].find('.plupload_status_'+ident_key+' div').show();
									}else{
	
										return confirm("Un chargement a été préparé, voulez-vous l'annuler ?");
	
									}
								
								}
								
							}else if( (Plupload.list[ident_key] == 2) ) {

								if (Plupload.callback(ident_key,'SubmitBeforeState2',arguments)) {
								
									return confirm("Un chargement est en cours, voulez-vous l'annuler ?");

								}
							}
							
							return false;
							
						}
					});

				}

			}

		},

		callback : function(key,method,args) {
			
			if (Plupload.callbacks[key][method]!=undefined) {
				var func = Plupload.callbacks[key][method];
				return func(key,args);
			}

			return true; 
		},
		
		isReady : function(runtimes) {

			var plupload_ready = false; 

			var list = runtimes.split(',');

			if (Acid.Tools.inArray('html5',list)) {
				if(Acid.Runtime.html5()) {
					plupload_ready = true;
				}
			}

			if (Acid.Tools.inArray('flash',list)) {
				if(Acid.Runtime.flash()) {
					plupload_ready = true;
				}
			}

			if (Acid.Tools.inArray('html4',list)) {
				plupload_ready = true;
			}
			
				
			return plupload_ready; 
		}


	}
-->
</script>