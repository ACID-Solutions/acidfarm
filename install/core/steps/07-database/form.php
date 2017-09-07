<div class="block">
    <h2>Database</h2>
    <div><?php echo form_from_input('database:init',$inputs);  ?></div>
    <div><?php echo form_from_input('database:type',$inputs);  ?></div>
    <div><?php echo form_from_input('database:host',$inputs);  ?></div>
    <div><?php echo form_from_input('database:port',$inputs);  ?></div>
    <div><?php echo form_from_input('database:username',$inputs);  ?></div>
    <div><?php echo form_from_input('database:password',$inputs);  ?></div>
    <div><?php echo form_from_input('database:database',$inputs);  ?></div>
    <div><?php echo form_from_input('database:prefix',$inputs);  ?></div>
    <div><a href="#" onclick="DBTools.checkDB(); return false;" >Check Database</a> : <span id="check_data_base_result"></span></div>
</div>


<script type="text/javascript">
    <!--

    var DBTools = {

        checkDB : function() {
            var http = new XMLHttpRequest();
            var url = "<?php echo basename($_SERVER['REQUEST_URI']);  ?>";
            var params = "acidfarm_do=check_database"+
                "&db_type="+document.getElementsByName("database:type")[0].value+
                "&db_host="+document.getElementsByName("database:host")[0].value+
                "&db_port="+document.getElementsByName("database:port")[0].value+
                "&db_username="+document.getElementsByName("database:username")[0].value+
                "&db_password="+document.getElementsByName("database:password")[0].value+
                "&db_prefix="+document.getElementsByName("database:prefix")[0].value+
                "&database="+document.getElementsByName("database:database")[0].value;

            http.open("POST", url, true);

            //Send the proper header information along with the request
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.setRequestHeader("Content-length", params.length);
            http.setRequestHeader("Connection", "close");

            http.onreadystatechange = function() {//Call a function when the state changes.
                if(http.readyState == 4) { //&& http.status == 200

                    if (http.responseText) {
                        var res = JSON.parse(http.responseText);
                        if (res.success==true) {
                            document.getElementById("check_data_base_result").innerHTML='Success';
                        }else{
                            document.getElementById("check_data_base_result").innerHTML='Bad params for database';
                        }
                    }else{
                        document.getElementById("check_data_base_result").innerHTML='Failed';
                    }

                }
            }
            http.send(params);

            document.getElementById("check_data_base_result").innerHTML='Treatment';

        }

    }

    -->
</script>