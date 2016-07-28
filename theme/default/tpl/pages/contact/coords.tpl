<h2 class="contact_title"><?php echo Acid::trad('contact_page_coords'); ?></h2>
<div id="contact_coord" class="contact_block" itemscope itemtype="http://schema.org/LocalBusiness">
    <div class="contact_div">

        <meta itemprop="name" content="<?php echo Acid::get('site:name'); ?>">

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
            <div class="info_contact" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                <div itemprop="streetAddress"><span class="strong"><?php echo $g['site_config']->hscConf('address'); ?></span></div>
                <div itemprop="addressLocality"><span class="strong"><?php echo $g['site_config']->hscConf('cp'); ?> <?php echo $g['site_config']->hscConf('city'); ?></span></div>
            </div>
        <?php } ?>

        <?php if ($g['site_config']->getConf('phone') || $g['site_config']->getConf('fax')) {?>
            <div class="info_contact">

                <?php if ($g['site_config']->getConf('phone')) {?>
                    <div itemprop="telephone">Tél. <span class="strong"><?php echo $g['site_config']->hscConf('phone'); ?></span></div>
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