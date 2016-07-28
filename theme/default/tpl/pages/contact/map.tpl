<h2 class="contact_title"><?php echo Acid::trad('contact_page_map'); ?></h2>

<div class="contact_block">
    <div id="block_gmap">

    </div>
</div>

<?php echo AcidGMap::apiCall(); ?>

<?php

//Configuration de la Google Map
$style = array(	'featureType' => "all", 'stylers'=> array(array('saturation'=>-100),array('gamma'=> 0.50)));
$address = trim($g['site_config']->hscConf('address') .' '.$g['site_config']->hscConf('cp').' '.$g['site_config']->hscConf('city'));
$coords = trim($g['site_config']->hscConf('coords'));
$zoom = intval($g['site_config']->getConf('zoom')) ? intval($g['site_config']->getConf('zoom')) : 8;

$gmap_config = array(
    //'init_address'=>$address,
    'address'=>$address,
    //'center'=>($coords ? $coords : '0,0'),
    'zoom'=>$zoom,
    'no_inner_content'=>false,
    //'icon'=>$g['acid']['url']['img'].'langs/'.$g['acid']['lang']['current'].'_sel.png',
    //'style'=>$style,
);

if ($coords) {
    $gmap_config['coords'] = $coords;
}else{
    $gmap_config['init_address'] = $address;
}

//Affichage de la Google Map
echo AcidGMap::initMap('block_gmap',$gmap_config);
//echo AcidGMap::initDirection('block_gmap','start','stop',	array('coords'=>'0,0','zoom'=>5, 'no_inner_content'=>false));

?>
