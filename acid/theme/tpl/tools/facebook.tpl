<div id="fb-root"></div>

<script>
<!--
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/fr_FR/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
-->
</script>

<div 
class="fb-like"
data-href="<?php echo $o->getPrintedURL(); ?>"
data-send="false"
<?php if ($o->isCountBox()): ?>
	data-layout="box_count" 
<?php endif ?>
data-width="<?php echo $o->getPrintedWidth(); ?>"
data-action="<?php echo $o->getPrintedVerb(); ?>"
data-show-faces="<?php echo $o->getPrintedShowFaces(); ?>">

</div>