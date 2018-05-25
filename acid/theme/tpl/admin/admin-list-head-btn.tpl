<div class="<?php echo $o::TBL_NAME; ?>_head_btn admin_list_head_btn navbar navbar-default" >
    
    <?php if (!empty($v['links'])) { ?>
        <?php foreach ($v['links'] as $link => $name) { ?>
            <a class="admin_btn admin_list_head_btn navbar-brand" href="<?php echo $link; ?>">
                <?php echo $name; ?>
            </a>
        <?php }  ?>
    <?php }  ?>

</div>
