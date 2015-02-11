<?php $trad_tab = array("\\"=>"\\\\","'"=>"\\'"); ?>
<script type="text/javascript">
function changeDisplay(node_id){
	var my_window = document.getElementById(node_id);
	if (my_window.style.display == 'none') my_window.style.display = '';
	else my_window.style.display = 'none';
}

function fsbNewDir(id_form) {
	var dirName = prompt('<?php echo Acid::trad('browser_ask_new_folder',$trad_tab); ?>','<?php echo Acid::trad('browser_new_folder',$trad_tab); ?>')
	if (dirName) {
		var my_form = document.getElementById(id_form);
		if (my_form) {
			var inputs = my_form.getElementsByTagName('input');
			var i;
			for (i=0;i<inputs.length;i++) {
				if (inputs[i].getAttribute('name') == 'name')
					inputs[i].setAttribute('value',dirName);
			}
			my_form.submit();
		}else alert('<?php echo Acid::trad('browser_error',$trad_tab); ?>');
	}
}

function getExtension(file_name) {

	var elts = file_name.split('.');
	elts.reverse();

	if(elts[0] == 'gz' && elts[1] == 'tar')
		return elts[1]+'.'+elts[0];
	else
		return elts[0];
}

function fsbChangeName(id_form,current_name,ftype) {
	if (ftype == 'file'){
		extension = getExtension(current_name);

		current_name = current_name.replace(RegExp('\.'+extension+'$'),'');
		question = '<?php echo Acid::trad('browser_ask_new_name',$trad_tab); ?> (.'+extension+') : ';
	}else
		question = '<?php echo Acid::trad('browser_ask_new_name',$trad_tab); ?> : ';

	var dirName = prompt(question,current_name)
	if (dirName) {
		var my_form = document.getElementById(id_form);
		if (my_form) {
			if (ftype == 'file') {
				var new_name = dirName+'.'+extension;
				current_name = current_name + '.' + extension;
			}
			else {
				var new_name = dirName;
			}

			var inputs = my_form.getElementsByTagName('input');
			var i;

			for (i=0;i<inputs.length;i++) {
				if (inputs[i].getAttribute('name') == 'name')
					inputs[i].setAttribute('value',new_name);
				else if (inputs[i].getAttribute('name') == 'current_name')
					inputs[i].setAttribute('value',current_name);
				else if (inputs[i].getAttribute('name') == 'type')
					inputs[i].setAttribute('value',ftype);
			}
			my_form.submit();
		}else alert('<?php echo Acid::trad('browser_error',$trad_tab); ?>');
	}
}

function fsbChooseFile(URL) {
	<?php $rm_sh =   isset($g['acid']['tinymce']['remove_script_host']) ?  $g['acid']['tinymce']['remove_script_host'] : true;   ?>

	var prefix = (tinyMCEPopup.getWindowArg("rm_host")==1) ? '' : '<?php echo Acid::get('url:prefix'); ?>';
	var win = tinyMCEPopup.getWindowArg("window");
	var field = tinyMCEPopup.getWindowArg("input");

	win.document.getElementById(field).value = prefix+URL;

    if (typeof(win.ImageDialog) != "undefined") {
		if (win.ImageDialog.getImageData) {
			win.ImageDialog.getImageData();
		}
		if (win.ImageDialog.showPreviewImage)  {
			win.ImageDialog.showPreviewImage(URL);
		}
	}

    tinyMCEPopup.close();

}

function fsbDelete(id_form,file_path,ftype) {
	if (ftype == 'file') var question = '<?php echo Acid::trad('browser_ask_del_file',$trad_tab); ?>';
	else var question = '<?php echo Acid::trad('browser_ask_del_folder',$trad_tab); ?>';
	if (confirm(question)) {
		var my_form = document.getElementById(id_form);
		if (my_form) {
			var inputs = my_form.getElementsByTagName('input');
			var i;
			for (i=0;i<inputs.length;i++) {
				if (inputs[i].getAttribute('name') == 'path')
					inputs[i].setAttribute('value',file_path);
			}
			my_form.submit();
		}else alert('<?php echo Acid::trad('browser_error',$trad_tab); ?>');
	}
}
</script>