<?php 
$method_filter='';
foreach ($v['method_list'] as $key=>$val) {
	$method_filter .= '<option value="'.$key.'">'.$val.'</option>' . "\n";
}

$method_list_strict =	'	<option value="unused">'.Acid::trad('admin_search_list_isnt').'</option>' .
						'	<option value="is">'.Acid::trad('admin_search_list_is').'</option>';

$form = $o->initForm($o->preKey('fv_'),false);
$tab = new AcidTable();
$line = 1;
foreach ($v['keys'] as $key) {
	$class = isset($v['vars'][$key]) ? get_class($v['vars'][$key]) : 'AcidVarText';
	if ($class !== 'AcidVarFile') {

		$mo = in_array($class,array('AcidVarBool','AcidVarList','AcidVarRadio')) ?
		$method_list_strict : $method_filter;

		$method =	'<select name="'.$o->preKey('fm_'.$key).'">' . $mo . '</select>';

		$field_form = $class != 'AcidVarText' ?
		$form->getComponent($o->preKey('fv_'.$key)) :
		AcidForm::text($o->preKey('fv_'.$key),'',50);

		$tab->addVal($line,1,$o->getLabel($key));
		$tab->addVal($line,2,$method);
		$tab->addVal($line,3,$field_form);
	}
	$line++;
}

$limit_res = Acid::trad('admin_search_pagination',array('__INPUT__'=>AcidForm::text($o->preKey('ll'),$v['ll'],3))).' ';
$tab->addVal($line,1,$limit_res.AcidForm::submit('Rechercher'),array('colspan'=>3,'style'=>'padding-top:15px;'));

?>

<div class="generic_elt_search generic_elt_admin">
	<hr />
	<form method="get" action="<?php AcidUrl::build(); ?>">
		<div>
		<?php 
		foreach ($v['get'] as $key=>$val) {
			echo AcidForm::hidden($key,$val);
		}
		?>
		
		<?php echo $tab->html(); ?>

		</div>
	</form>
	
	<div class="generic_elts_footer"></div>
</div>
