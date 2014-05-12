<div id="footer_flags">
		<?php
			$url_lang = Acid::get('url:img').'langs/';
			foreach (Acid::get('lang:available') as $l) {
				$url_sel = $l.'_sel.png';
				$url_unsel = $l.'_unsel.png';
				$sel = ($l==Acid::get('lang:current'));
				$url = $sel ? $url_sel:$url_unsel;
		?>
				<a href="<?php echo Lang::langUrl($l); ?>" title="<?php echo Acid::trad('lang_'.$l); ?>">
					<?php
						echo Lang::langFlag($l,'',array('onmouseover'=>"this.src='".$url_lang.$url_sel."'",'onmouseout'=>"this.src='".$url_lang.$url."'"),$sel);
					?>

				</a>
		<?php }?>
</div>