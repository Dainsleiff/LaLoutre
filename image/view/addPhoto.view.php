<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="fr" >
	<head>
		<title>Site SIL3</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" media="screen" title="Normal" />
		<script src="jquery-3.2.1.min.js"></script>
		</head>
	<body>
		<div id="entete" >
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
      <!-- Le type d'encodage des données, enctype, DOIT être spécifié comme ce qui suit -->
      <form enctype="multipart/form-data" action="index.php" method="post">
        <!-- MAX_FILE_SIZE doit précéder le champ input de type file -->
        <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
        <!-- Le nom de l'élément input détermine le nom dans le tableau $_FILES -->
        Envoyez ce fichier : <input id="uploadBox" name="userfile" type="file"  value=""/>
				<input type="reset" id="reset" name="Reset" value="Reset">
				<legend>Commentaire :</legend>
        <input  type="text" name="commentaire" value="" required>
				<legend>Categorie :</legend>
        <input type="text" name="categorie" value="" required>
				<legend>Url internet (si pas de fichier local) :</legend>
        <input type="text" id="abcd" name="url" value="" required>
				<input type="hidden" name="controller" value="photo">
				<input type="hidden" name="action" value="addImg">
				<input type="submit" name="submit" value="Envoyer le fichier" />
      </form>

			</div>

		<div id="pied_de_page">
		</div>
		<script type="text/javascript">
		document.getElementById('uploadBox').addEventListener('change', function(e) {
			document.getElementById('abcd').disabled = !! e.target.files.length;
		});
		document.getElementById('reset').addEventListener('click', function(e){
			console.log(e);
			document.getElementById('abcd').disabled = false;
		});
		</script>
	</body>
</html>
