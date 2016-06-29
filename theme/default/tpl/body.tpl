<?php
$navold = Lib::getIn('nav_old',$v);

$htag = $navold ? 'div':'header';
$ftag = $navold ? 'div':'footer';
?>


<<?php echo $htag;  ?> id="header" >
    <?php echo Lib::getIn('header',$v); ?>
</<?php echo $htag;  ?>>


<div id="wrapper">
    <div id="container">
        <div id="content">
            <?php echo Lib::getIn('content',$v); ?>
        </div>
    </div>
</div>

<<?php echo $ftag;  ?> id="footer" >
<?php echo Lib::getIn('footer',$v); ?>
</<?php echo $ftag;  ?>>
