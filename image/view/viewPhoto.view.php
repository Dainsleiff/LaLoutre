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
				//mise en place du formulaire caché pour passer les données
				print '<form id="test" action="le_lien_vers_ta_page" method="post">';
				# Mise en place des deux boutons
				print "<p>\n";
				//action pour image précédente
				print "<a href=\"index.php?controller=photo&action=prev&imgId=".$this->data['imgIdPrev']."&size=".$this->data['size']."\">Prev</a> ";
				//action pour image suivante
				print "<a href=\"index.php?controller=photo&action=next&imgId=".$this->data['imgIdNext']."&size=".$this->data['size']."\">Next</a>\n";
				print "</p>\n";
				# Zoom l'image avec une reaction au click
				print "<a href=\"index.php?controller=photo&action=zoomPlus&imgId=".$this->data['imgId']."&size=".$this->data['size']."\">\n";

				// Réalise l'affichage de l'image
				print "<img src=\"".$this->data['imgUrl']."\" width=\"".$this->data['size']."\">\n";
				print "</a>\n";
				?>
			</div>

		<div id="pied_de_page">
		</div>
	</body>
</html>
