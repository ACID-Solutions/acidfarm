<script type="text/javascript">
    <!--
    var MyCookiePolicy = {
        
        messages: <?php echo json_encode(Lib::getIn('messages', $v))  ?>,
        deny_messages:  <?php echo json_encode(Lib::getIn('deny_btns', $v))  ?>,
        dismiss_messages:  <?php echo json_encode(Lib::getIn('dismiss_btns', $v))  ?>,
        revoke_messages:  <?php echo json_encode(Lib::getIn('revoke_btns', $v))  ?>,
        learnmore_messages: <?php echo json_encode(Lib::getIn('learnmore_btns', $v))  ?>,
        readmore_links: <?php echo json_encode(Lib::getIn('readmore_links', $v))  ?>,
        getCookie : function(name) {
            var dc = document.cookie;
            var prefix = name + '=';
            var begin = dc.indexOf('; ' + prefix);
            if (begin == -1) {
                begin = dc.indexOf(prefix);
                if (begin != 0) {
                    return null;
                }
            }
            else {
                begin += 2;
                var end = document.cookie.indexOf(';', begin);
                if (end == -1) {
                    end = dc.length;
                }
            }
            
            return decodeURI(dc.substring(begin + prefix.length, end));
        },
        myDisable: function(obj) {
            //obj.revokeChoice();
            //window.location = 'https://tools.google.com/dlpage/gaoptout/';
        },
        myEnable: function(obj) {
        
        },
        myLanguage: function() {
            if (document.documentElement.lang != undefined) {
                return document.documentElement.lang;
            }
            
            if (navigator.languages != undefined) {
                return navigator.languages[0].split('-')[0];
            }
            else {
                return navigator.language.split('-')[0];
            }
        },
        myMessage: function() {
            
            if (this.messages[this.myLanguage()] != undefined) {
                return this.messages[this.myLanguage()].replace('{{href}}', this.myReadMore());
            }
            
            return this.messages['fr'].replace('{{href}}', this.myReadMore());
        },
        myDismiss: function() {
            if (this.dismiss_messages[this.myLanguage()] != undefined) {
                return this.dismiss_messages[this.myLanguage()];
            }
            
            return this.dismiss_messages['fr'];
        },
        myDeny: function() {
            if (this.deny_messages[this.myLanguage()] != undefined) {
                return this.deny_messages[this.myLanguage()];
            }
            
            return this.deny_messages['fr'];
        },
        myRevoke: function() {
            
            if (this.revoke_messages[this.myLanguage()] != undefined) {
                var msg = this.revoke_messages[this.myLanguage()];
            }
            else {
                var msg = this.revoke_messages['fr'];
            }
            
            if (!msg) {
                return '<div class="cc-revoke {{classes}}">Cookie Policy</div>';
            }
            
            return '<div class="cc-revoke {{classes}}">' + msg + '</div>';
        },
        myLearnMore: function() {
            if (this.learnmore_messages[this.myLanguage()] != undefined) {
                return this.learnmore_messages[this.myLanguage()];
            }
            
            return this.learnmore_messages['fr'];
        },
        myReadMore: function() {
            if (this.readmore_links[this.myLanguage()] != undefined) {
                return this.readmore_links[this.myLanguage()];
            }
            
            return this.readmore_links['fr'];
        },
        myContentConfig: function() {
            var json = {};
            
            if (MyCookiePolicy.myMessage()) {
                json['message'] = MyCookiePolicy.myMessage();
            }
            
            if (MyCookiePolicy.myDismiss()) {
                json['dismiss'] = MyCookiePolicy.myDismiss();
            }
            
            if (MyCookiePolicy.myDeny()) {
                json['deny'] = MyCookiePolicy.myDeny();
            }
            
            if (MyCookiePolicy.myLearnMore()) {
                json['link'] = MyCookiePolicy.myLearnMore();
            }
            
            if (MyCookiePolicy.myReadMore()) {
                json['href'] = MyCookiePolicy.myReadMore();
            }
            
            return json;
        },
        
    };
    
    window.addEventListener('load', function() {
        
        window.cookieconsent.initialise({
            'palette': {
                'popup': {
                    'background': '#edeff5',
                    'text': '#838391',
                },
                'button': {
                    'background': '#4b81e8',
                },
            },
            'revokable': true,
            'revokeBtn': MyCookiePolicy.myRevoke(),
            'position': 'bottom-left',
            'theme': 'classic',
            'type': '<?php echo Lib::getIn('consent_type', $v);  ?>',
            'cookie': {
                'path': '<?php echo Acid::get('cookie:path');   ?>',
            },
            'content': MyCookiePolicy.myContentConfig(),
            
            onInitialise: function(status) {
                var type = this.options.type;
                var didConsent = this.hasConsented();
                if (type == 'opt-in' && didConsent) {
                    // enable cookies
                    MyCookiePolicy.myEnable(this);
                }
                
                if (type == 'opt-out' && !didConsent) {
                    // disable cookies
                    MyCookiePolicy.myDisable(this);
                    setTimeout(function() { $('.cc-revoke').fadeIn(); }, 1000);
                }
                
                if (didConsent) {
                    setTimeout(function() { $('.cc-revoke').fadeIn(); }, 1000);
                }
            },
            
            onStatusChange: function(status, chosenBefore) {
                var type = this.options.type;
                var didConsent = this.hasConsented();
                if (type == 'opt-in' && didConsent) {
                    // enable cookies
                    MyCookiePolicy.myEnable(this);
                }
                if (type == 'opt-out' && !didConsent) {
                    // disable cookies
                    MyCookiePolicy.myDisable(this);
                }
            },
            
            onRevokeChoice: function() {
                var type = this.options.type;
                if (type == 'opt-in') {
                    // disable cookies
                    MyCookiePolicy.myDisable(this);
                }
                if (type == 'opt-out') {
                    // enable cookies
                    MyCookiePolicy.myEnable(this);
                }
            },
        });
    });
    -->
</script>