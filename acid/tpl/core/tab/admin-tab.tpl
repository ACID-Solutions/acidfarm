	<table class="admin_tab">
	
		<tr class="head_row">
			<?php 
			//on créer les colonnes
			foreach ($v['cols'] as $kh => $th) {
				
				if ( is_array($th) ) {
					$th_content = ($th['url']!==false) ? '<a href="'.$th['url'].'">'.$th['name'].'</a>' : $th['name'];
				}else{
					$th_content =  $th;
				}
				
				$th_attr = isset($th['attr']) ? $th['attr'] : array();
				$th_attr['class'] = isset($th_attr['class']) ? $th_attr['class'].' head_'.$kh : 'head_'.$kh;
				echo $o->getAdminTh($th_content,$th_attr,$v['config']) . "\n" ;
			}
			?>
		</tr>
		
		<?php 
			$num = 0;
			//si on a des lignes
			if (is_array($v['rows'])) {
				//pour chaque ligne
				foreach ($v['rows'] as $k=> $line) {
					
					$class= ((($num%2)==0)? 'odd_line':'even_line');
					$class .= ' line_'.$k;
					if (isset($v['config']['assoc_rows_id'][$k])) {
						$class .= ' list_'.$o->checkTbl().'_'.$v['config']['assoc_rows_id'][$k];
					}
					
					//on l'affiche
					echo $o->getAdminTr($line,$class,array(),$v['config']) ;
					$num++;
				}
				
			}
			
			//si on a du texte
			else{
				echo  $rows? $rows:'';
			}
		?>
	
	
	</table>

				
				