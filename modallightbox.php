<?php
	defined('_JEXEC') or die('Access deny');
	
	include('plugins/content/modallightbox/imageMetadataParser.php');//A inclure AVANT la classe
	
	class plgContentModallightbox extends JPlugin 
	{
		function onContentPrepare($content, $article, $params, $limit){				
			/************************************************************
			 * MODE D'EMPLOI
			 * 
			 * 1. dans l'appel du plugin dans l'article, il faut spécifier 
			 *    le chemin relatif du dossier d'image à parcourir ainsi : 
			 *           {GMLB}/images/banners{/GMLB}
			 * 2. dans le dossier spécifié ci-dessus entre {GMLB} et {/GMLB}
			 * seuls deux types de fichiers doivent être présent
			 *   2.1 : les fichiers "images" aux formats : JPG,PNG,GIF (Exemple : apple.jpg)
			 *   2.2 : les fichiers de légendes dont les nom de fichiers reprennent EXACTEMENT le nom de l'image 
		     *         auquel concaténer l'extension .txt (Ex : apple.jpg.txt)
			 * Le plugin saura alors faire la distinction. Bien entendu, les fichiers de légense peuvent contenir du HTML ainsi que du  css INLINE.
			 * Il est possible de créer DANS LE REPERTOIRE DU PLUGIN, un fichier qui DOIT porter le nom custom_style.css, dans lequel on peut rajouter
			 * ses propres styles css personnaliés.		 			 
			 *************************************************************/
			$document = JFactory::getDocument();
			$document->addStyleSheet('plugins/content/modallightbox/style.css');
			if (file_exists('plugins/content/modallightbox/custom_style.css'))
			{
				$document->addStyleSheet('plugins/content/modallightbox/custom_style.css');
			}
			$re = '/{GMLB}(.*){\/GMLb}/mi';

			preg_match_all($re, $article->text, $matches, PREG_SET_ORDER, 0);



			//GMLB = Gallerie Modale Light Box
			$article->text = str_replace('{GMLB}(.*){/GMLB}', $article->title, $article->text);
			$i=0;
			foreach($matches as $elt)
			{
				
				$files = scandir("./".$elt[1]);
				
				
				foreach($files  as $unfichier)
				{
					{
						
						$e = explode('.',$unfichier);
						//Je recherche la légende dans un fichier texte qui porte le nom de l'image mais avec l'extension txt, dans le même dossier !
							
						if (($unfichier<>'.') and ($unfichier<>'..') and ($e[1]<>'txt'))
						{
							
							$z = explode('.',JURI::base().substr($elt[1],1).'/'.$unfichier);
							//print_r($z);
							if ($z[2]<>'txt')
							{
								$f = file_get_contents(JURI::base().substr($elt[1],1).'/'.$unfichier.'.txt');
								
								echo '<a href="#modal'.$i.'" id="monfic'.$i.'"><img class="image-modale" src="'.JURI::base().substr($elt[1],1).'/'.$unfichier.'"> ';
								echo '<div id="modal'.$i.'" class="modale_LB">
										  <div class="modal_LB_window">
											  <a class="modal_LB_close" href="#monfic'.$i.'"></a>
											  <p><img src="'.JURI::base().substr($elt[1],1).'/'.$unfichier.'"></p> 
											  <div class="legende-image">'.$f.'</div>
										  </div>
										</div>';
								$legende='';
							}
					
						}
					}
					$i++;
					$article->text = str_replace($elt[0],'', $article->text);
					
				}
				unset($files);
				
				
			}
			
		}	
	}