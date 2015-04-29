<div>

<table style="width:100%; padding:30px;">
	<tr>
		<td colspan="3" style="vertical-align:top;  padding-bottom:25px; height:100px;  border-bottom:1px solid #CCCCCC;" >
			<p>
				Bonjour <b><?php echo User::curUser()->fullName(); ?></b>,<br />
    			Vous voici dans votre espace d'administration.
    		</p>
		</td>
	</tr>

	<tr>
		<td style="vertical-align:top; padding-top:25px; ">

			<div>
				<img src="<?php echo Acid::get('url:img'); ?>admin/picto_stats.png"  alt="" title=""  style="width:80px; margin:auto;" />
				<h4>Votre site</h4>
			</div>

			<table>
				<tr><td>Nom du site : </td><td><b><?php echo Acid::get('site:name');?></b></td></tr>
				<tr><td>Email du site : </td><td><b><?php echo Acid::get('site:email');?></b></td></tr>
				<tr><td>Email formulaire : </td><td><b><?php echo SiteConfig::getCurrent()->hscConf('email');?></b></td></tr>
			</table>

		</td>

		<td style="vertical-align:top; padding-top:25px; ">

			<div>
				<img src="<?php echo Acid::get('url:img'); ?>admin/picto_users.png"  alt="" title=""  style="width:80px; margin:auto;" />
				<h4>Statistiques</h4>
			</div>

			<ul style="padding:0px 15px;">
				<?php if (isset( $v['stats']['users']['count']  )) { ?>
				<li><b><?php echo $v['stats']['users']['count']; ?></b> session(s) active(s)</li>
				<?php } ?>
			</ul>
		</td>

		<?php if (!empty( $v['registration']  )) { ?>
		<td style="width:25%; vertical-align:top;  padding-top:25px; ">
			<div>
				<img src="<?php echo Acid::get('url:img'); ?>admin/picto_registre.png"  alt="" title=""  style="width:80px; margin:auto;"  />
				<h4>Version logicielle</h4>
			</div>

			<?php echo $v['registration']; ?>
		</td>
		<?php } ?>

	</tr>

</table>

</div>