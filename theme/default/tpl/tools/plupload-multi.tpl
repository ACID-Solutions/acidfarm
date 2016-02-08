<script type="text/javascript">
<!--

var MultiPlupload = {
	instances : new Array(),
	queue : new Array(),
	queuedone : new Array(),
	forms : new Array(),

	init : function(config) {

		if (config==undefined) {
			var config = { }
		}

		if (config.extensions==undefined) {
			config.extensions = "<?php echo implode(',', Acid::get('ext:files')); ?>";
		}

		if (config.hide_submit==undefined) {
			config.hide_submit = true;
		}

		if (config.selector==undefined) {
			config.selector = false;
		}

		if (config.key==undefined) {
			config.key = 'files';
		}


		if (config.selector) {

			$(config.selector).each(function () {


				var ident = MultiPlupload.instances.length;

				MultiPlupload.queue[ident] = 0;
				MultiPlupload.queuedone[ident] = 0;
				MultiPlupload.forms[ident] = $(this).parents('form');
				MultiPlupload.forms[ident].find('[name='+config.key+']').remove();

				MultiPlupload.instances[ident] = $(this).pluploadQueue({

					// General settings
					runtimes: '<?php echo implode(', ',Acid::get('plupload:runtimes')); ?>',

					url: url_upload,

					max_file_size: '<?php echo Acid::get('plupload:max_size'); ?>mb',
					chunk_size: '<?php echo Acid::get('plupload:chunk_size'); ?>mb',
					rename: true,
					unique_names: true,
					dragdrop: true,


					flash_swf_url: url_base + 'js/plupload/plupload.flash.swf',

					filters: [{title: "Fichier", extensions: config.extensions}],


					init: {

						'FilesAdded': function (up, files) {
							MultiPlupload.queue[ident] = MultiPlupload.queue[ident] + files.length;
						},

						'FilesRemoved': function (up, files) {
							MultiPlupload.queue[ident] = MultiPlupload.queue[ident] - files.length;
						},

						'FileUploaded': function (up, file) {

							var filepath = '<?php echo addslashes(SITE_PATH . Acid::get('path:tmp')); ?>' + file.target_name;

							var key = MultiPlupload.queuedone[ident];
							MultiPlupload.forms[ident].append('<input class="files_inputs" type="hidden" name="multi_'+config.key+'[' + key + ']" value="' + encodeURI(filepath) + '" />');
							MultiPlupload.forms[ident].append('<input class="files_inputs_names" type="hidden" name="multi_name_'+config.key+'[' + key + ']" value="' + encodeURI(file.name) + '" />');

							MultiPlupload.queuedone[ident] = MultiPlupload.queuedone[ident] + 1;

							if (MultiPlupload.queue[ident]) {
								if (MultiPlupload.queuedone[ident] == MultiPlupload.queue[ident]) {
									MultiPlupload.forms[ident].find('[type=submit]').click();
								}
							}
						}
					}

				});

				if (config.hide_submit) {
					MultiPlupload.forms[ident].find('[type=submit]').hide();
				}
				MultiPlupload.forms[ident].find('.plupload_button').on('click', function () {
					return false;
				});

			});

		}

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
	}


}

$().ready(function() {

	if (MultiPlupload.canRun()) {
		<?php
		if(Conf::get('plupload:multi')) {
			foreach (Conf::get('plupload:multi') as $config) {
		?>
				MultiPlupload.init(<?php echo json_encode($config); ?>);
		<?php
			}
		}
		?>

	}

});

-->
</script>