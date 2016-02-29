
<?php var_dump($_POST);  ?>

<form action="" method="POST" id="myform">
    <div>
        <input type="hidden" name="dontreload" />
        <div><input type="text" name="my_field_1" /></div>
        <div><input type="file" name="my_field_2" /></div>
        <div><input type="file" name="my_field_3" /></div>
        <input type="submit">
    </div>
</form>

<?php echo Acid::tpl('tools/plupload/plupload.tpl',$v,$o);  ?>

<script type="text/javascript">
<!--

AcidPlupload.apply('[name=my_field_1]','#myform',{type:'multi','autosubmit':false});
AcidPlupload.apply('[name=my_field_2]','#myform',{});
AcidPlupload.apply('[name=my_field_3]','#myform',{'show_upload':true});

-->
</script>
