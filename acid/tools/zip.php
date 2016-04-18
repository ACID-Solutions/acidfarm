<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tool
 * @version   0.1
 * @since     Version 0.3
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * GENERATEUR ZIP PHPMYADMIN
 * @package   Acidfarm\Tool
 */
class zipfile
{ 

	/**
	 * @var array datasec
	 */
    var $datasec      = array();   
    
    /**
     * @var array ctrl_dir 
     */
    var $ctrl_dir     = array();  
    
    /**
     * @var string eof_ctrl_dir
     */
    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00"; 
    
    /**
     * @var int old_offset
     */
    var $old_offset   = 0;
 
    /**
     * unix2DosTime
     * @param int $unixtime
     * @return boolean
     */
    function unix2DosTime($unixtime = 0) {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

        if ($timearray['year'] < 1980) {
            $timearray['year']    = 1980;
            $timearray['mon']     = 1;
            $timearray['mday']    = 1;
            $timearray['hours']   = 0;
            $timearray['minutes'] = 0;
            $timearray['seconds'] = 0;
        } 

        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
                ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    } 
   
    /**
     * Ajoute un fichier
     * @param unknown_type $data
     * @param unknown_type $name
     * @param unknown_type $time
     */
    function addFile($data, $name, $time = 0)
    {
        $name     = str_replace('\\', '/', $name);

        $dtime    = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
                  . '\x' . $dtime[4] . $dtime[5]
                  . '\x' . $dtime[2] . $dtime[3]
                  . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');

        $fr   = "\x50\x4b\x03\x04";
        $fr   .= "\x14\x00";          
        $fr   .= "\x00\x00";           
        $fr   .= "\x08\x00";           
        $fr   .= $hexdtime;             

        $unc_len = strlen($data);
        $crc     = crc32($data);
        $zdata   = gzcompress($data);
        $zdata   = substr(substr($zdata, 0, strlen($zdata) - 4), 2); 
        $c_len   = strlen($zdata);
        $fr      .= pack('V', $crc);           
        $fr      .= pack('V', $c_len);           
        $fr      .= pack('V', $unc_len);         
        $fr      .= pack('v', strlen($name));    
        $fr      .= pack('v', 0);                
        $fr      .= $name;
  
        $fr .= $zdata;
        
        $this -> datasec[] = $fr;

        $cdrec = "\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00";              
        $cdrec .= "\x14\x00";                
        $cdrec .= "\x00\x00";             
        $cdrec .= "\x08\x00";              
        $cdrec .= $hexdtime;                 
        $cdrec .= pack('V', $crc);          
        $cdrec .= pack('V', $c_len);         
        $cdrec .= pack('V', $unc_len);      
        $cdrec .= pack('v', strlen($name));
        $cdrec .= pack('v', 0);            
        $cdrec .= pack('v', 0);            
        $cdrec .= pack('v', 0);            
        $cdrec .= pack('v', 0);          
        $cdrec .= pack('V', 32);           

        $cdrec .= pack('V', $this -> old_offset); 
        $this -> old_offset += strlen($fr);

        $cdrec .= $name;

        $this -> ctrl_dir[] = $cdrec;
    } 

	/**
	 * Retourne un fichier
	 * @return string
	 */
    function file()
    {
        $data    = implode('', $this -> datasec);
        $ctrldir = implode('', $this -> ctrl_dir);

        return
            $data .
            $ctrldir .
            $this -> eof_ctrl_dir .
            pack('v', sizeof($this -> ctrl_dir)) . 
            pack('v', sizeof($this -> ctrl_dir)) .  
            pack('V', strlen($ctrldir)) .           
            pack('V', strlen($data)) .              
            "\x00\x00";                            
    } 

} 



/**
 * ACIDZIP
 * @package   Acidfarm\Tool
 */
class AcidZip {

	/**
	 * @var string nom du fichier
	 */
	public $name='';
	
	/**
	 * @var string chemin vers le fichier
	 */
	protected $path=null;
	
	/**
	 * @var array arborescence du zip
	 */
	protected $tree=array();
	
	/**
	 * @var array les logs
	 */
	protected $logs=array();
	
	/**
	 * @var array les fichiers à exclure lors des différents traitements
	 */
	protected $filter=array('.htaccess','.','..','.icon');
	
	/**
	 * @var string type de filtre à effectuer
	 */
	protected $filter_mode='forbid';
	
	/**
	 * @var object objet ZIP
	 */
	protected $zip=null;
	
	
	/**
	 * Constructeur
	 * @param string $path chemin vers le ZIP
	 */
	public function __construct($path=null) {
		if ($path && is_file($path)) {
			$this->setName(basename($path));
			$this->path=$path;
			$this->zip=new ZipArchive;
		} elseif($path) {
			$this->callError('Fichier introuvable.','Constructeur');
		}
	}
	
	
	/**
	 * Initialiser le ZIP
	 * @param string $path
	 * @return boolean
	 */
	public function init($path) {
		$success=false;
		
		if (get_class($this->zip) == 'ZipArchive') {
			$this->zip->close();
		}
		
		$this->zip=new ZipArchive;
		
		if (!is_file($path)) {
			 // $zipfile=new zipfile();
			
			 // $fd = fopen ($path, "wb");
			 // $out = fwrite ($fd, $zipfile -> file());
			 // fclose ($fd);
			 
			//touch($path);
			
			                      
			
			// if ($zip=new ZipArchive($path)) {
			//echo 'path : ' . $path;
			if ($this->zip->open($path, ZIPARCHIVE::CREATE) === true) {
				//$zip->close();
				//var_dump($zip);
				//echo $zip->addFile('.htaccess') ? 'added' : 'not founded';
				//$zip->close();
				//echo 'fin';
				//exit();
				// $zip->addFile('.htaccess');
				$this->setName(basename($path));
				$this->path=$path;
				
				
				
				$success=true;
				$this->logs[]='Création du fichier '.$path.' <br />';
			}else{
				$this->callError('La création est un échec dans '.$path.'.','Init');
			}
			
		}else{
			if ($this->zip->open($path)) {
				$this->path=$path;
				$this->setName(basename($path));
				$success=true;
				$this->logs[]='Ouverture du fichier '.$path.' <br />';
			}else{
				$this->callError('Accès impossible à '.$path.'.','Init');
			}
		}
		return $success;
	}
	
	/**
	 * Génère une erreur
	 * @param string $error
	 * @param string $caller
	 * @param boolean $warning
	 */
	protected function callError($error,$caller=null,$warning=true) {
		if ($caller) {
		$error='{'.$caller.'}'.$error;
		}
		
		//Acid::log('error',$erreur);
		if ($warning) {
			trigger_error($error,E_USER_WARNING);
		}
		
		$this->logs[]=$error;
	}
	
	/**
	 * Sauvegarde le zip vers le chemin $path
	 * @param string $path
	 * @return boolean
	 */
	protected function saveZip($path) {	
		return copy($this->path,$path);
	}
	
	/**
	 * Nettoie le nom de fichier designé par $name
	 * @param string $name
	 * @return mixed
	 */
	protected function cleanFileName($name) {
		return str_replace(array('/','[','\\',':','*','?','"','<','>','|','°','{','}',']'),'-',$name);
	}
	
	/**
	 * Nettoie le chemin désigné par $str_path
	 * @param string $str_path 
	 * @return string
	 */
	protected function cleanPath($str_path) {
	
		$rep=array();
		
		foreach (explode('/',$str_path) as $val) {
			if (($val) && ($val!='..') ) {
				$rep[]=$this->cleanFileName($val);
			}else{
				if ($val) {
					array_pop($rep);
				}
			}

		}
		
		
		return implode('/',$rep);
	}
	
	/**
	 * Créer un dossier dans le zip
	 * @param string $t_path chemin du dossier
	 * @return boolean
	 */
	public  function mkdir($t_path) {
	
		$t_path=$this->cleanPath($t_path);
		if (!$this->zip) {
			$zip=new ZipArchive;
		}else{
			$zip=$this->zip;
		}
		
		if ($zip->open($this->path, ZIPARCHIVE::CREATE)) {
		
			$suc=$zip->addEmptyDir($t_path);
			$success=true;
			if ($suc) {
				$this->log[]='Création du dossier '.$t_path.' <br />';
			}else{
				$this->log[]='Le Dossier existe déjà : '.$t_path.' <br />';
			}
			
		}else{
			$this->callError('Le fichier source est introuvable.','mkdir');
		}
	
		return $success;
	}
	
	/**
	 * Définit le nom du zip
	 * @param string $name
	 */
	public function setName($name) {
		
		$this->name=$this->cleanFileName($name);

	}
	
	/**
	 * Retourne le chemin vers le zip
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}
	
	
	/**
	 * Configure le filtrage du zip
	 * @param array $filter les filtres
	 * @param boolean $erase si true, éfface la configuration précédente
	 * @param string $mode mode de filtrage
	 */
	public function setfilter($filter,$erase=false,$mode=null) {
		
		if (is_array($filter)) {
			if ($erase) {
				$this->filter=$filter;
			}else{
				$this->filter=array_merge($this->filter,$filter);
			}
		}
		
	
		if ($mode) {
			$this->filter_mode=$mode;
		}
			
		
	}
	
	/**
	 * Retourne vrai si le fichier soumis est traitable
	 * @param string $file chemin vers le fichier
	 * @param string $path repertoire racine
	 * @return boolean
	 */
	protected function isValideFile($file,$path=null) {
		
		switch ($this->filter_mode) {
			case 'allow' :
				$access=in_array($file,$this->filter);
			break;
			
			case 'allowpath' :
				$comp = $path.'/'.$file;
				if ($path===null) {
					$access = true;
				}else{
					$access=  in_array($comp,$this->filter);
				}
			break;
			
			default :
				$access=!in_array($file,$this->filter);
			break;
		}
		
		return ( ($file) && ($file!='.') && ($file!='..') && ($access) );
	}
	
	/**
	 * Copie le contenu du dossier $path_a dans le repertoire $path_b du zip
	 * @param string $path_a
	 * @param string $path_b
	 * @return boolean
	 */
	protected function cloneElt($path_a,$path_b) {
		$success=false;
		$s_res=true;
		$tab_e=array();
		//on accède à notre fichier zip
		
		if (!$this->zip) {
			$this->zip = new ZipArchive;
		}
		
		$zip = $this->zip;
		
						
		if ($zip->open($this->path)) {
			
			//si on peut ouvrir le dossier source
			if ($hd=opendir($path_a)) {
							
				//on tente de créer le dossier dans notre zip
				if ($this->mkdir($path_b)) {
				
					$this->logs[]= 'Création du dossier '.$path_b.' <br />';		
					$success=true;
					
					//$this->CloneElt() ??
					while ($file = readdir($hd)) {
						
						if ( $this->isValideFile($file,$path_a) ) {
							$path_src=$path_a.'/'.$file;
							$path_dest=$path_b.'/'.$file;
							
							if (basename($path_src)) {	
							
								if (is_file($path_src)) {
										$this->logs[] = 'Fichier '.$file .' => '.$path_src.' - '.$path_dest.'<br />';
										$s_res = $this->addFile($path_src,$path_dest);
								}else{
										$this->logs[]='Dossier '.$file .' => '.$path_src.' - '.$path_dest.'<br />';
										$s_res=$this->cloneElt($path_src,$path_dest);	
								}
								
							}
							
							if (!$s_res) {
								$tab_e[] = $path_src;
							}
						}
						
						$success=($success and $s_res);
					}
					
					if (!$success) {
						$detail='';
						Foreach ($tab_e as $e) {
							$detail.=' '.$e.',';
						}
						$this->callError('Erreur lors de la copie de l\'element de source Serveur[ '.$path_a.' ] vers Zip[ '.$path_b.' ] ('.$detail.')','cloneElt');
					}
					
				}else{
					$this->callError('Impossible de créer le dossier de destination.','cloneElt');
				}
				
				closedir($hd);			
			}else{
				$this->callError('Impossible d\'ouvrir le dossier.','cloneElt');
			}	
			
			// $zip->close();
			
		}else{
			$this->callError('Impossible d\'initialiser le zip.','cloneElt');
		}	
			
		
		return $success;
	}
	
	/**
	 * Ajoute un dossier au zip
	 * @param string $path chemin vers le dossier
	 * @param string $t_path racine
	 * @return boolean
	 */
	public function addDir($path,$t_path=null) {
		$success=false;
		
		//pas de destination définie => dest = src
		if ($t_path==null) {
			$t_path=basename($path);
			$this->logs[]='La destination devient '.$t_path.'.<br />';
		}
		
		
		//on nettoie les chemins renseignés en paramètre
		//$path=$this->cleanPath($path);
	
		$t_path=$this->cleanPath($t_path);
	
		//la source est un dossier
		if (is_dir($path)) {
		
			//on a un dossier de destination de renseigné
			if (basename($t_path)) {
					
				//on copie le dossier et ses enfants
				$success=$this->cloneElt($path,$t_path);
			
			}else{
				$this->callError('Le dossier de destination n\a pas de nom','addDir');
			}
		
		}else{
			$this->callError('La source n\'est pas un dossier.','addDir');
		}
		
		
		return $success;
	}
	
	/**
	 * Ajoute un fichier au zip
	 * @param string $path chemin vers le fichier
	 * @param unknown_type $t_path racine
	 * @return boolean
	 */
	public function addFile($path,$t_path=null) {
		$success=false;
		if ($t_path==null) {
			$t_path=basename($path);
			$this->logs[]='t_path name change as '.$t_path.'<br />';
		}
		$t_path=$this->cleanPath($t_path);
		
		if (basename($t_path)) {
			if ( is_file($path) ) {
				
				if ( $this->isValideFile(basename($path)) ) {
					if (!$this->zip) {
						$this->zip = new ZipArchive;
					}
					$zip = $this->zip;
					
					if ($zip->open($this->path)) {
						$success=$zip->addFile($path,$t_path);	
						$zip->close();
					}
				}else{
					$this->callError('Ce fichier est interdit.','addFile');
				}
				
			}else{
				$this->callError('La source n\'est pas un fichier.','addFile');
			}
		}else{
			$this->callError('Le fichier de destination n\'a pas de nom','addFile');			
		}
		
		return $success;
	}
	
	/**
	 * Supprime un fichier du zip
	 * @param string $t_path chemin vers le fichier
	 * @return boolean
	 */
	public function removeFile($t_path) {
		$success=false;

		$t_path=$this->cleanPath($t_path);
		
		if (basename($t_path)) {

				if (!$this->zip) {
					$this->zip = new ZipArchive;
				}
		
				$zip = $this->zip;
				
				if ($zip->open($this->path)) {
					$success=$zip->deleteName($t_path);	
					$zip->close();
				}

		}else{
			$this->callError('Le fichier  n\'a pas de nom','removeFile');
		}
		
		return $success;
	}
	
	/**
	 * Lit le fichier zip
	 * @param boolean $close si true, conclut la méthode avec un exit();
	 */
	public function readZip($close=true) {
		header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Pragma: no-cache');
		
		header('Content-Type: application/zip'); // ZIP file
		header('Content-Disposition: attachment; filename="'.$this->name.'"'); 
		header('Content-Length: '.filesize($this->path));
		

		readfile($this->path);
			
		if ($close) {
			exit();
		}
		
	}
	
	/**
	 * Génère un zip à partir du repertoire $folder
	 * @param string $folder chemin vers le repertoire à zipper
	 * @param string $name nom du fichier
	 * @param string $tmp_path repertoire de destination du zip
	 * @param array $filter filtre à utiliser lors de la generation du zip
	 * @param string $filter_mode type de filtrage
	 */
	public function generateZip($folder,$name=null,$tmp_path=null,$filter=array(),$filter_mode=null) {
		if ($tmp_path===null) {
			$tmp_path='./';
		}
		
		
		if ($name===null) {
			$name='copy_'.basename($folder).'.zip';
		}
		
		$name=$this->cleanFileName($name);
		
		// $path=$this->cleanPath($_SERVER['DOCUMENT_ROOT'].$tmp_path.$name);
		$path=SITE_PATH.$tmp_path.$name;
		
		$zip=new AcidZip();
		$zip->init($path);
		
		if (!empty($filter)) {
			$zip->filter=$filter;
		}
		
		if ($filter_mode) {
			$zip->filter_mode=$filter_mode;
		}
			
			
		if ($zip->addDir($folder,substr($name, 0, strpos($name, '.')))) {
			//$zip->readZip(false);
			$this->saveZip($path);
		 }
		
		 unlink($path);
	
		 exit();
	}
	
	/**
	 * Retourne les logs de debug
	 * @return string
	 */
	public function debug() {
		$res='';
		
		foreach ($this->logs as $k=>$l) {
			$res.=$k.' ) '.$l.'<br />';
		}
		
		return $res;
	}
}

?>