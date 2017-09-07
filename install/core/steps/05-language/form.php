<div class="block" advanced>
    <h2>Lang</h2>
    <div><?php echo form_from_input('lang:custom', $inputs); ?></div>
    <ul>
        <li>
            <div><?php echo form_from_input('lang:multilingual', $inputs); ?></div>
        </li>
        <li>
            <div><?php echo form_from_input('lang:available', $inputs); ?></div>
        </li>
        <li>
            <div><?php echo form_from_input('lang:default', $inputs); ?></div>
        </li>
    </ul>
</div>