<div class="fsb_window  <?php echo $o->getPlugin() ? 'plugin_'.$o->getPlugin() : 'no_plugin'; ?>">


	<div  class="fsb_head">
		<div class="fsb_path">
			<?php  echo $v['print_path']; ?>
		</div>

		<div class="fsb_actions">
			<div class="fsb_belt_file_action_eng">
				<?php  echo $v['new_dir_form']; ?>
				<a  class="btn_newdir" href="#" onclick="fsbNewDir('<?php  echo $v['key']; ?>_new_dir_form','<?php  echo $v['cur_path']; ?>');return false;">
					<img src="<?php  echo $v['img_path']; ?>new.png" title="<?php echo Acid::trad('browser_new_folder'); ?>" alt="<?php echo Acid::trad('browser_new_folder'); ?>" style="vertical-align:middle;" />
					<?php echo Acid::trad('browser_new_folder'); ?>
				</a>
				<a  class="btn_upload" href="" style="cursor:pointer;" onclick="changeDisplay('fsb_upload_form');return false;">
					<img src="<?php  echo $v['img_path']; ?>upload.png" title="<?php echo Acid::trad('browser_upload_file'); ?>" alt="<?php echo Acid::trad('browser_upload_file'); ?>" style="vertical-align:middle;" />
					<?php echo Acid::trad('browser_upload_file'); ?>
				</a>
				<div id="fsb_upload_form" style="display:none;">
				<?php  echo $v['upload_form']; ?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="fsb_plugin" style="position:relative;"><?php echo Acid::tpl('tools/browser/print-dir-plugin.tpl',$v,$o); ?></div>
	</div>

	<hr />

	<div class="fsb_forms">
		<?php  echo $v['remove_form']; ?>
		<?php  echo $v['change_form']; ?>
	</div>

	<?php
		foreach ($v['dirs'] as $dir) {
			echo  $o->printEltDir($dir) . "\n";
		}

		foreach ($v['links'] as $link) {
			echo  $o->printEltLink($link,$v['base_path'].$link['name']) . "\n";
		}

		foreach ($v['files'] as $file) {
			echo $o->printEltFile($file,$v['base_path'].$file['name']) . "\n";
		}
	?>


	<div class="clear"></div>
</div>

<?php echo $v['js']; ?>

<?php echo Acid::tpl('tools/browser/print-dir-stop.tpl',$v,$o); ?>