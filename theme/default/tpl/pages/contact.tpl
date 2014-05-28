<div id="contact_content" >
	<h1><?php echo AcidRouter::getName('contact'); ?></h1>
	<div id="contact_left" class="col-md-6">
		<div class="clear"></div>

		<div id="contact_inputs" class="contact_block">

			<h2><?php echo Acid::trad('contact_form_please'); ?></h2>
			<div class="contact_subtitle" ></div>

			<div class="input_double row">
				<div class="input_left" >
					<div class="label"><?php echo Acid::trad('contact_form_firstname'); ?></div>
					<input id="input_firstname" type="text" name="firstname" value="<?php echo Contact::getSession('firstname'); ?>" />
				</div>
				<div class="input_right" >
					<div class="label"><?php echo Acid::trad('contact_form_lastname'); ?></div>
					<input id="input_name"  type="text" name="lastname" value="<?php echo Contact::getSession('lastname'); ?>" />
				</div>
				<div class="clear"></div>
			</div>
			<div class="input row" >
				<div class="label"><?php echo Acid::trad('contact_form_address'); ?></div>
				<input id="input_address"  type="text" name="address" value="<?php echo Contact::getSession('address'); ?>" />
			</div>
			<div class="input row" >
				<div class="label"><?php echo Acid::trad('contact_form_phone'); ?></div>
				<input id="input_tel"  type="text" name="phone" value="<?php echo Contact::getSession('phone'); ?>" />
			</div>
			<div class="input row" >
				<div class="label"><?php echo Acid::trad('contact_form_email'); ?></div>
				<input id="input_mail"  type="text" name="email" value="<?php echo Contact::getSession('email'); ?>" />
			</div>
			<div class="input row" >
				<div class="label"><?php echo Acid::trad('contact_form_message'); ?></div>
				<textarea id="input_mess"  name="message" cols="30" rows="2" ><?php echo Contact::getSession('message'); ?></textarea>
			</div>

			<div class="clear"></div>

			<div class="row">
				<input class="btn" id="contact_form_submit" type="submit" value="Envoyer" 	/>
			</div>

		</div>



	</div>

	<div id="contact_right"  class="col-md-6">

		<h2 class="contact_title"><?php echo Acid::trad('contact_page_map'); ?></h2>

		<div class="contact_block">
			<div id="block_gmap">

			</div>
		</div>


		<h2 class="contact_title"><?php echo Acid::trad('contact_page_coords'); ?></h2>
		<div id="contact_coord" class="contact_block">
			<div class="contact_div">

				<?php /*
				<div class="info_contact">
					<?php echo Acid::get('site:name'); ?>
				</div>
				*/ ?>

				<?php if ($g['site_config']->getConf('contact')) {?>
				<div class="info_contact">
					<div><span class="strong"><?php echo $g['site_config']->hscConf('contact'); ?></span></div>
				</div>
				<?php } ?>

				<?php if ($g['site_config']->getConf('address') || $g['site_config']->getConf('cp') || $g['site_config']->getConf('city')) {?>
				<div class="info_contact">
					<div><span class="strong"><?php echo $g['site_config']->hscConf('address'); ?></span></div>
					<div><span class="strong"><?php echo $g['site_config']->hscConf('cp'); ?> <?php echo $g['site_config']->hscConf('city'); ?></span></div>
				</div>
				<?php } ?>

				<?php if ($g['site_config']->getConf('phone') || $g['site_config']->getConf('fax')) {?>
				<div class="info_contact">

					<?php if ($g['site_config']->getConf('phone')) {?>
					<div>Tél. <span class="strong"><?php echo $g['site_config']->hscConf('phone'); ?></span></div>
					<?php } ?>

					<?php if ($g['site_config']->getConf('fax')) {?>
					<div>Fax <span class="strong"><?php echo $g['site_config']->hscConf('fax'); ?></span></div>
					<?php } ?>

				</div>
				<?php } ?>

				<?php if ($g['site_config']->getConf('email') || $g['site_config']->getConf('website')) {?>
				<div class="info_contact">

					<?php if ($g['site_config']->getConf('email')) {?>
					<div>
						<span class="strong">
						Email :
						<a href="mailto:<?php echo $g['site_config']->hscConf('email'); ?>">
						<?php echo $g['site_config']->hscConf('email'); ?>
						</a>
						</span>
					</div>
					<?php } ?>

					<?php if ($g['site_config']->getConf('website')) {?>
					<div>
						<span class="strong">
							Website :
							<a href="<?php echo $g['site_config']->hscConf('website'); ?>">
							<?php echo $g['site_config']->hscConf('website'); ?>
							</a>
						</span>
					</div>
					<?php } ?>

				</div>
				<?php } ?>

			</div>
		</div>

	<div class="clear"></div>
</div>

</div>

<?php echo AcidGMap::apiCall(); ?>

<?php
$address = $g['site_config']->hscConf('address') .' '.$g['site_config']->hscConf('cp').' '.$g['site_config']->hscConf('city');
echo AcidGMap::initMap('block_gmap',array('init_address'=>$address,'coords'=>'0,0','zoom'=>5,'no_inner_content'=>false/*,'icon'=>$g['acid']['url']['img'].'langs/'.$g['acid']['lang']['current'].'_sel.png'*/));
//echo AcidGMap::initDirection('block_gmap','start','stop',	array('coords'=>'0,0','zoom'=>5, 'no_inner_content'=>false));
?>
