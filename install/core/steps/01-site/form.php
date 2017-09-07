<div class="block">
    <h2>Site Profile</h2>
    <div><?php echo form_from_input('site:name',$inputs);  ?></div>
    <div><?php echo form_from_input('site:email',$inputs);  ?></div>
    <div><?php echo form_from_input('site:salt',$inputs);  ?></div>
</div>
<div class="block">
    <h2>Site Url</h2>
    <div><?php echo form_from_input('site:scheme',$inputs);  ?></div>
    <div><?php echo form_from_input('site:domain',$inputs);  ?></div>
    <div><?php echo form_from_input('site:folder',$inputs);  ?></div>
</div>
