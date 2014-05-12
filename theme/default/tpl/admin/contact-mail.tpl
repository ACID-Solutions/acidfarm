<?php
	$base_url = Acid::get('url:scheme').Acid::get('url:domain').'/';
	//$base_img = $base_url.$g['acid']['theme'].'/theme/'.$g['acid']['theme'].'/img/';
	$base_img = $base_url.Acid::get('url:img');
?>

<table>
<tr><td style="padding-top:10px;">
	<?php echo Acid::trad('contact_post_mail_head', array('__SITE__'=>Acid::get('site:name'))); ?>
</td></tr>
<?php  if (!empty($v['profile'])) { ?>
<tr><td  style="padding-top:10px;" >
	<table cellspacing="0" cellpadding="0" border="1" style="border-collapse:collapse; border-style:solid; border-color:#020202;" >
		<?php foreach ($v['profile'] as $label => $value) { ?>
				<tr>
					<td style="padding:2px 5px; border-style:solid; border-color:#020202;">
						<?php echo $label; ?>
					</td>
					<td style="padding:2px 5px; border-style:solid; border-color:#020202;">
						<?php echo $value; ?>
					</td>
				</tr>
		<?php } ?>
	</table>
</td></tr>
<?php } ?>

<?php  if (!empty($v['cause'])) { ?>
<tr><td style="padding-top:10px;">
	<h3 style="margin-bottom:5px; font-size:14px;" ><?php echo Acid::trad('contact_post_mail_user_wants'); ?></h3>
	<ul style="margin:0px; list-style-type:disc; padding-left:25px; ">
		<?php foreach ($v['cause'] as $label) { ?>
				<li><?php echo $label; ?></li>
		<?php } ?>
	</ul>
</td></tr>
<?php } ?>

<?php  if (!empty($v['message'])) { ?>
<tr><td style="padding-top:10px;">
	<h3 style="margin-bottom:5px; font-size:14px;" ><?php echo Acid::trad('contact_post_mail_msg_sent'); ?></h3>
	<?php foreach ($v['message'] as $label => $value) { ?>
	<?php echo nl2br($value); ?><br />
	<?php } ?>
</td></tr>
<?php } ?>
</table>