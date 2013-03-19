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
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */




/**
 * Outil AcidTable, Gestionnaire de tableaux
 * @package   Tool
 */
class AcidTable
{
    
	/**
	 * @var array les lignes
	 */
    protected $vals          = array();
    
    /**
     * @var array les entêtes
     */
    protected $headers       = array('lines'=>array(),'cols'=>array());

    /**
     * @var string tlc
     */
    protected $tlc           = '';
    
    /**
     * @var array paramêtres d'affichages des entêtes
     */
    protected $print_headers = array('lines'=>false,'cols'=>false);
    
    /**
     * @var array attributs
     */
    protected $attrs         = array(
                                       'table' => array(),
                                       'lines' => array(),
                                       'cols'  => array()
                                    );
    
    /**
     * @var string couleur impaire
     */
    protected $even_color    = null;
    
    /**
     * @var string couleur paire
     */
    protected $odd_color     = null;
    
    /**
     * @var string classe impaire
     */
    protected $even_class    = null;
    
    /**
     * @var string classe paire
     */
    protected $odd_class     = null;
    
    
    const NO_CONTENT         = '&nbsp;';
    
    
    
    /**
     * Constructeur AcidTable
     * @param array $table_attrs Listes des attributs du tableau.
     */
    public function __construct ($table_attrs=array()) {
        $this->setTableAttr($table_attrs);
    }
    
    /**
     * Retourne les éléments du tableau en entrée sous forme d'une chaîne de caractères (Formaté comme Attributs HTML)
     *
     * @param array $params ([paramètres]=>[valeurs])
     * 
     * @return string 
     */
    public static function getParams($params) {
		$str = '';
		foreach ($params as $key=>$val) $str .= ' ' . $key . '="' . $val . '"';
		return $str;
	}
    
    /**
     * Définit la couleur des lignes paires et retourne true en cas de réussite, false sinon.
     *
     * @param string $color
     * 
     * @return bool
     */
    public function setEvenColor($color) {
        if (preg_match('`^#[0-9a-fA-F]{6}$`',$color)) {
            $this->even_color = $color;
            return true;
        }
        return false;
    }
    
    /**
     * Définit la couleur des lignes impaires et retourne true en cas de réussite, false sinon.
     *
     * @param string $color
     * 
     * @return bool
     */
    public function setOddColor($color) {
        if (preg_match('`^#[0-9a-fA-F]{6}$`',$color)) {
            $this->$odd_color = $color;
            return true;
        }
        return false;
    }
    
    /**
     * Définit la classe HTML des lignes paires et retourne true en cas de réussite, false sinon.
     *
     * @param string $class_name
     * 
     * @return bool
     */
    public function setEvenClass($class_name) {
    	$this->even_class = $class_name;
    }
    
    /**
     * Définit la classe HTML des lignes impaires et retourne true en cas de réussite, false sinon.
     *
     * @param string $class_name
     * 
     * @return bool
     */
    public function setOddClass($class_name) {
    	$this->odd_class = $class_name;
    }
    
    
    
    /**
     *Assigne les paramètres $params au $type identifié par $id et retourne true en cas de succès, false sinon.
     *
     * @param string $type Cible. (table, lines, cols)
     * @param int $id 
     * @param array $params Liste des paramètres.
     * 
     * @return bool
     */
    public function setAttrs($type,$id,$params) {
        if (isset($this->attrs[$type])) {
            if (is_array($params)) {
                foreach ($params as $key=>$val) {
                    $this->attrs[$type][(int)$id][$key] = (string)$val;
                }
                return true;
            }
        }
        return false;
    }
    
    /**
     * Assigne les attributs en entrée au tableau.
     * 
     * @param array $params ([paramètres]=>[valeurs])
     */
    public function setTableAttr($params) {
    	foreach ($params as $key=>$val) {
    		$this->attrs['table'][$key] = (string)$val;
    	}
    }
    
    
    
    /**
     * Ajoute une valeur au tableau
     * Si la cellule est déjà prise, écrase le contenu.
     * 
     * @param int $line Ligne.
     * @param int $col Colonne.
     * @param string $val Valeur.
     * @param array $params Paramètres.
     */
    public function addVal($line, $col, $val, $params=array()) {
        $this->vals[(int)$line][(int)$col] = array((string)$val,(array)$params);
    }
    
    /**
     * Définit l'entête du tableau, et retourne true en cas de réussite, false sinon.
     *
     * @param string $type (lines | cols)
     * @param int $id 
     * @param string $text
     * @param array $params
     * 
     * @return bool
     */
    public function setHeader($type, $id, $text, $params=array()) {
        if (isset($this->headers[$type])) {
            $this->headers[$type][(int)$id] = (string)$text;
            $this->setAttrs($type, $id, $params);
            return true;
        } else {
            trigger_error(	'Acid : $type attribute of AcidTable::setHeader '.
            				'should be "lines" or "cols", "'.$type.'" given',E_USER_WARNING);
            return false;
        }
    }
    
    /**
     * Définit la visibilité de l'entête définie par $type, et retourne true en cas de succès.
     *
     * @param string $type (lines | cols)
     * @param bool $display 
     * 
     * @return bool
     */
    public function diplayHeaders ($type,$display) {
        if (isset($this->print_headers[$type])) {
            $this->print_headers[$type] = (bool)$display;
            return true;
        } else {
            trigger_error(	'Acid : $type attribute of AcidTable::displayHeaders '.
            				'should be "lines" or "cols", "'.$type.'" given',E_USER_WARNING);
            return false;
        }
    }
    
    /**
     * Définit le contenu du coin Haut/Gauche
     * 
     * @param string $text
     */
    public function setTopLeftCorner ($text) {
        $this->tlc = (string)$text;
    }
    
    
    /**
     * Retourne l'entête du tableau qui est renseignée par les paramètres en entrée.
     *
     * @param string $type
     * @param int $id
     * 
     * @return string
     */
    protected function getHeader($type,$id) {
        $class = '';
        if ($type == 'cols') {
        	$class = ' class="col"';
        }elseif ($type == 'lines') {
        	$class = ' class="line"';
        }
    	return '		<th'.$class.(isset($this->attrs[$type][$id]) ? self::getParams($this->attrs[$type][$id]) : '').'>' . 
               (empty($this->headers[$type][$id]) ? self::NO_CONTENT : $this->headers[$type][$id]).
               '</th>';
    }
    
    
    /**
     * Retourne le nombre effectif de colonnes dans le tableau 
     *
     * @return int
     */
    protected function getMaxCols() {
        $max = 0;
        foreach ($this->vals as $cols) {
            $count = count($cols);
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }
    
    
    /**
     * Generation of table
     * 
     * @return HTLM code : <table> ... </table>
     */
    public function html() {
        $headers = $content = '';
        
        $max_cols = $this->getMaxCols();
        
        if ($this->print_headers['lines'] && $this->print_headers['cols']) {
            $headers .= '		<th class="tlc">' . 
                            (empty($this->tlc) ? self::NO_CONTENT : $this->tlc) . 
                        '</th>';
        }
        $col_params = array();
        for ($col=1;$col<=$max_cols;$col++) {
            $col_params[$col] = isset($this->attrs['cols'][$col]) ? $this->attrs['cols'][$col] : array();
            if ($this->print_headers['cols']) {
                $headers .= $this->getHeader('cols',$col);
            }
        }
        if ($this->print_headers['cols']) {
	        $content .= '	<tr>' . "\n" . 
	                            $headers . 
	                    '	</tr>' . "\n";
        }
        
        $line = 1;
        foreach ($this->vals as $line=>$line_content) {
            
        	$line_params = isset($this->attrs['lines'][(string)$line]) ? $this->attrs['lines'][$line] : array();
            
            if ($line%2) {
                if ($this->odd_color) {
                    $line_params = array_merge($line_params,array('style'=>'background-color:'.$this->odd_color));
                }
                if ($this->odd_class) {
                	$line_params = array_merge($line_params,array('class'=>$this->odd_class));
                }
            } else {
                if ($this->even_color) {
                	$line_params = array_merge($line_params,array('style'=>'background-color:'.$this->even_color));
                }
                if ($this->even_class) {
                	$line_params = array_merge($line_params,array('class'=>$this->even_class));
                }
            }
            
            $content .= '	<tr'.self::getParams($line_params).'>' . "\n";
            
            if ($this->print_headers['lines']) {
                $content .= $this->getHeader('lines',$line);
            }
            
            $col = 1;
            foreach ($line_content as $cols) {
                $content .= '		<td'.self::getParams(array_merge($col_params[$col],$cols[1])).'>' . 
                                $cols[0] . 
                            '</td>' . "\n";
                $col ++;
            }
            $content .= '	</tr>' . "\n";
            $line ++;
        }
        
        
        return	'<table '.self::getParams($this->attrs['table']).'>' . "\n" . 
                    $content . 
                '</table>' . "\n";
    }
}



?>
