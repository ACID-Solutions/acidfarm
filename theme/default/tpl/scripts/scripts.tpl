<?php if ($scripts = Script::getAll()) { ?>
    <?php foreach ($scripts as $script) { ?>

<!-- <?php echo $script->hscTrad('name'); ?>-->
        
        <?php if ($script->hasConsent()) {
            echo $script->get('script');
        } else { ?>

<!--

<?php echo $script->category()->cookiename(); ?> : <?php
            echo htmlspecialchars(AcidCookie::getValue($script->category()->cookiename()));
            ?>
            
<?php echo $script->cookiename(); ?> : <?php
            echo htmlspecialchars(AcidCookie::getValue($script->cookiename()));
            ?> 
            
-->
        
        <?php } ?>
    
    <?php } ?>
<?php } ?>
