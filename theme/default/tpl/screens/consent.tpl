<script type="text/javascript">
    <!--
    var MyCookiePolicy = {

        messages: {
            'fr': 'Ce site utilise des cookies pour son fonctionnement et à des fins statistiques.',
            'en': 'This site uses cookies for its operation and for statistical purposes',
            'de': 'Diese Seite verwendet Cookies für ihren Betrieb und für statistische Zwecke',
            'it': 'Questo sito utilizza i cookie per il suo funzionamento e per scopi statistici',
            'es': 'Este sitio utiliza cookies para su funcionamiento y con fines estadísticos',
        },
        deny_messages: {
            'fr': 'Refuser',
            'en': 'Decline',
            'de': 'Verweigern',
            'it': 'Rifiutare',
            'es': 'Rechazar',
        },
        dismiss_messages: {
            'fr': 'J\'accepte !',
            'en': 'Got it!',
            'de': 'Ich akzeptiere',
            'it': 'Accetto',
            'es': 'Acepto',
        },
        revoke_messages: {
            'fr': 'Politique sur les cookies',
            'en': 'Cookie Policy',
            'de': 'Ich akzeptiere',
            'it': 'Cookie Policy',
            'es': 'Política de cookies',
        },
        learnmore_messages: {
            'fr': 'En savoir plus',
            'en': 'Learn more',
            'de': 'Mehr erfahren',
            'it': 'Per saperne di più',
            'es': 'Aprende más',
        },
        readmore_links: {
            'fr': '<?php echo Route::buildUrl('policy'); ?>',
            'en': '<?php echo Route::buildUrl('policy'); ?>',
            'de': '<?php echo Route::buildUrl('policy'); ?>',
            'it': '<?php echo Route::buildUrl('policy'); ?>',
            'es': '<?php echo Route::buildUrl('policy'); ?>',
        },
        getCookie(name) {
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
                return this.messages[this.myLanguage()];
            }

            return this.messages['fr'];
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
            //'type': 'opt-out',
            'cookie' : {
                'path' : <?php echo Acid::get('cookie:path');   ?>,
            },
            'content': {
                'message': MyCookiePolicy.myMessage(),
                'dismiss': MyCookiePolicy.myDismiss(),
                'deny': MyCookiePolicy.myDeny(),
                'link': MyCookiePolicy.myLearnMore(),
                'href': MyCookiePolicy.myReadMore(),
            },

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