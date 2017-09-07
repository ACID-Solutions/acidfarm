<form class="corpus" action="" method="post">
    <div>
        <h1>Install your AcidFarm</h1>
        <input name="acidfarm_do" value="install" type="hidden">
        <div>
            <label><input type="radio" name="install_mode" value="quick" checked="checked" />Quick</label>
            <label><input type="radio" name="install_mode" value="advanced" />Advanced</label>
        </div>
        <?php
        print_steps();
        ?>
        <div><input type="submit" value="Install"/></div>
    </div>
</form>

<script type="text/javascript">
    <!--
    
    var InstallForm = {
        
        init: function() {
            $('.corpus [name=install_mode]').on('change', InstallForm.treat);
            $('.corpus [name=install_mode]').on('click', InstallForm.treat);
            
            InstallForm.treat();
        },
    
        treat: function() {
 
            if ($('.corpus [name=install_mode]:checked').val()=='advanced') {
                InstallForm.advanced();
            }else{
                InstallForm.quick();
            }
        },
        
        all: function() {
            $('.corpus .block').fadeIn();
        },
        
        quick: function() {
            InstallForm.all();
            $('.corpus [advanced]').hide();
        },
        
        advanced: function() {
            InstallForm.all();
        }
        
    };

    InstallForm.init();
    
    -->
</script>