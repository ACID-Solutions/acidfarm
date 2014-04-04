<script type="text/javascript">
<!--

var BrowserPlupload = {
	instances : new Array(),
	queue : new Array(),
	queuedone : new Array(),
	multiform : function() { return $('#<?php echo $v['key']; ?>_multi_upload_file'); },

	init : function(selector) {

		 $(selector).each( function() {

			var ident = BrowserPlupload.instances.length;
			BrowserPlupload.queue[ident] = 0;
			BrowserPlupload.queuedone[ident] = 0;

			BrowserPlupload.instances[ident] = $(this).pluploadQueue({

				// General settings
				runtimes : '<?php echo implode(', ',Acid::get('plupload:runtimes')); ?>',

				url : url_upload,

				max_file_size : '<?php echo Acid::get('plupload:max_size'); ?>mb',
				chunk_size : '<?php echo Acid::get('plupload:chunk_size'); ?>mb',
				rename : true,
				unique_names : true,
				dragdrop: true,


				flash_swf_url : url_base + 'js/plupload/plupload.flash.swf',

				filters : [  {title : "Fichier", extensions : "<?php echo implode(',', Acid::get('ext:files')); ?>"} ],

				/*
				// Resize images on clientside if we can
				resize: {
				width : 200,
				height : 200,
				quality : 90,
				crop: true // crop to exact dimensions
				},
				*/


		        init: {

					'FilesAdded' : function(up, files) {
						BrowserPlupload.queue[ident] = BrowserPlupload.queue[ident] + files.length;
					},

					'FilesRemoved' : function(up, files) {
						BrowserPlupload.queue[ident] = BrowserPlupload.queue[ident] - files.length;
						alert(BrowserPlupload.queue[ident]);
					},

					'FileUploaded' : function(up, file) {


					   var form = BrowserPlupload.multiform();
					   var filepath = '<?php echo addslashes(SITE_PATH . Acid::get('path:tmp')); ?>' + file.target_name;

					   var tab = {};
					   tab['files'] = new Array();
					   tab['names'] = new Array();


					   var formA = form.serializeArray();
					   $.each(formA, function() {
					       if (tab[this.name]) {
					           if (!tab[this.name].push) {
					        	   tab[this.name] = [tab[this.name]];
					           }
					           tab[this.name].push(this.value || '');
					       } else {
					    	   tab[this.name] = this.value || '';
					       }
					   });

					   key = tab['files'].length;
						tab['files'][key] = filepath;
						tab['names'][key] = file.name;

						tab['ajax'] = 1;



						$.ajax({
							type: "POST",
							url: form.attr('action'),
							data: tab,
							success: function(data) {
								json = $.parseJSON(data);

								BrowserPlupload.queuedone[ident] = BrowserPlupload.queuedone[ident] + json.treatment.length;

								if (BrowserPlupload.queuedone[ident] == BrowserPlupload.queue[ident]) {
									window.location.reload();
								}

							}
						});

					}
		     }

	 	   });

		});

	},

	canRun : function() {

		<?php if(in_array('html5', Acid::get('plupload:runtimes'))): ?>
			if(Acid.Runtime.html5()) {
				return true;
			}
		<?php endif; ?>

		<?php if(in_array('flash', Acid::get('plupload:runtimes'))): ?>
			if(Acid.Runtime.flash()) {
				return true;
			}
		<?php endif; ?>

		return false;
	},


}

$().ready(function() {

	if (BrowserPlupload.canRun()) {
		BrowserPlupload.init(".fsb_plupload");
		$('#fsb_upload_file').hide();
		$('.fsb_plupload').toggle();
		$('.btn_upload').bind('click',function() { $('.fsb_plupload').toggle(); });
	}

});

-->
</script>