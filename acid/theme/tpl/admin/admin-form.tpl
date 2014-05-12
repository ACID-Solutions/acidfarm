<div id="admin_form" >
	<form method="post" action="" >
		<div>
			<input type="hidden" name="do" value="login" />
			<input type="hidden" name="auto" value="1" />
			<input type="hidden" name="no_redirect" value="1" />
			
			<?php if  ($v['msg']) { ?>
				<div><?php echo $v['msg']; ?></div>
			<?php } ?>
				
			<table>
				<tr><td class="label" ><?php echo Acid::trad('login'); ?> </td><td><input type="text" name="login" value="" size="20"/></td></tr>
				<tr><td class="label" ><?php echo Acid::trad('password'); ?> </td><td><input type="password" name="pass" value="" size="20"/></td></tr>
				<tr><td>&nbsp;</td>
				<td><input type="submit" name="submit" value="Connexion" class="input_btn adminbtn_connexion" /></td>
				</tr>
			</table>
		</div>
	</form>
</div>