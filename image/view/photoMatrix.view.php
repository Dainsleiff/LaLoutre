<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="fr" >
	<head>
		<title>Site SIL3</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" media="screen" title="Normal" />
		</head>
	<body>
		<div id="entete">
			<h1>Site SIL3</h1>
			</div>
		<div id="menu">
			<h3>Menu</h3>
			<ul>
				<?php
					//Affichage des items du menu
					foreach ($this->data['menu'] as $item => $act) {
						print "<li><a href=\"$act\">$item</a></li>\n";
					}
					?>
				</ul>
			</div>

		<div id="corps">
			<?php # mise en place de la vue partielle : le contenu central de la page
				# Mise en place des deux boutons
				print "<p>\n";
				// pre-calcul de la page d'images précedente

				print "<a href=\"index.php?controller=photoMatrix&action=prev&imgId=".$this->data['imgIdPrev']."&nbImg=".$this->data['nbImg']."\">Prev</a> "; //ICIIIIII
				// pre-calcul de la page d'images suivante
				print "<a href=\"index.php?controller=photoMatrix&action=next&imgId=".$this->data['imgIdNext']."&nbImg=".$this->data['nbImg']."\">Next</a> "; //ICIIIIII
				print "</p>\n";
				# Affiche de la matrice d'image avec une reaction au click
				print "<a href=\"index.php?controller=photoMatrix&action=zoomPlus&imgId=".$this->data['imgId']."\">\n";
				// Réalise l'affichage de l'image
				# Affiche les images
				foreach ($this->data['imgMatrixURL'] as $i) {
					print "<a href=\"".$i[1]."\"><img src=\"".$i[0]."\" width=\"".$this->data['size']."\" height=\"".$this->data['size']."\"></a>\n";
				};
				?>
			</div>

		<div id="pied_de_page">
			</div>
		</body>
	</html>
