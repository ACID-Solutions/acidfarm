<div id="menu">
	<div id="menu_head"></div>
	<ul id="menu_body">
		<?php 
		$p = $v['page'];
		$s = 'selected';
		$uc = ' unclickable';
		$cat = $v['siteadmin_cat'];

		foreach ($cat as $value) {
		    
			if(!empty($v['controller'][$value])){
		        $my_key = ($value=='default' ? '':$value);
		        
                $display = isset($v['controller'][$value]['display']) ? $v['controller'][$value]['display'] : true;
				$level = isset($v['controller'][$value]['level']) ? $v['controller'][$value]['level'] : $v['def_level'];
				$display = $display && User::curLevel($level);
				$cat_separator = !empty($v['controller'][$value]['separator']);
				if ($display) {
	                if (empty($v['controller'][$value]['url'])) {
	                    $url = $_SERVER['PHP_SELF']. ($my_key ? '?page='.$my_key  : '');
	                }else{
	                    $url = $v['controller'][$value]['url'];
	                }
	                
	                $sP= ($my_key == $p) ? ' '.$s : '';
	                if(!empty($v['controller'][$p]['parent'])&&$v['controller'][$p]['parent']===$value){
	                    $sP = ' '.$s;
	                }
	                
	                $ucP='';
	                $ucP=empty($v['controller'][$value]['unclickable']) ? '' : $uc;
	                
	                $margin = !empty($v['controller'][$value]['margin']) ? 'style="margin-bottom:'.$v['controller'][$value]['margin'].'px;"' : '';
			        $a_url = (!empty($v['controller'][$value]['unclickable']))?$v['controller'][$value]['label']:'  <a href="'.$url.'">'.$v['controller'][$value]['label'].'</a>';
	                echo 	'<li class="headmenu'.$sP.$ucP.'" '.$margin.' >'. "\n" .
	                        $a_url;
	                echo    '</li>' . "\n" ;
				}
				
		    }
		    
		    if ($display) { 
		    	
		    	$submenu = '';
			                  
				foreach ($v['controller'] as $key => $line) {
					$my_key = ($key=='default' ? '':$key);
	                $display = isset($line['display']) ? $line['display'] : true;
	                if ($display) {
	                    if (User::curLevel($line['level']) ) {
	                        $label = $line['label'];
	                        $margin = !empty($line['margin']) ? 'style="margin-bottom:'.$line['margin'].'px;"' : '';
	                                
	                        if (empty($line['url'])) {
	                            $url = $_SERVER['PHP_SELF']. ($my_key ? '?page='.$my_key  : '');
	                        }else{
	                            $url = $line['url'];
	                        }
	                        if(!empty($line['parent'])&&$line['parent']===$value){
	                            $tcolor = (!empty($line['color']))?'style ="color:'.$line['color'].'"':((!empty($v['controller'][$line['parent']]['color']))?'style ="color:'.$v['controller'][$line['parent']]['color'].'"':'');
	                            $color = (!empty($line['color']))?'style ="background-color:'.$line['color'].';"':((!empty($v['controller'][$line['parent']]['color']))?'style ="background-color:'.$v['controller'][$line['parent']]['color'].'"':'');
	                            $submenu .=	'<li '.($p==$my_key? ('class="'.$s.'"'):'').' '.$margin.' '.$tcolor.'>'. "\n" .
	                                	 	'  <span class="puce" '.$color.'></span>'.
	                                		'  <a href="'.$url.'">'.$label.'</a>'.
	                               		  	'</li>' . "\n" ;
	                            if (!empty($line['separator'])) {
	                                $submenu .= '<li class="separator"></li>';
	                            }
	                        }
	                    }
	                }
				}
				
				if ($submenu) {
					echo '<li class="separator light"></li>';
					echo '<li class="submenu">';
				    echo '<ul class="child_menu" >';
					echo $submenu;
		            echo '</ul>';
	            	echo '</li>';
				}
				            
	            if ($cat_separator) {
	            	echo '<li class="separator"></li>';
	            }
	                
		    }
		}
		?>
		<li class="separator"></li>
		<li class="headmenu">
			<a href="<?php echo Acid::get('url:folder'); ?>">
			<?php echo Acid::trad('admin_menu_back'); ?>
			</a>
		</li>
		<li class="separator"></li>
		<li class="headmenu">
			<a href="#" onclick="document.getElementById('unlog_form').submit();return false;">
			<?php echo Acid::trad('admin_menu_unlog'); ?></a>
			</li> 
	</ul>
	
	<form method="post" action="" id="unlog_form"><div><input type="hidden" name="do" value="logout" /></div></form>
</div>