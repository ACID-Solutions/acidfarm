<?php
$color = '#CCCCCC';
$color2 = '#332C28';
$hr = '<hr style=" height:1px;  border:0px solid ' . $color . ';background-color:' . $color . '; color:' . $color
      . '; " />';
?>
<table style="width:100%; empty-cells: show; ">
    <tr>
        <td></td>
        <td style=" text-align:center; width:642px;">
            <table id="corps" cellpadding="0" cellspacing="0"
                   style="width:642px; font-family:arial; margin:auto; border-collapse:collapse; ">
                <tr id="header">
                    <td>
                        <table style="width:100%;">
                            <tr>
                                <td style="font-size:30px; color:<?php echo $color; ?>;">
                                    <a href="<?php echo Acid::get('url:system'); ?>">
                                        <?php echo AcidFs::base64Image(Acid::themePath('img/mail/logo.png'),
                                            Acid::get('site:name'),
                                            ['title' => Acid::get('site:name')]); ?>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr id="header_sep_top">
                    <td>
                        <?php echo $hr; ?>
                    </td>
                </tr>

                <tr id="content">
                    <td style="padding:10px; color:<?php echo $color2; ?>;" cellpadding="0" cellspacing="0">
                        <?php echo $v['content']; ?>
                    </td>
                </tr>

                <tr id="header_sep_bottom">
                    <td>
                        <?php echo $hr; ?>
                    </td>
                </tr>

                <tr id="footer">
                    <td style="font-size:10px; font-style:italic; color:<?php echo $color; ?>; ">
                        <table style="width:100%;">
                            <tr>
                                <td>
                                    <?php echo Acid::trad('mail_footer_auto_generation',
                                        ['__NAME__' => Acid::get('site:name')]); ?><br/>
                                    <?php echo Acid::trad('mail_footer_no_response', [
                                        '__LINK__' => '<a href="' . Route::buildUrl('contact', [], true) . '">'
                                                      . Acid::trad('mail_footer_contact_form') . '</a>'
                                    ]); ?>
                                </td>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td></td>
    </tr>
</table>