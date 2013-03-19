<?php
/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Tool
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Gestion de formulaires / processus Paypal
 * 
 * Exemple d'utilisation : 
 * 
 *	//INITIALISATION
 *	$paypal = new AcidPaypal(
 *		'adress@domain.tdl',
 *		'MERCANT_ID',
 *		'PDT_CODE',
 *		function () { AcidDialog::add('info','Operation has succeeded'); Acid::log('PAYPAL','paiement success'); },
 *		function () { AcidDialog::add('info','Operation has failed');  Acid::log('PAYPAL','paiement fail'); }
 *		//,function ($tx) {  $my_array = Transactions::getList(); return !in_array($tx,$my_array); }
 *   );
 *	
 *	//PRINT PAYPAL BTN									   
 *	$paypal->addBtn('sample','btn_id',Paypal::IMG_PAY);
 *	echo  $paypal->callBtn('sample');
 *	  
 *	//PRINT PAYPAL FORM
 *	$paypal->setConfigForm(array('amount'=>10));
 *	echo  $paypal->getForm();
 * 
 * 
 *	//PDT_PAGE
 *	echo $paypal->execute();
 *	echo $paypal->dialog();
 *
 * @package   Tool
 */
class AcidPaypal {
	
        const PAYPAL_URL = 'https://www.paypal.com/';
        const PAYPAL_URL_UNSECURE = 'www.paypal.com';
        const SANDBOX_URL = 'https://www.sandbox.paypal.com';
        const SANDBOX_URL_URL_UNSECURE = 'www.sandbox.paypal.com';
		
		const PAYMENT_SUFFIX = '/cgi-bin/webscr';
		
        const IMG_PAY = 'https://www.paypalobjects.com/WEBSCR-640-20110429-1/fr_FR/FR/i/btn/btn_paynow_LG.gif';
        const IMG_DONATE = 'https://www.paypalobjects.com/WEBSCR-640-20110429-1/fr_FR/FR/i/btn/btn_donate_LG.gif';
        const IMG_PAY_CC = 'https://www.paypalobjects.com/WEBSCR-640-20110429-1/fr_FR/FR/i/btn/btn_paynowCC_LG.gif';
        const IMG_DONATE_CC = 'https://www.paypalobjects.com/WEBSCR-640-20110429-1/fr_FR/FR/i/btn/btn_donateCC_LG.gif';
        const IMG_PAYPAL = 'https://www.paypal.com/fr_FR/FR/i/btn/x-click-but5.gif';

		
        /**
         * @var string email du compte
         */
        protected $email_id = null;
        
        /**
         * @var string id marchand
         */
        protected $mercant_id = null;
        
        /**
         * @var string identifiant pdt
         */
        protected $pdt_id = null;
        
        /**
         * @var array tableau associatif des boutons paypal
         */
        protected $btns_id = array();
        
        /**
         * @var string domaine de paypal utilisé
         */
        protected $paypal_domain = 'https://www.paypal.com';
        
        /**
         * @var string retour du processus pdt
         */
        protected $pdt_result = '';
        
        /**
         * @var string dialog du processus pdt
         */
        protected $pdt_dialog = '';
        
       /**
        * @var array données retournée par le processus pdt
        */
        protected $pdt_data = array();
		
        /**
         * @var boolean vrai si en mode développeur
         */
        protected $dev_mode = false;

        /**
         * @var array configuration
         */
        protected $config=array();
        
        /**
         * @var array configuration du formulaire
         */
        protected $form_config=array();

        
        /**
         * @var function callback de succès
         */
        protected $success_function = null;
        
        /**
         * @var function callback d'echec
         */
        protected $fail_function = null;
        
        /**
         * @var function callback pdt
         */
        protected $pdt_function = null;

        /**
         * @var string formulaire html
         */
        protected $form_html  = null;
        
        /**
         * @var string chemin vers formulaire html
         */
        protected $form_file  = null;

        /**
         * Constructeur
         * @param string $email
         * @param string $mercant_id
         * @param string $pdt
         * @param function $success_function
         * @param function $fail_function
         * @param function $pdt_func
         * @param boolean $dev_mode si vrai, passe l'objet en mode dev. Le domaine paypal utilisé passera automatiquement en sandbox
         */
        public function __construct($email,$mercant_id=null,$pdt=null,$success_function=null,$fail_function=null,$pdt_func=null,$dev_mode=false) {
                $this->email_id = $email;
                $this->mercant_id = $mercant_id;
                $this->pdt_id = $pdt;
                $this->pdt_function = $pdt_func;
                $this->setSuccess($success_function);
                $this->setFail($fail_function);
				$this->setDevMode($dev_mode); 
        }

        /**
         * Ajoute un bouton paypal à l'objet
         * @param string $alias identifiant d'appel
         * @param string $btn_id identifiant paypal
         * @param string $img image type d'image utilisée
         */
        public function addBtn($alias,$btn_id,$img=self::IMG_PAY) {
                $this->btns_id[$alias]['code'] = $btn_id;
                $this->btns_id[$alias]['img'] = $img;
        }

		/**
		 * Configure l'objet
		 * @param array $array
		 * @param boolean $init si true, éfface la configuration précédente
		 */
		public function setConf($array=array(),$init=false) {
				if ($init){
					$this->config = array();
				}
				
				foreach ($array as $k => $val) {
					$this->config[$k] = $val; 
				}
        }
		
        /**
         * Configure le formulaire de l'objet
         * @param array $array
         * @param boolean $init si true, éfface la configuration précédente
         */
		public function setConfForm($array=array(),$init=false) {
				if ($init){
					$this->form_config = array();
				}
				
				foreach ($array as $k => $val) {
					$this->form_config[$k] = $val; 
				}
        }
		
        /**
         * Définit le chemin vers le formulaire
         * @param string $file
         */
		public function setFileForm($file) {
			$this->form_file = $file;
        }
		
        /**
         * Définit le formulaire HTML
         * @param string $content
         */
		public function setHtmlForm($content) {
			$this->form_html = $content;
        }
		
        /**
         * Active/désactive le mode développeur
         * Change le domaine paypal utilisé en fonction du résultat
         * @param boolean $dev_mode 
         */
		public function setDevMode($dev_mode) {
			$this->dev_mode = $dev_mode;
                
			if ($this->dev_mode) {
				$this->paypal_domain = self::SANDBOX_URL;
			}else{
				$this->paypal_domain = self::PAYPAL_URL;
			}
        }

        /**
         * Retourne un bouton paypal en HTML
         * @param string $alias identifiant d'appel du bouton
         * @return string
         */
        public function callBtn($alias) {
        	if (isset($this->btns_id[$alias])) {
            	return '
                                <form action="'.$this->paypal_domain.'/cgi-bin/webscr" method="post">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="'.$this->btns_id[$alias]['code'].'">
                                <input type="image" src="'.$this->btns_id[$alias]['img'].'" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
                                <img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110429-1/fr_FR/i/scr/pixel.gif" width="1" height="1">
                                </form>
            	';
        	}
        }

        /**
         * Retourne le dialogue PDT
         * @return string
         */
        public function dialog() {
                return $this->pdt_dialog;
        }
        
        /**
         * Retourne le resultat PDT
         * @return string
         */
 		public function result() {
 	              return $this->pdt_result;
        }
		
        
        /**
         * Retournes les données PDT
         * @return array
         */
		public function data() {
 	              return $this->pdt_data;
        }
        
        /**
         * Retourne l'url PDT en fonction du dev_mode
         * @return string
         */
        public function pdtUrl() {
        	return  $this->dev_mode ? self::SANDBOX_URL_URL_UNSECURE : self::PAYPAL_URL_UNSECURE;        	
        }
        
        /**
         * Définit le callback de succès
         * l'argument '__DATA__' peut être utilisé pour faire reférence au pdt_data
         * @param function $function
         */
        public function setSuccess($function) {
                $this->sucess_function=$function;
        }

        /**
         * Définit le callback d'échec
         * l'argument '__DATA__' peut être utilisé pour faire reférence au pdt_data
         * @param function $function
         */
        public function setFail($function) {
                $this->fail_function=$function;
        }
		
        /**
         * Définit le callback pdt
         * l'argument '__DATA__' peut être utilisé pour faire reférence au pdt_data
         * @param function $function
         */
		public function setPdt($function) {
                $this->pdt_function=$function;
        }
		
        /**
         * Définit les identifiant paypal
         * @param array $array email_id/mercant_id/pdt_id attendus
         */
		public function setIdents($array=array()) {
			if ( isset($array['email_id']) ) {
				$this->email_id = $array['email_id'];
			}
			
			if ( isset($array['mercant_id']) ) {
				$this->mercant_id = $array['mercant_id'];
			}
			
			if ( isset($array['pdt_id']) ) {
				$this->pdt_id = $array['pdt_id'];
			}
        }

        /**
         * Execute une fonction de callback
         * @param function $function
         * @return mixed
         */
        protected function exeFunction($function) {

               // Executing Process Function

                if ( is_array($function) ) {

                        $arg_1 = $function[0];
                        $arg_2 = isset($function[1]) ? $function[1] : array();
                        
                        foreach ($arg_2 as $key => $val) {
                        	if ($val=='__DATA__') {
                        		$arg_2[$key] = $this->pdt_data;
                        	}
                        }
                        
                        return call_user_func_array($arg_1,$arg_2);

                }elseif ((is_string($function) && function_exists($function))) {
                        return $function($this->pdt_data);
                }elseif ( is_callable( $function ) ) {
                        return $function($this->pdt_data);
                }


        }

        /**
         * Execute le callback de succès
         * @return mixed
         */
        protected function exeSuccess() {
                return $this->exeFunction($this->sucess_function);
        }

        /**
         * Execute le callback d'échec
         * @return mixed
         */
		protected function exeFail() {
                return $this->exeFunction($this->fail_function);
        }

        /**
         * Execute le processus PDT
         * @return mixed
         */
        public function execute() {
        //      if (!empty($_SESSION['get'])) {
                        if( $this->validate_pdt()) {
                                return $this->exeSuccess();
                        }else{
                                return $this->exeFail();
                        }
        //      }
        }

        /**
         * Ajoute $result aux resultat pdt
         * @param string $result
         */
        protected function log_results($result) {
                $this->pdt_result .= $result;
        }
 
        /**
         * Valide le traitement pdt et retourne si true en cas de succès
         * @return boolean
         */
  		protected function validate_pdt() {
                $func_success = false;
                $message = '';
                $keyarray = array();

                if (isset($_GET['tx'])) {
                        // read the post from PayPal system and add 'cmd'
                        $req = 'cmd=_notify-synch';

                        $tx_token = $_GET['tx'];

                        $auth_token = $this->pdt_id;

                        $req .= "&tx=$tx_token&at=$auth_token";

                        $header = '';

                        // post back to PayPal system to validate
                        $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
                        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
                        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
                        
                     
                        $fp = fsockopen ($this->pdtUrl(), 80, $errno, $errstr, 30); //'www.paypal.com'
                        // If possible, securely post back to paypal using HTTPS
                        // Your PHP server will need to be SSL enabled
                        // $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

                        $post_content = $header . $req;
                        $this->log_results($post_content);

                        if (!$fp) {
                                $this->log_results('HTTP ERROR');
                        } else {
                        fputs ($fp, $post_content);
                        // read the body data
                        $res = '';
                        $headerdone = false;
                        while (!feof($fp)) {
                        $line = fgets ($fp, 1024);
                        if (strcmp($line, "\r\n") == 0) {
                        // read the header
                        $headerdone = true;
                        }
                        else if ($headerdone)
                        {
                        // header has been read. now read the contents
                        $res .= $line;
                        }
                        }
  						
                        // parse the data
                        $lines = explode("\n", $res);
                        $keyarray = array();
                        if (strcmp ($lines[0], "SUCCESS") == 0) {
                        for ($i=1; $i<count($lines);$i++){
                                if (isset($lines[$i])) {
                                        list($key,$val) = explode("=", $lines[$i]);
                                        $keyarray[urldecode($key)] = urldecode($val);
                                }
                        }

                        //INFOS
                        $firstname = $keyarray['first_name'];
                        $lastname = $keyarray['last_name'];
                        $itemname = $keyarray['item_name'];
                        $amount = $keyarray['mc_gross'];
                        $devise = $keyarray['mc_currency'];


                        $secured_status = true;
                        $missmatch = array();

                        // check the payment_status is Completed
                        if ($keyarray['payment_status']!='Completed') {
                                $secured_status = false;
                                $missmatch[] = 'payment_status';
                        }
                        // check that txn_id has not been previously processed
                        if ($keyarray['txn_id']!=$tx_token) {
                                $secured_status = false;
                                $missmatch[] = 'txn_id';
                        }

                        if ($this->pdt_function !==null) {
                                        $fun = $this->pdt_function;
                                        $secured_func = $fun($keyarray);

                                        if (!$secured_func) {
                                                $secured_status = false;
                                                $missmatch[] = 'pdt_function';
                                        }
                        }

                        // check that receiver_email is your Primary PayPal email
                        if ($keyarray['receiver_email']!=$this->email_id) {
                                $secured_status = false;
                                $missmatch[] = 'receiver_email';
                        }
						
                        // check that payment_amount/payment_currency are correct


                                if ($secured_status) {
                                        // process payment

                                        //print_r($keyarray);
                                        $message .= ("<p><h3>Thank you for your purchase!</h3></p>");

                                        $message .= ("<b>Payment Details</b><br>\n");
                                        $message .= ("<p><b>Name:</b> $firstname $lastname</p>\n");
                                        $message .= ("<p><b>Item:</b> $itemname</p>\n");
                                        $message .= ("<p><b>Amount:</b> $amount $devise</p>\n");
                                        $message .= 'Your transaction has been completed, and a receipt for your purchase has been emailed to you.
                                                                <br>You may log into your account at
                                                                <a href="https://www.paypal.com">www.paypal.com</a> to view details of this transaction.<br>';

                                        $this->log_results('SUCCESS : ' . implode("\n",$keyarray));
                                        $func_success = true;

                                }else{
                                        $this->log_results('SECURED FAIL : ' . implode("\n",$missmatch) . "\n\n" . implode("\n",$keyarray));
                                        $message .= 'Authentification erronée';
                                }

                        }
                        else if (strcmp ($lines[0], "FAIL") == 0) {
                                // log for manual investigation
                                $note = 'unknown';
                                if (isset($lines[1])) {
                                        switch ($lines[1]) {
                                                case 'Error: 4003' :
                                                        $note = 'session expirée';
                                                break;

                                                case 'Error: 4002' :
                                                        $note = 'transaction erronée';
                                                break;
                                        }
                                }

                                $this->log_results('FAIL : ' . $res . ' - ' . $note);
                                $message .= $res  ;
                        }

                        }

                        fclose ($fp);

                        $this->pdt_data = $keyarray;
                        $this->pdt_dialog =     utf8_encode($message) . "\n" ;
                }
                return $func_success;
        }

        /**
         * Retourne la base interne du le formulaire de l'objet
         * @return string
         */
        protected function getPrivateForm() {
                $nom_article = isset($this->form_config['item_name']) ? $this->form_config['item_name'] : null;
                $prix = isset($this->form_config['amount']) ? $this->form_config['amount'] : null;
                $no_shipping = empty($this->form_config['no_shipping']) ? false : true;
                $url_ok = isset($this->form_config['return']) ? $this->form_config['return'] : null;
                $url_erreur = isset($this->form_config['cancel_return']) ? $this->form_config['cancel_return'] : null;
                $no_note = empty($this->form_config['no_note']) ? false : true;
                $devise = isset($this->form_config['currency_code']) ? $this->form_config['currency_code'] : 'EUR';
                $bn = isset($this->form_config['bn']) ? $this->form_config['bn'] : 'PP-BuyNowBF';
                $lc = isset($this->form_config['lc']) ? $this->form_config['lc'] : 'FR';
                $bn_img = isset($this->form_config['image']) ? $this->form_config['image'] : self::IMG_PAY;
				
				$amount_type = isset($this->form_config['amount_type']) ? $this->form_config['amount_type'] : 'none';
				
				$default_price = ($prix===null ? '' : "<input type=\"hidden\" name=\"amount\" value=\"".$prix."\">\n");
				$display_price = ($amount_type == 'text') ?  "<input type=\"text\" name=\"amount\" value=\"".$prix."\">\n" : $default_price;
				
				
				
                $inputs =
                "<input type=\"hidden\" name=\"business\" value=\"".$this->email_id."\">\n"
                .($nom_article===null ? '' : "<input type=\"hidden\" name=\"item_name\" value=\"".$nom_article."\">\n")
                .$display_price
                .( empty($no_shipping) ? '' : "<input type=\"hidden\" name=\"no_shipping\" value=\"1\">\n")
                .($url_ok===null ? '' : "<input type=\"hidden\" name=\"return\" value=\"".$url_ok."\">\n")
                .($url_erreur===null ? '' : "<input type=\"hidden\" name=\"cancel_return\" value=\"".$url_erreur."\">\n")
                .( empty($no_note) ? '' : "<input type=\"hidden\" name=\"no_note\" value=\"1\">\n")
                .($devise===null ? '' : "<input type=\"hidden\" name=\"currency_code\" value=\"".$devise."\">\n")
                ."<input type=\"hidden\" name=\"lc\" value=\"".$lc."\">\n"
                ."<input type=\"hidden\" name=\"bn\" value=\"".$bn."\">\n"
                ."<input type=\"image\" src=\"".$bn_img."\" border=\"0\" name=\"submit\" alt=\"Effectuez vos paiements via PayPal : une solution rapide, gratuite et sécurisée\">\n"
                ."<img alt=\"\" border=\"0\" src=\"".$this->paypal_domain."/fr_FR/i/scr/pixel.gif\" width=\"1\" height=\"1\">\n";

                return $inputs;
        }

        /**
         * Retourne le formulaire associé à l'objet
         * @return string
         */
        public function getForm() {
                //$form = $this->getPrivateForm();

                if ($this->form_file!==null) {
                        $form = file_get_contents($this->form_file);
                }elseif ($this->form_html !== null) {
                        $form = $this->form_html;
                }else{
                        $form = $this->getStandardForm();
                }

                return $form;
        }

        /**
         * Retourne le formulaire standard de l'objet
         * @return string
         */
        public function getStandardForm() {

                return "<form action=\"".$this->paypal_domain.self::PAYMENT_SUFFIX."\" method=\"post\">\n"
                ."<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">\n"
                .$this->getPrivateForm()
                ."</form>\n";
        }

}	