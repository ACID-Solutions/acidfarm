<?php $rm_sh =   isset($g['acid']['tinymce']['remove_script_host']) ?  $g['acid']['tinymce']['remove_script_host'] : true;   ?>

<script type="text/javascript">
<!--

function getTinyMceElements() {

	<?php
	$selector = array('textarea.acidtinymce');

	if ($g['acid']['tinymce']['all']) {
		$selector[] = 'textarea';
	}

	if ($v['ids']) {
		foreach ($v['ids'] as $id) {
			$selector[] = '#' . $id;
		}
	}
	?>

	return $('<?php echo implode(', ',$selector);  ?>');
}

function configTinyMceBodyId(target) {

	if ($(target).attr('data-tinymce-body-id')) {
		return $(target).attr('data-tinymce-body-id');
	}

	return "<?php echo Acid::get('tinymce:body_id'); ?>";
}

function configTinyMceBodyClass(target) {

	if ($(target).attr('data-tinymce-body-class')) {
		return $(target).attr('data-tinymce-body-class');
	}

	return "<?php echo Acid::get('tinymce:body_class') ? '' :'content_body'; ?>";
}

function configTinyMceCssFiles(target) {

	if ($(target).attr('data-tinymce-css-file')) {
		return $(target).attr('data-tinymce-css-file');
	}

	return "<?php echo Acid::get('sass:enable') ? AcidTemplate::sassUrl('tiny-mce') : Acid::themeUrl('css/tiny-mce
	.css'); ?>";
}

function configTinyMceCssStyle(target) {

	if ($(target).attr('data-tinymce-css-style')) {
		return $(target).attr('data-tinymce-css-style');
	}

	return "<?php echo Acid::get('tinymce:style'); ?>";
}


getTinyMceElements().each(function() {

	tinyMCE.init({

		theme : "modern",
		convert_urls : true,
		relative_urls : false,
		fix_list_elements : true,
		remove_script_host : <?php echo $rm_sh ?  'true':'false'; ?>,
		document_base_url : "<?php echo Acid::get('url:system'); ?>",
		file_browser_callback : tinyFileBrowser,
		extended_valid_elements : "hr[class|width|size|noshade]",
		//selector: "'textarea.acidtinymce<?php echo $g['acid']['tinymce']['all'] ? ', textarea' : ''; ?>'",
		target : this,
		plugins: [
			"advlist autolink lists link image charmap print preview anchor template",
			"searchreplace visualblocks code fullscreen",
			"insertdatetime media table contextmenu paste textcolor colorpicker"
		],
		branding: false,
		fontsize_formats: "8px 11px 12px 13px 14px 16px 18px 22px 24px 26px 32px 48px",
		toolbar: "insertfile undo redo | styleselect fontsizeselect forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
		body_id : configTinyMceBodyId(this),
		body_class : configTinyMceBodyClass(this),
		content_css : configTinyMceCssFiles(this),
		content_style : configTinyMceCssStyle(this),

		 /*
		 , textcolor_map: [
		 "FFFFFF", "Blanc",
		 "333333", "Noir",
		 "4D4D4D", "Gris1",
		 "F2F2F2", "Gris 2"
		 ]

		 ,style_formats: [
		 {title: 'Headers', items: [
		 {title: 'Header 1', format: 'h1'},
		 {title: 'Header 2', format: 'h2'},
		 {title: 'Header 3', format: 'h3'},
		 {title: 'Header 4', format: 'h4'},
		 {title: 'Header 5', format: 'h5'},
		 {title: 'Header 6', format: 'h6'}
		 ]},
		 {title: 'Inline', items: [
		 {title: 'Bold', icon: 'bold', format: 'bold'},
		 {title: 'Italic', icon: 'italic', format: 'italic'},
		 {title: 'Underline', icon: 'underline', format: 'underline'},
		 {title: 'Strikethrough', icon: 'strikethrough', format: 'strikethrough'},
		 {title: 'Superscript', icon: 'superscript', format: 'superscript'},
		 {title: 'Subscript', icon: 'subscript', format: 'subscript'},
		 {title: 'Code', icon: 'code', format: 'code'}
		 ]},
		 {title: 'Blocks', items: [
		 {title: 'Paragraph', format: 'p'},
		 {title: 'Blockquote', format: 'blockquote'},
		 {title: 'Div', format: 'div'},
		 {title: 'Pre', format: 'pre'}
		 ]},
		 {title: 'Alignment', items: [
		 {title: 'Left', icon: 'alignleft', format: 'alignleft'},
		 {title: 'Center', icon: 'aligncenter', format: 'aligncenter'},
		 {title: 'Right', icon: 'alignright', format: 'alignright'},
		 {title: 'Justify', icon: 'alignjustify', format: 'alignjustify'}
		 ]},
		 {title: 'Inline Styles', items: [
		 {title: 'Titre 1', block: 'h2', styles: {color: '#FF8A00', fontSize: '25px', fontWeight :'normal', margin:'0px', marginnBottom:'15px'}},
		 {title: 'Titre 2', block: 'h3', styles: {color: '#2B3144', fontSize: '17px', fontWeight :'normal', margin:'0px', marginnBottom:'10px'}},
		 {title: 'Titre 3', block: 'h4', styles: {color: '#000000', fontSize: '13px', fontWeight :'bold', fontStyle :'normal', margin:'0px', marginnBottom:'10px'}},
		 {title: 'Sous Titre 1', block: 'h4', styles: {color: '#747474', fontSize: '14px', fontStyle :'italic', fontWeight :'normal', margin:'0px', marginBottom:'15px'}},
		 {title: 'Sous Titre 2', block: 'h5', styles: {color: '#C2C2C2', fontSize: '13px', fontStyle :'italic', fontWeight :'normal', margin:'0px', marginBottom:'10px'}},
		 {title: 'Information', selector: 'div, p, span', styles: {color: '#747474', fontSize: '12px', fontStyle :'italic', fontWeight :'normal', margin:'0px', marginBottom:'10px'}},
		 {title: 'Block', selector: 'div, p, span', styles: {border: '1px solid #354052', padding:'10px', margin:'10px 0px', color:'#000000', fontSize:'12px;'}},
		 {title: 'Noir', inline: 'span', styles: {color: '#000000'}},
		 {title: 'Gris', inline: 'span', styles: {color: '#C2C2C2'}},
		 ]}
		],

		template_replace_values: {
			main_color : "#1C1C1C",
			url_system : "<?php echo Acid::get('url:system'); ?>", //use {$url_system}
			url_img : "<?php echo Acid::get('url:img_abs'); ?>", //use {$url_img}
			site_name : "<?php echo Acid::get('site:name'); ?>" //use {$site_name}
		},
		templates: [
		   {
			   title : "Gabarit",
			   url : "<?php echo Acid::get('url:tpl'); ?>tiny_mce/gabarit.htm",
			   description : "Gabarit de newsletter"
		   }
		]
		*/

	});

});

function tinyFileBrowser(field_name, url, type, win) {

	fileBrowserURL = "<?php echo Conf::get('url:admin'); ?>?page=medias&plugin=tinymce";

	tinyMCE.activeEditor.windowManager.open({
		title: "PDW File Browser",
		url: fileBrowserURL,
		width: 950,
		height: 650,
		inline: 0,
		maximizable: 1,
		scrollbars : "yes",
		close_previous: 0
	  },{
		window : win,
		input : field_name,
		rm_host : <?php echo $rm_sh ?  '1':'0'; ?>
	  }
	);
}

//tinyMCE.execCommand('mceToggleEditor',false,'#mytinymce');

-->
</script>
