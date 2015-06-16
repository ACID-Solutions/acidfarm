<div>

<table style="width:100%; padding:30px; table-layout: fixed;">
	<tr>
		<td colspan="3" style="vertical-align:top;  padding-bottom:25px; height:100px;  border-bottom:1px solid #CCCCCC;" >
			<p>
				<?php echo Acid::trad('admin_board_welcome',array('__NAME__'=>User::curUser()->fullName())); ?>
    		</p>
		</td>
	</tr>

	<tr>
		<td style="vertical-align:top; padding-top:25px; ">

			<div>
				<img src="<?php echo Acid::get('url:img'); ?>admin/picto_stats.png"  alt="" title=""  style="width:80px; margin:auto;" />
				<h4><?php echo Acid::trad('admin_board_site_title'); ?></h4>
			</div>

			<table>
				<tr><td style="vertical-align:top; white-space:nowrap; padding-right:15px;" ><?php echo Acid::trad('admin_board_site_name'); ?> </td><td><b><?php echo Acid::get('site:name');?></b></td></tr>
				<tr><td style="vertical-align:top; white-space:nowrap; padding-right:15px;"><?php echo Acid::trad('admin_board_site_email'); ?> </td><td><b><?php echo Acid::get('site:email');?></b></td></tr>
				<tr><td style="vertical-align:top; white-space:nowrap; padding-right:15px;"><?php echo Acid::trad('admin_board_form_email'); ?> </td><td><b><?php echo SiteConfig::getCurrent()->hscConf('email');?></b></td></tr>
				<tr><td style="vertical-align:top; white-space:nowrap; padding-right:15px;"><?php echo Acid::trad('admin_board_site_url'); ?> </td><td><a href="<?php echo Acid::get('url:system_lang');?>"><b><?php echo Acid::get('url:system_lang');?></b></a></td></tr>
			</table>

		</td>

		<td style="vertical-align:top; padding-top:25px; ">

			<div>
				<img src="<?php echo Acid::get('url:img'); ?>admin/picto_users.png"  alt="" title=""  style="width:80px; margin:auto;" />
				<h4><?php echo Acid::trad('admin_board_stats_title'); ?></h4>
			</div>

			<ul style="padding:0px 15px;">
				<?php if (isset( $v['stats']['users']['count']  )) { ?>
				<li><?php echo Acid::trad('admin_board_stats_sessions',array('__NB__'=>$v['stats']['users']['count'])); ?></li>
				<?php } ?>
				<?php if ($actu = Lib::getIn('lastactu',$v)) { ?>
				<li><?php echo Acid::trad('admin_board_stats_lastnews',array('__DATE__'=>AcidTime::conv($actu->get('adate')))); ?></li>
				<?php } ?>
			</ul>
		</td>

		<?php if (!empty( $v['registration']  )) { ?>
		<td style="width:25%; vertical-align:top;  padding-top:25px; ">
			<div>
				<img src="<?php echo Acid::get('url:img'); ?>admin/picto_registre.png"  alt="" title=""  style="width:80px; margin:auto;"  />
				<h4><?php echo Acid::trad('admin_board_version_title'); ?></h4>
			</div>

			<?php echo $v['registration']; ?>
		</td>
		<?php } ?>

	</tr>

</table>

</div>