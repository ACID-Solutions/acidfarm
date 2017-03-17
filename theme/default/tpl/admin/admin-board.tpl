<div class="table-responsive">

<table class="table table-board" style="width:100%; padding:30px; table-layout: fixed;">
	<tr>
		<td  class="board_home" colspan="3" >
			<p>
				<?php echo Acid::trad('admin_board_welcome',array('__NAME__'=>User::curUser()->fullName())); ?>
    		</p>
		</td>
	</tr>

	<tr>
		<td  class="board_coords">

			<div>
				<img src="<?php echo Acid::themeUrl('img/admin/picto_stats.png'); ?>"  alt="" title=""  style="width:80px; margin:auto;" />
				<h4><?php echo Acid::trad('admin_board_site_title'); ?></h4>
			</div>

			<table>
				<tr><td><?php echo Acid::trad('admin_board_site_name'); ?> </td><td><b><?php echo Acid::get('site:name');?></b></td></tr>
				<tr><td><?php echo Acid::trad('admin_board_site_email'); ?> </td><td><b><?php echo Acid::get('site:email');?></b></td></tr>
				<tr><td><?php echo Acid::trad('admin_board_form_email'); ?> </td><td><b><?php echo SiteConfig::getCurrent()->hscConf('email');?></b></td></tr>
				<tr><td><?php echo Acid::trad('admin_board_site_url'); ?> </td><td><a href="<?php echo Acid::get('url:system_lang');?>"><b><?php echo Acid::get('url:system_lang');?></b></a></td></tr>
			</table>

		</td>

		<td  class="board_stats">

			<div>
				<img src="<?php echo Acid::themeUrl('img/admin/picto_users.png'); ?>"  alt="" title=""  style="width:80px; margin:auto;" />
				<h4><?php echo Acid::trad('admin_board_stats_title'); ?></h4>
			</div>

			<ul style="padding:0px 15px;">
				<?php if (isset( $v['stats']['users']['count']  )) { ?>
				<li><?php echo Acid::trad('admin_board_stats_sessions',array('__NB__'=>$v['stats']['users']['count'])); ?></li>
				<?php } ?>
				<?php if ($news = Lib::getIn('lastnews',$v)) { ?>
				<li><?php echo Acid::trad('admin_board_stats_lastnews',array('__DATE__'=>AcidTime::conv($news->get('adate')))); ?></li>
				<?php } ?>
			</ul>
		</td>

		<?php if (!empty( $v['registration']  )) { ?>
		<td class="board_registration">
			<div>
				<img src="<?php echo Acid::themeUrl('img/admin/picto_registre.png'); ?>"  alt="" title=""  style="width:80px; margin:auto;"  />
				<h4><?php echo Acid::trad('admin_board_version_title'); ?></h4>
			</div>

			<?php echo $v['registration']; ?>
		</td>
		<?php } ?>

	</tr>

</table>

</div>