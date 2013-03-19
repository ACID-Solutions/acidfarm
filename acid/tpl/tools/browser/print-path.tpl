<a href="<?php echo AcidUrl::build(array('fsb_path'=>'')); ?>">
	<img src="<?php echo $v['img_path']; ?>home.png" title="<?php echo Acid::trad('browser_home'); ?>" alt="<?php echo Acid::trad('browser_home'); ?>" />
</a>

<?php 
		$attr = '';
		foreach ($v['dirs'] as $dir) {
			if (!empty($dir)) {
				$attr .= $dir . '/';
				echo ' / <a href="'.AcidUrl::build(array('fsb_path'=>rawurlencode($attr))).'">'.$dir.'</a>' . "\n";
			}
		}
?>