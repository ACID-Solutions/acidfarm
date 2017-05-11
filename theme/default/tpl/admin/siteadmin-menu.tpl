<label id="menuburger" for="menustate"><hr /><hr /><hr /></label>
<label id="menuenlarge" for="bodystate">&lt;=&gt;</label>

<div id="menu">
	<div id="menu_head"></div>
	<ul id="menu_body">
		<?php

		$p = Lib::getIn('page',$v);
		$s = 'selected';
		$uc = ' unclickable';
		$cat = Lib::getIn('siteadmin_cat',$v,array());
		$controller =  Lib::getIn('controller',$v,array());

		if ($cat)  {
			foreach ($cat as $catkey => $catconfig) {

			        $my_key = ($catkey=='default' ? '':$catkey);

	                $display = Lib::getIn('display',$catconfig,true);
					$level = Lib::getIn('level',$catconfig,$v['def_level']);
					$display = $display && User::curLevel($level);
					$cat_separator = Lib::getIn('separator',$catconfig,true);

					if ($display) {

						$urldef = $_SERVER['PHP_SELF']. ($my_key ? '?page='.$my_key  : '');
						$url = Lib::getIn('url',$catconfig,$urldef);

		                $sP= ($my_key == $p) ? ' '.$s : '';
		                if ( (!empty($catconfig['elts'])) && (in_array($p,$catconfig['elts'])) ) {
		                    $sP = ' '.$s;
		                }

		                $ucP='';
		                $ucP=empty($catconfig['unclickable']) ? '' : $uc;

		                $margin = !empty($catconfig['margin']) ? 'style="margin-bottom:'.$catconfig['margin'].'px;"' : '';
				        $a_url = (!empty($catconfig['unclickable']))?$catconfig['label']:'  <a href="'.$url.'">'.$catconfig['label'].'</a>';

		                echo 	'<li class="headmenu'.$sP.$ucP.'" '.$margin.' >'. "\n" .
		                        $a_url;
		                echo    '</li>' . "\n" ;

					}



			    if ( (!empty($display)) && (!empty($catconfig['elts'])) ) {

			    	$submenu = '';

					foreach ($catconfig['elts'] as $key) {
						$line = Lib::getIn($key,$controller,array());

						$my_key = ($key=='default' ? '':$key);
		                $display = Lib::getIn('display',$line,true);
		                if ($display) {
		                    if (User::curLevel($line['level']) ) {
		                        $label = Lib::getIn('label',$line,$key);
		                        $margin = !empty($line['margin']) ? 'style="margin-bottom:'.$line['margin'].'px;"' : '';

		                        $urldef = $_SERVER['PHP_SELF']. ($my_key ? '?page='.$my_key  : '');
		                        $url = Lib::getIn('url',$line,$urldef);

		                        $color_line = Lib::getIn('color',$line, Lib::getIn('color',$catconfig,false));

		                        $tcolor = $color_line ? ('style ="color:'.$color_line.';"') : '';
		                        $color = $color_line ? ('style ="background-color:'.$color_line.';"') : '';

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

					if ($submenu) {
						echo '<li class="separator light"></li>';
						echo '<li class="submenu">';
					    echo '<ul class="child_menu" data-category="'.htmlspecialchars($catconfig['label']).'">';
						echo $submenu;
			            echo '</ul>';
		            	echo '</li>';
					}
			    }

			    if ($cat_separator && $display) {
			    	echo '<li class="separator"></li>';
			    }

			}
		}
		?>

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