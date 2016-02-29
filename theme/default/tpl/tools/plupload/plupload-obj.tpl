<script type="text/javascript">
<!--

  var AcidPlupload = {

      tmp_path: '<?php echo addslashes(SITE_PATH) . Acid::get('path:tmp'); ?>',

      ready: new Array(),

      uploader: new Array(),

      list: new Array(),

      extensions: new Array(),

      runtimes: new Array(),

      callbacks: new Array(),

      autosubmit: new Array(),

      show_upload: new Array(),

      instance: new Array(),

      type: new Array(),

      events: {},

      prepare : function(field,form,config) {

          if (config==undefined) {
              var config = {};
          }

          var key = AcidPlupload.instance.length;
          AcidPlupload.instance[key] = new Array();
          AcidPlupload.extensions[key] = config.extensions==undefined ? '' : config.extensions;
          AcidPlupload.runtimes[key] = config.runtimes==undefined ? '<?php echo implode(',',Acid::get('plupload:runtimes')); ?>' : config.runtimes;
          AcidPlupload.autosubmit[key] = config.autosubmit==undefined ? <?php echo (Acid::get('plupload:autosubmit') ? 'true':'false'); ?> : config.autosubmit;
          AcidPlupload.show_upload[key] = config.show_upload==undefined ? <?php echo (Acid::get('plupload:show_upload') ? 'true':'false'); ?> : config.show_upload;
          AcidPlupload.callbacks[key] = config.callbacks==undefined ? {} : config.callbacks;
          AcidPlupload.type[key] = config.type==undefined ? 'uploader' : config.type;
          AcidPlupload.events[key] = config.events==undefined ? AcidPlupload.init : config.events;
          AcidPlupload.ready[key] = AcidPlupload.isReady(AcidPlupload.runtimes[key]);

          return key;
     },

     apply : function(field,form,config) {

         if (config==undefined) {
             var config = {};
         }

         var ident_key = AcidPlupload.prepare(field, form, config);

         AcidPlupload.list[ident_key] = new Array();


         if (AcidPlupload.ready[ident_key]) {

             /**
              * Le selecteur jQuery du champ <input type="file" />
              */
             AcidPlupload.instance[ident_key]['selector'] = $(field);

             //On associe la clé à l'instance
             AcidPlupload.instance[ident_key]['key'] = ident_key;

             /**
              * Si l'élément existe dans le DOM de la page
              */
             if(AcidPlupload.instance[ident_key]['selector'].length) {

                 // Le selecteur du formulaire
                 AcidPlupload.instance[ident_key]['form'] = $(form);
                 // Le selecteur du parent du input file
                 AcidPlupload.instance[ident_key]['parent'] = AcidPlupload.instance[ident_key]['selector'].parent();
                 AcidPlupload.instance[ident_key]['name'] = AcidPlupload.instance[ident_key]['selector'].attr('name');
                 // Le nom du champ caché correspondant au path du fichier temporaire
                 AcidPlupload.instance[ident_key]['sel_tmp'] = 'tmp_' + AcidPlupload.instance[ident_key]['name'];
                 // Le nom du champ caché correspondant au vrai filename uploadé
                 AcidPlupload.instance[ident_key]['sel_tmp_name'] = 'tmp_name_' + AcidPlupload.instance[ident_key]['name'] + '';

                 // Le container
                 AcidPlupload.instance[ident_key]['container'] = 'container_plupload_'+ident_key;
                 // Le logger
                 AcidPlupload.instance[ident_key]['logger'] = 'filelist_plupload_'+ident_key;
                 // Le picker
                 AcidPlupload.instance[ident_key]['picker'] = 'pickfiles_plupload_'+ident_key;
                 // Le launcher
                 AcidPlupload.instance[ident_key]['launcher'] = 'uploadfiles_plupload_'+ident_key;

                 // Le nom du champ input file (ex : 'src')

                 // On ajoute l'interface plupload

                 //-Le container
                 AcidPlupload.instance[ident_key]['parent'].append('<div lass="plupload_container" data-plupload-key="'+ident_key+'" id="'+  AcidPlupload.instance[ident_key]['container'] +'" >');
                 //-La console
                 AcidPlupload.instance[ident_key]['parent'].append('<div style="float:left; margin-right:10px;" class="plupload_status" id="'+  AcidPlupload.instance[ident_key]['logger'] +'"><?php echo addslashes(Acid::trad('plupload_init')); ?></div>');

                 if (AcidPlupload.type[ident_key]!='multi') {

                     //-Création des boutons d'actions
                     AcidPlupload.instance[ident_key]['parent'].append(
                         '<div style="float:left;" class="plupload_buttons">' +
                         '<a class="plupload_pickfiles" id="' + AcidPlupload.instance[ident_key]['picker'] + '" href="#"><span><?php echo addslashes(Acid::trad('plupload_select')); ?></span></a>' +
                         '<a class="plupload_uploadfiles"  id="' + AcidPlupload.instance[ident_key]['launcher'] + '" href="#"><span><?php echo addslashes(Acid::trad('plupload_upload')); ?></span></a>' +
                         '</div>'
                     );

                     // -Champ caché du path temporaire, s'il n'existe pas, on crée le input hidden, sinon on lui ajoute une classe pour le sélectionner
                     // Sa valeur sera modifié par le callback de plupload après l'upload
                     if(!AcidPlupload.instance[ident_key]['form'].find('[name='+AcidPlupload.instance[ident_key]['sel_tmp']+']').length) {
                         AcidPlupload.instance[ident_key]['parent'].append('<input class="plupload_'+ident_key+'_tmp" type="hidden" name="'+ AcidPlupload.instance[ident_key]['sel_tmp'] +'" value="" />');
                     } else {
                         AcidPlupload.instance[ident_key]['form'].find('[name='+ AcidPlupload.instance[ident_key]['sel_tmp'] +']').addClass('plupload_'+ident_key+'_tmp');
                     }

                     //-Champ caché du vrai filename, s'il n'existe pas, on crée le input hidden, sinon on lui ajoute une classe pour le sélectionner
                     // Sa valeur sera modifié par le callback de plupload après l'upload
                     if(!AcidPlupload.instance[ident_key]['form'].find('[name='+AcidPlupload.instance[ident_key]['sel_tmp_name']+']').length) {
                         AcidPlupload.instance[ident_key]['parent'].append('<input class="plupload_'+ident_key+'_tmp_name" type="hidden" name="'+ AcidPlupload.instance[ident_key]['sel_tmp_name'] +'" value="" />');
                     } else {
                         AcidPlupload.instance[ident_key]['form'].find('[name='+ AcidPlupload.instance[ident_key]['sel_tmp_name'] +']').addClass('plupload_'+ident_key+'_tmp_name');
                     }

                 }

                 //On ferme l'interface plupload
                 AcidPlupload.instance[ident_key]['parent'].append('<div class="clear"></div>');
                 AcidPlupload.instance[ident_key]['parent'].append('</div>');


                 //On cache le champ
                 AcidPlupload.instance[ident_key]['selector'].hide();

                 // Identifiant unique "plupload_" + id, pour instancier plusieurs plupload
                 var cur_ident = "plupload_" + ident_key;

                 //On définit les extensions autorisées par défaut
                 var mime_types = '<?php echo implode(',', Acid::get('ext:files')); ?>';

                 //Si un jeu d'extensions est défini, on l'ajoute à la configuration
                 if (AcidPlupload.extensions[ident_key]) {
                     var mime_types = AcidPlupload.extensions[ident_key];
                 }

                 //Taille maximale d'un fichier
                 if (config.max_file_size==undefined) {
                     config.max_file_size = '<?php echo Acid::get('plupload:max_size');  ?>mb';
                 }

                 //Chunck du fichier
                 if (config.chunk_size==undefined) {
                     config.chunk_size = '<?php echo Acid::get('plupload:chunk_size');  ?>mb';
                 }

                 //Configuration du Plupload
                 var pconfig ={
                     runtimes : AcidPlupload.runtimes[ident_key],

                     container: AcidPlupload.instance[ident_key]['container'], // ... or DOM Element itself
                     multiple_queues: false,

                     url : url_upload,

                     filters : {
                         max_file_size : config.max_file_size,
                         mime_types: [{extensions : mime_types}]
                     },

                     flash_swf_url: url_base + 'js/<?php echo Acid::get('plupload:folder');  ?>Moxie.swf',
                     silverlight_xap_url: url_base + 'js/<?php echo Acid::get('plupload:folder');  ?>Moxie.xap',

                     rename: true,
                     unique_names: true,


                     init: AcidPlupload.events[ident_key]

                 };

                 if (   config.chunk_size ) {
                     pconfig.chunk_size = config.chunk_size;
                 }

                 if ( AcidPlupload.type[ident_key] == 'multi') {

                     pconfig.dragdrop = true;

                     AcidPlupload.uploader[ident_key] = AcidPlupload.getEltFromId(AcidPlupload.instance[ident_key]['container']).pluploadQueue(pconfig);
                 }else{

                     pconfig.browse_button =  AcidPlupload.instance[ident_key]['picker'];

                     AcidPlupload.uploader[ident_key] = new plupload.Uploader(pconfig);
                     AcidPlupload.uploader[ident_key].init();
                 }

             }
         }

     },

     callback : function(key,method,args) {

          if (AcidPlupload.callbacks[key][method]!=undefined) {
              var func = AcidPlupload.callbacks[key][method];
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
     },

     getKey : function(up)  {
         var container = up.getOption('container');
         if ($('#'+container).length) {
             if ($('#'+container).attr('data-plupload-key')) {
                 return $('#'+container).attr('data-plupload-key');
             }else{
                 var tag = "container_plupload_";
                 if (container.indexOf(tag)===0) {
                     return container.substr(tag.length);
                 }
             }
         }

         return false;
     } ,

     getInstance : function(up) {
        var key = AcidPlupload.getKey(up);
        if (key) {
            return AcidPlupload.instance[key];
        }
     },

     getType : function(up) {
          var key = AcidPlupload.getKey(up);
          if (key) {
              return AcidPlupload.type[key];
          }
     },

     getEltFromId: function(what) {
         return $('#'+what);
     },

     log : function(mylog,mylog2) {

         if (mylog2==undefined) {
             console.log(mylog);
         }else{
             console.log(mylog,mylog2);
         }

     },


      bind : {

          FormSubmit : function (instance)  {


              var form = instance['form'];
              var launcher = AcidPlupload.getEltFromId( instance['launcher'] );
              var picker = AcidPlupload.getEltFromId( instance['picker'] );
              var logger = AcidPlupload.getEltFromId( instance['logger'] );
              var container = AcidPlupload.getEltFromId( instance['container'] );

              AcidPlupload.callback(instance['key'],'SubmitBefore',arguments);



              if( (AcidPlupload.list[instance['key']] == undefined) || (AcidPlupload.list[instance['key']] == 0) ) {

                  if (AcidPlupload.callback(instance['key'],'SubmitBeforeState0',arguments)) {
                      return true;
                  }
              }else{

                  if( (AcidPlupload.list[instance['key']] == 1) ) {

                      if (AcidPlupload.callback(instance['key'],'SubmitBeforeState1',arguments)) {

                          if (AcidPlupload.autosubmit[instance['key']]) {

                              if (AcidPlupload.type[instance['key']]!='multi') {
                                  launcher.click();
                              }else{
                                  AcidPlupload.list[instance['key']] = 2;
                                  container.find('.plupload_start').click();
                              }

                              form.find('div:first-child').hide();
                              form.append('<div class="plupload_status_'+instance['key']+'"></div>');
                              form.find('.plupload_status').css('float', 'none');

                              var myContent = form.find('.plupload_status').detach();
                              myContent.appendTo('.plupload_status_'+instance['key']);

                              var myContent = container.detach().appendTo('.plupload_status_'+instance['key']);


                              form.find('.plupload_status_'+instance['key']+' div').show();

                          }else{

                              return confirm('<?php echo addslashes(Acid::trad('plupload_cancel_prepare')); ?>');

                          }

                      }

                  }else if( (AcidPlupload.list[ident_key] == 2) ) {

                      if (AcidPlupload.callback(instance['key'],'SubmitBeforeState2',arguments)) {

                          return confirm('<?php echo addslashes(Acid::trad('plupload_cancel_upload')); ?>');

                      }
                  }

                  return false;

              }
          }
      },

      init :  {

             PostInit: function() {
                 // Called after initialization is finished and internal event handlers bound
                 AcidPlupload.log('[PostInit]');
                 var instance = AcidPlupload.getInstance(this);
                 var form = instance['form'];
                 var launcher = AcidPlupload.getEltFromId( instance['launcher'] );
                 var picker = AcidPlupload.getEltFromId( instance['picker'] );
                 var logger = AcidPlupload.getEltFromId( instance['logger'] );
                 var container = AcidPlupload.getEltFromId( instance['container'] );
                 logger.html('');

                 if (AcidPlupload.type[instance['key']]!='multi') {
                     launcher.on('click', function () {
                         AcidPlupload.list[instance['key']] = 2;
                         AcidPlupload.uploader[instance['key']].start();
                         return false;
                     });

                     launcher.hide();
                 }

                 form.on('submit', function() {
                     return AcidPlupload.bind.FormSubmit(instance);
                 });

                 AcidPlupload.callback(instance['key'],'InitBefore',arguments);

                 setTimeout(function() {

                     if (AcidPlupload.type[instance['key']]!='multi') {
                         container.css('position', 'relative').css('overflow', 'hidden').css('top', '0px').css('left', '0px').width(0).height(0);
                         launcher.css('position', 'relative');
                         if (container.find('.moxie-shim.moxie-shim-flash').length) {
                             container.css('position', 'relative').css('overflow', 'visible').css('top', '0px').css('left', '0px').width('auto').height('auto');
                             container.find('.moxie-shim.moxie-shim-flash').css('top', '0px').css('left', '0px');
                         }

                         if (container.find('.moxie-shim.moxie-shim-html5').length) {
                             container.css('position', 'relative').css('overflow', 'visible').css('top', '0px').css('left', '0px').width('auto').height('auto');
                             container.find('.moxie-shim.moxie-shim-html5').css('top', '0px').css('left', '0px').css('cursor', 'pointer').css('z-index', parseInt($('#bwin').css('z-index')) + 100);
                             container.find('.moxie-shim.moxie-shim-html5').height(picker.height());
                             container.find('.moxie-shim.moxie-shim-html5').width(picker.width());
                             picker.css('cursor', 'pointer');
                         }
                     }

                     AcidPlupload.callback(instance['key'],'Init',arguments);

                 }, 100);
             },

             Browse: function(up) {
                 // Called when file picker is clicked
                 AcidPlupload.log('[Browse]');
             },

             Refresh: function(up) {
                 // Called when the position or dimensions of the picker change
                 AcidPlupload.log('[Refresh]');

                 var instance = AcidPlupload.getInstance(up);
                 var logger = AcidPlupload.getEltFromId( instance['logger'] );

                 if (AcidPlupload.type[instance['key']]!='multi') {
                     setTimeout(function () {

                         var cont = AcidPlupload.getEltFromId(instance['container']);
                         var ref = instance['parent'].find('.plupload_buttons');
                         var pick = AcidPlupload.getEltFromId(instance['picker']);

                         var flash = instance['form'].find('.moxie-shim.moxie-shim-flash')
                         var html5 = instance['form'].find('.moxie-shim.moxie-shim-html5')


                         if (ref.length) {
                             instance['parent'].css('position', 'relative');
                             //cont.css('left', ref.position().left + 'px');
                             //cont.css('top', ref.position().top + 'px');
                             //cont.width(ref.width());
                             //cont.height(ref.height());
                         }

                         //flash.css('background-color', 'green');
                         if (pick.length) {
                             flash.width(pick.width());
                             flash.css('top', pick.position().top + 'px');
                             flash.css('left', pick.position().left + 'px');
                         }

                         //html5.css('background-color', 'purple');
                         if (pick.length) {
                             html5.width(pick.width());
                             html5.height(pick.height());
                             html5.css('top', pick.position().top  + 'px');
                             html5.css('left', pick.position().left  + 'px');
                         }


                     }, 300);
                 }

                 AcidPlupload.callback(instance['key'],'Refresh',arguments);
             },

             StateChanged: function(up) {
                 // Called when the state of the queue is changed
                 AcidPlupload.log('[StateChanged]', up.state == plupload.STARTED ? "STARTED" : "STOPPED");
             },

             QueueChanged: function(up) {
                 // Called when queue is changed by adding or removing files
                 AcidPlupload.log('[QueueChanged]');

                 var instance = AcidPlupload.getInstance(up);

                 AcidPlupload.callback(instance['key'],'QueueChangedBefore',arguments);

                 if (AcidPlupload.type[instance['key']]!='multi') {
                     var iter = 0;
                     while (iter < up.files.length) {
                         if (iter == 1) {
                             $('#' + up.files[0].id).remove();
                             up.splice(0, 1); //remove the file from the queue
                         }
                         iter++;
                     }
                 }

                 AcidPlupload.callback(instance['key'],'QueueChanged',arguments);
             },

             OptionChanged: function(up, name, value, oldValue) {
                 // Called when one of the configuration options is changed
                 AcidPlupload.log('[OptionChanged]', 'Option Name: ', name, 'Value: ', value, 'Old Value: ', oldValue);
             },

             BeforeUpload: function(up, file) {
                 // Called right before the upload for a given file starts, can be used to cancel it if required
                 AcidPlupload.log('[BeforeUpload]', 'File: ', file);
             },

             UploadProgress: function(up, file) {
                 // Called while file is being uploaded
                 AcidPlupload.log('[UploadProgress]', 'File:', file, "Total:", up.total);
                 var instance = AcidPlupload.getInstance(up);
                 var logger = AcidPlupload.getEltFromId( instance['logger'] );
                 logger.find('#'+file.id+' b').html('<span>' + file.percent + "%</span>");
             },

             FileFiltered: function(up, file) {
                 // Called when file successfully files all the filters
                 AcidPlupload.log('[FileFiltered]', 'File:', file);
             },

             FilesAdded: function(up, files) {
                 // Called when files are added to queue
                 AcidPlupload.log('[FilesAdded]');
                 var instance = AcidPlupload.getInstance(up);
                 var logger = AcidPlupload.getEltFromId( instance['logger'] );


                 AcidPlupload.callback(instance['key'],'FilesAddedBefore',arguments);

                 plupload.each(files, function(file) {
                     if (AcidPlupload.type[instance['key']]!='multi') {
                         logger.append('<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>');
                     }
                     AcidPlupload.log('  File:', file);
                 });

                 //On affiche le launcher si demandé
                 if (AcidPlupload.show_upload[instance['key']]) {
                     var launcher = AcidPlupload.getEltFromId( instance['launcher'] );
                     launcher.show();
                 }

                 // On ajoute à la liste le fichier
                 AcidPlupload.list[instance['key']] = 1;

                 if (AcidPlupload.type[instance['key']]!='multi') {
                     AcidPlupload.uploader[instance['key']].refresh(); // Reposition Flash/Silverlight
                 }

                 AcidPlupload.callback(instance['key'],'FilesAdded',arguments);
             },

             FilesRemoved: function(up, files) {
                 // Called when files are removed from queue
                 AcidPlupload.log('[FilesRemoved]');

                 var instance = AcidPlupload.getInstance(up);
                 AcidPlupload.callback(instance['key'],'FilesRemoveddBefore',arguments);

                 plupload.each(files, function(file) {
                     AcidPlupload.log('  File:', file);
                 });
             },

             FileUploaded: function(up, file, info) {
                 // Called when file has finished uploading
                 AcidPlupload.log('[FileUploaded] File:', file, "Info:", info);

                 var instance = AcidPlupload.getInstance(up);
                 var logger = AcidPlupload.getEltFromId( instance['logger'] );

                 AcidPlupload.callback(instance['key'],'FileUploadedBefore',arguments);
                 logger.find('#'+file.id+' b').html("<span>OK</span>");

                 var dest = AcidPlupload.tmp_path + file.target_name;
                 var name = file.name;

                 if (AcidPlupload.type[instance['key']]!='multi') {
                     $('.plupload_'+instance['key']+'_tmp').val(dest);
                     $('.plupload_'+instance['key']+'_tmp_name').val(name);
                 }else{
                     instance['form'].append('<input type="hidden" name="'+instance['sel_tmp']+'[]" value="'+dest+'" />');
                     instance['form'].append('<input type="hidden" name="'+instance['sel_tmp_name']+'[]" value="'+name+'" />');
                 }


                 AcidPlupload.callback(instance['key'],'FileUploaded',arguments);


             },

             ChunkUploaded: function(up, file, info) {
                 // Called when file chunk has finished uploading
                 AcidPlupload.log('[ChunkUploaded] File:', file, "Info:", info);
             },

             UploadComplete: function(up, files) {
                 // Called when all files are either uploaded or failed
                 AcidPlupload.log('[UploadComplete]');
                 var instance = AcidPlupload.getInstance(up);

                 AcidPlupload.list[instance['key']] = 0;

                 if (AcidPlupload.autosubmit[instance['key']]) {
                     var go = true;
                     for(i in AcidPlupload.list) {
                         if(AcidPlupload.list[i] != 0) {
                             go = false;
                         }
                     }

                     if(go) {
                         AcidPlupload.callback(instance['key'],'AutoSubmitBefore',arguments);
                         instance['form'].find('[type=submit]').click();
                     }
                 }

             },

             Destroy: function(up) {
                 // Called when uploader is destroyed
                 AcidPlupload.log('[Destroy] ');
             },

             Error: function(up, args) {
                 // Called when error occurs

                 if (args.message!=undefined) {
                     AcidPlupload.log('[Error] ',  args);
                     alert(args.message);
                 }else{
                     AcidPlupload.log('[Error] ',  args);
                 }
                 $.each(args,function(k,v) {

                 });
             }
      }
  }

-->
</script>