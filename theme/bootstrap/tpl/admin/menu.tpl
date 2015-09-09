<div class="navbar-header">

    <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
        <span class="sr-only"><?php echo Acid::trad('toggle'); ?></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>

    <div class="navbar-collapse collapse" >
        <?php
        $my_key = Lib::getIn('page',$_GET);

        if ($menuelts = Lib::getIn('elts',$v)) {
            $i = 0;
            //BOUCLE :: POUR CHAQUE ELEMENT
            foreach ($menuelts as $key => $tab) {
                $link  = Lib::getIn('unclickable',$tab) ? '' : $tab['url'];
                $title = $tab['name'];
                $children = Lib::getIn('elts',$tab);

                $selected = ($my_key == $key) ? ' active' : 'unselected';
                $sep = $i ? ' - ' : '' ;
                ?>
                <?php //echo $sep; ?>
                <div class="menu navbar-brand <?php echo $selected; ?> <?php echo $children ? 'dropdown' : ''; ?> ">

                <?php if($link) {  ?>
                    <a  href="<?php echo $link; ?>">
                        <?php echo $title; ?>
                    </a>
                <?php }else{ ?>
                    <span>
                        <?php echo $title; ?>
                    </span>
                <?php }  ?>

                <?php
                if ($children) {
                    ?>
                    <ul class="dropdown-menu">
                        <?php
                        foreach ($children as $skey => $child) {
                            $slink  = $child['url'];
                            $stitle = $child['name'];
                            $selected = ($my_key == $skey) ? ' selected active' : 'unselected';
                            ?>
                            <li class="<?php echo $selected; ?>"  ><a href="<?php echo $slink; ?>"><?php echo $stitle; ?></a></li>
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
        }
        ?>
        <div class="menu navbar-brand">
            <a href="<?php echo Acid::get('url:folder'); ?>">
                <?php echo Acid::trad('admin_menu_back'); ?>
            </a>
        </div>
        <div class="menu navbar-brand">
            <a href="#" onclick="document.getElementById('unlog_form').submit();return false;">
                <?php echo Acid::trad('admin_menu_unlog'); ?>
            </a>
            <form method="post" action="" id="unlog_form"><div><input type="hidden" name="do" value="logout" /></div></form>
        </div>
    </div>
</div>


