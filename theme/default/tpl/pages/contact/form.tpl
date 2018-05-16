<?php
$ph = false; 	//Placeholders or Labels
?>

<div id="contact_inputs" class="contact_block">

    <h2><?php echo Acid::trad('contact_form_please'); ?></h2>

    <div class="input row" >
        <div class="label"><label for="input_firstname"><?php echo Acid::trad('contact_form_firstname'); ?></label></div>
        <input	id="input_firstname"
                  <?php if ($ph) {?>placeholder="<?php echo Acid::trad('contact_form_firstname'); ?>"<?php }  ?>
                  type="text" name="firstname" value="<?php echo Contact::getSession('firstname'); ?>" />
    </div>
    <div class="input row" >
        <div class="label"><label for="input_name"><?php echo Acid::trad('contact_form_lastname'); ?></label></div>
        <input id="input_name"
               <?php if ($ph) {?>placeholder="<?php echo Acid::trad('contact_form_lastname'); ?>"<?php }  ?>
               type="text" name="lastname" value="<?php echo Contact::getSession('lastname'); ?>" />
    </div>
    <div class="input row" >
        <div class="label"><label for="input_address"><?php echo Acid::trad('contact_form_address'); ?></label></div>
        <input id="input_address"
               <?php if ($ph) {?>placeholder="<?php echo Acid::trad('contact_form_address'); ?>"<?php }  ?>
               type="text" name="address" value="<?php echo Contact::getSession('address'); ?>" />
    </div>
    <div class="input row" >
        <div class="label"><label for="input_tel"><?php echo Acid::trad('contact_form_phone'); ?></label></div>
        <input id="input_tel"
               <?php if ($ph) {?>placeholder="<?php echo Acid::trad('contact_form_phone'); ?>"<?php }  ?>
               type="text" name="phone" value="<?php echo Contact::getSession('phone'); ?>" />
    </div>
    <div class="input row" >
        <div class="label"><label for="input_mail"><?php echo Acid::trad('contact_form_email'); ?></label></div>
        <input id="input_mail"
               <?php if ($ph) {?>placeholder="<?php echo Acid::trad('contact_form_email'); ?>"<?php }  ?>
               type="email" name="email" value="<?php echo Contact::getSession('email'); ?>" />
    </div>
    <div class="input row" >
        <div class="label"><label for="input_mess"><?php echo Acid::trad('contact_form_message'); ?></label></div>
					<textarea id="input_mess"
                              <?php if ($ph) {?>placeholder="<?php echo Acid::trad('contact_form_message'); ?>"<?php }  ?>
                              name="message" cols="30" rows="2" ><?php echo Contact::getSession('message'); ?></textarea>
    </div>

    <?php if ($captcha = Recaptcha::front()) { ?>
    <div class="input row" >
        <?php echo $captcha; ?>
    </div>
    <?php } ?>

    <div class="clear"></div>

    <div class="row">
        <?php if (Conf::get('contact:shield')) { ?>
            <noscript><div style="color:red;"><?php echo Acid::trad('contact_please_enable_javascript'); ?></div></noscript>
        <?php } ?>
        <input class="btn" id="contact_form_submit" type="submit" value="Envoyer" 	/>
    </div>

</div>

<?php echo Recaptcha::jsLoader(); ?>

<script type="text/javascript">
    <!--

    <?php if ($ph) { ?>
    $('body:not(.nav-old) #contact_content .label').hide();
    <?php }  ?>

    <?php if (Conf::get('contact:shield')) { ?>
    $().ready( function() {
        <?php if (Conf::get('contact:shield_time')) { ?>
        setTimeout(function() {
            <?php }  ?>
            $('#contact_form').append('<input type="hidden" name="<?php echo Conf::get('contact:shield_key'); ?>" value="<?php echo Conf::get('contact:shield_val'); ?>"  />');
            <?php if (Conf::get('contact:shield_time')) { ?>
        }, <?php echo Conf::get('contact:shield_time'); ?>  );
        <?php }  ?>
    });
    <?php }?>
    -->
</script>