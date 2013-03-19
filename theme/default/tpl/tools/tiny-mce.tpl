<?php $rm_sh =   isset($g['tinymce']['remove_script_host']) ?  $g['tinymce']['remove_script_host'] : true;   ?>

<script type="text/javascript"> 
<!--


tinyMCE.init({ 
			mode : "<?php echo (!empty($g['tinymce']['all'])?  'textareas':'none'); ?>", 
			theme : "advanced", 
			convert_urls : true,
			relative_urls : false,
			remove_script_host : <?php echo $rm_sh ?  'true':'false'; ?>, 
			document_base_url : "<?php echo Acid::get('url:system'); ?>", 
			plugins : "advhr,advimage,media,table,layer,paste,inlinepopups",
			file_browser_callback : "tinyFileBrowser",
			theme_advanced_buttons1 : "bold,italic,underline,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,formatselect,fontsizeselect,separator,forecolor,backcolor,separator,undo,redo",
			theme_advanced_buttons2 : "image,advhr,media,separator,bullist,numlist,outdent,indent,separator,link,unlink,anchor,code,separator,sub,sup,charmap",
			theme_advanced_buttons3 : "tablecontrols,styleselect,removeformat,pastetext",
			theme_advanced_toolbar_location : "top",
			extended_valid_elements : "hr[class|width|size|noshade]",
			theme_advanced_resizing : true, 
			theme_advanced_resize_horizontal : true, 
			theme_advanced_path : false, 
			theme_advanced_statusbar_location : "bottom", 
			theme_advanced_blockformats : "h2,h3,h4",
			theme_advanced_styles :"sans marge en haut=first; sans marges=without_margin; forcer en dessous=clear",
			body_class : "content_body",
			content_css : "<?php echo Acid::get('url:theme'); ?>css/tiny-mce.css"
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
							