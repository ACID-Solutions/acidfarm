<?php $rm_sh =   isset($g['acid']['tinymce']['remove_script_host']) ?  $g['acid']['tinymce']['remove_script_host'] : true;   ?>

<script type="text/javascript">
<!--


tinyMCE.init({
			mode : "<?php echo (!empty($g['acid']['tinymce']['all'])?  'textareas':'none'); ?>",
			theme : "advanced",
			convert_urls : true,
			relative_urls : false,
			fix_list_elements : true,
			remove_script_host : <?php echo $rm_sh ?  'true':'false'; ?>,
			document_base_url : "<?php echo Acid::get('url:system'); ?>",
			plugins : "advhr,advimage,media,table,layer,template,paste,inlinepopups",
			file_browser_callback : "tinyFileBrowser",
			theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,formatselect,fontsizeselect,separator,forecolor,backcolor,separator,undo,redo",
			theme_advanced_buttons2 : "image,advhr,media,separator,bullist,numlist,outdent,indent,separator,link,unlink,anchor,code,separator,sub,sup,charmap",
			theme_advanced_buttons3 : "tablecontrols,styleselect,template,removeformat,pastetext",
			theme_advanced_toolbar_location : "top",
			extended_valid_elements : "hr[class|width|size|noshade],#p[class|style],#div[class|style],#span[class|style]",
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : true,
			theme_advanced_path : false,
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_blockformats : "h2,h3,h4",
			theme_advanced_styles :"sans marge en haut=first; sans marges=without_margin; forcer en dessous=clear",
			body_class : "content_body",
			content_css : "<?php echo Acid::themeUrl('css/tiny-mce.css'); ?>"
			/*
			,style_formats: [
				{title: 'Titre 1', block: 'h2', styles: {color: '#FF8A00', fontSize: '25px', fontWeight :'normal', margin:'0px', marginnBottom:'15px'}},
				{title: 'Titre 2', block: 'h3', styles: {color: '#2B3144', fontSize: '17px', fontWeight :'normal', margin:'0px', marginnBottom:'10px'}},
				{title: 'Titre 3', block: 'h4', styles: {color: '#000000', fontSize: '13px', fontWeight :'bold', fontStyle :'normal', margin:'0px', marginnBottom:'10px'}},
				{title: 'Sous Titre 1', block: 'h4', styles: {color: '#747474', fontSize: '14px', fontStyle :'italic', fontWeight :'normal', margin:'0px', marginBottom:'15px'}},
				{title: 'Sous Titre 2', block: 'h5', styles: {color: '#C2C2C2', fontSize: '13px', fontStyle :'italic', fontWeight :'normal', margin:'0px', marginBottom:'10px'}},
				{title: 'Information', block: 'p', styles: {color: '#747474', fontSize: '12px', fontStyle :'italic', fontWeight :'normal', margin:'0px', marginBottom:'10px'}},
				{title: 'Block', block: 'div', styles: {border: '1px solid #354052', padding:'10px', margin:'10px 0px', color:'#000000', fontSize:'12px;'}},
				{title: 'Noir', inline: 'span', styles: {color: '#000000'}},
				{title: 'Gris', inline: 'span', styles: {color: '#C2C2C2'}},

			],
			template_replace_values: {
				main_color : "#1C1C1C",
		        url_system : "<?php echo Acid::get('url:system'); ?>", //use {$url_system}
		        url_img : "<?php echo Acid::get('url:img_abs'); ?>",
	        	site_name : "<?php echo Acid::get('site:name'); ?>"
		    },
		    template_templates: [
               {
                   title : "Gabarit",
                   src : "<?php echo Acid::get('url:tpl'); ?>tiny_mce/gabarit.htm",
                   description : "Gabarit de newsletter"
               }
			]
			*/

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
			<?php
				if ($v['ids']) {
					foreach ($v['ids'] as $id) {
			?>
					tinyMCE.execCommand('mceToggleEditor',false,'<?php echo $id ; ?>');

			<?php
					}
				 }
			 ?>
-->
</script>
