<div class="navbar-header">

    <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
        <span class="sr-only"><?php echo Acid::trad('toggle'); ?></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>

    <div class="navbar-collapse collapse" >
        <?php
        $my_key = AcidRouter::searchKey(AcidRouter::getParamById(0));
        $my_key =  $my_key ? $my_key : AcidRouter::getParamById(0);
        $my_key = ($my_key == 'default') ? 'index' : $my_key;

        $i = 0;
        //BOUCLE :: POUR CHAQUE ELEMENT
        foreach ($v['elts'] as $key => $tab) {
        $link  = $tab['url'];
        $title = $tab['name'];
        $children = Lib::getIn('elts',$tab);

        $selected = ($my_key == $key) ? ' selected' : 'unselected';
        $sep = $i ? ' - ' : '' ;
        ?>
        <?php //echo $sep; ?>
        <div class="menu navbar-brand <?php echo $selected; ?> <?php echo $children ? 'dropdown' : ''; ?> ">
        <a  href="<?php echo $link; ?>"><?php echo $title; ?></a>
        <?php
        if ($children) {
            ?>
            <ul class="dropdown-menu">
                <?php
                foreach ($children as $skey => $child) {
                    $slink  = $child['url'];
                    $stitle = $child['name'];
                    $selected = ($my_key == $skey) ? ' selected' : 'unselected';
                    ?>
                    <li><a  href="<?php echo $slink; ?>"><?php echo $stitle; ?></a></li>
                    <?php
                }
                ?>
            </ul>
            <?php
        }
        ?>
    </div>

    <?php
    $i++;
    }
    //FIN BOUCLE
    ?>
</div>
</div>
