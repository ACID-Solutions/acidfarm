<div class="block">
    <h2>Mail Server</h2>
    <div><?php echo form_from_input('email:method', $inputs); ?></div>
    <div>If SMTP :</div>
    <ul>
        <li>
            <div><?php echo form_from_input('email:smtp:host', $inputs); ?></div>
        </li>
        <li>
            <div><?php echo form_from_input('email:smtp:user', $inputs); ?></div>
        </li>
        <li>
            <div><?php echo form_from_input('email:smtp:pass', $inputs); ?></div>
        </li>
        <li>
            <div><?php echo form_from_input('email:smtp:port', $inputs); ?></div>
        </li>
        <li>
            <div><?php echo form_from_input('email:smtp:secure', $inputs); ?></div>
        </li>
        <li>
            <div><?php echo form_from_input('email:smtp:debug', $inputs); ?></div>
        </li>
    </ul>
    <div>
        <a href="#" onclick="EmailTools.checkEmail(); return false;">Send a test email</a> :
        <span id="check_email_result"></span>
        <ul>
            <li>
            From : <input type="text" name="email:testfrom" value=""/>
            To : <input type="text" name="email:testto" value=""/>
            </li>
        </ul>
       
    </div>
</div>


<script type="text/javascript">
    <!--
    
    var EmailTools = {
        
        checkEmail: function() {
            var http = new XMLHttpRequest();
            var url = '<?php echo basename($_SERVER['REQUEST_URI']);  ?>';
            
            if (!document.getElementsByName('email:testfrom')[0].value) {
                document.getElementsByName('email:testfrom')[0].value = document.getElementsByName(
                    'site:email')[0].value;
            }
            
            var params = 'acidfarm_do=check_mail' +
                '&mail_method=' + document.getElementsByName('email:method')[0].value +
                '&mail_host=' + document.getElementsByName('email:smtp:host')[0].value +
                '&mail_port=' + document.getElementsByName('email:smtp:port')[0].value +
                '&mail_username=' + document.getElementsByName('email:smtp:user')[0].value +
                '&mail_password=' + document.getElementsByName('email:smtp:pass')[0].value +
                '&mail_secure=' + document.getElementsByName('email:smtp:secure')[0].value +
                '&mail_to=' + document.getElementsByName('email:testfrom')[0].value +
                '&mail_from=' + document.getElementsByName('email:testto')[0].value;
            
            http.open('POST', url, true);
            
            //Send the proper header information along with the request
            http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            http.setRequestHeader('Content-length', params.length);
            http.setRequestHeader('Connection', 'close');
            
            http.onreadystatechange = function() {//Call a function when the state changes.
                if (http.readyState == 4) { //&& http.status == 200
                    
                    if (http.responseText) {
                        var res = JSON.parse(http.responseText);
                        
                        if (res.result === true) {
                            document.getElementById('check_email_result').innerHTML = 'Email was sent, please ' +
                                'check your inbox.';
                        }
                        else {
                            if (res.result === false) {
                                document.getElementById('check_email_result').innerHTML = 'Failed :  Bad params for ' +
                                    'email';
                            }
                            else {
                                document.getElementById('check_email_result').innerHTML = res.result;
                            }
                        }
                    }
                    else {
                        document.getElementById('check_email_result').innerHTML = 'Failed';
                    }
                    
                }
            };
            http.send(params);
            
            document.getElementById('check_email_result').innerHTML = 'Treatment';
            
        },
        
    };
    
    -->
</script>