<div class="content_menu">
<?php echo $v['menu']; ?>
</div>
<div class="content_display">
	<div class="content_body">
		<?php if (!empty($v['title'])) { ?>
			<div class="content_title">
				<div <?php echo$v['title_attr']; ?>>
					<?php echo $v['title']; ?>
				</div>
				<hr class="hr_title" />
			</div>
		<?php } ?>
		<?php echo $v['content']; ?>
	</div>
</div>