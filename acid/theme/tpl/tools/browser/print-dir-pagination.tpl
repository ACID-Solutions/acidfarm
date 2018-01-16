<?php
$page = isset($v['page']) ? $v['page'] : false;
$nb_pages = isset($v['nb_pages']) ? $v['nb_pages'] : 1;
$nb_elts = isset($v['nb_elts']) ? $v['nb_elts'] : 0;
$nb_elts_page = isset($v['nb_elts_page']) ? $v['nb_elts_page'] : 0;
$pagination = isset($v['pagination']) ? $v['pagination'] : false;

$start = ($page - 1) * $pagination + 1;
$stop = $start + $nb_elts_page - 1;
$start = $start > $nb_elts ? $nb_elts : $start;
?>
<hr />

<div class="fsb_navigation" >
    <div class="fsb_navigation_infos">
        <?php echo Acid::trad('admin_list_total_elts', [
            '__TOTAL__' => $nb_elts, '__NB__' => $nb_elts_page,
            '__START__' => $start, '__STOP__' => $stop
        ]); ?>
    </div>
    
    <?php if ($page) { ?>
        <nav class="navbar navbar-default fsb_navigation_pages">
            <?php if ($page > 1) { ?>
                <a href="<?php echo AcidUrl::build(['fsb_page' => ($page - 1)]); ?>">
                    &lt;
                </a>
            <?php } ?>
            <span><?php echo $page; ?> / <?php echo $nb_pages ? $nb_pages : 1; ?></span>
            <?php if ($page < $nb_pages) { ?>
                <a href="<?php echo AcidUrl::build(['fsb_page' => ($page + 1)]); ?>">
                    &gt;
                </a>
            <?php } ?>
        </nav>
    <?php } ?>

    <div class="clear clearfix"></div>
</div>
