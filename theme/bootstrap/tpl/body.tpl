<?php echo Lib::getIn('header',$v); ?>

<div  id="content"  class="container">
    <div class="row">
        <div class="col-md-12">
            <?php echo Lib::getIn('content',$v); ?>
        </div>
    </div>

    <hr />
    <footer id="footer">
        <?php echo Lib::getIn('footer',$v); ?>
    </footer>
</div>