<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="fr" >
	<head>
		<title>Site SIL3</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="style.css" media="screen" title="Normal" />
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
			<?php # mise en place de la vue partielle : le contenu central de la page
				# Mise en place des deux boutons
				print "<p>\n";
				//action pour image précédente
				if (isset($_GET['categorieSearch'])) {
					print "<a href=\"index.php?controller=photo&action=prevOfCat&imgId=".$this->data['imgId']."&size=".$this->data['size']."&categorieSearch=".$this->data['imgCategorie']."\">Prev</a> ";
				} else {
					print "<a href=\"index.php?controller=photo&action=prev&imgId=".$this->data['imgIdPrev']."&size=".$this->data['size']."\">Prev</a> ";
				}
				//action pour image suivante
				if (isset($_GET['categorieSearch'])) {
					print "<a href=\"index.php?controller=photo&action=nextOfCat&imgId=".$this->data['imgId']."&size=".$this->data['size']."&categorieSearch=".$this->data['imgCategorie']."\">Next</a>\n";
				} else {
					print "<a href=\"index.php?controller=photo&action=next&imgId=".$this->data['imgIdNext']."&size=".$this->data['size']."\">Next</a>\n";
				}
			?>

				<!-- Champs de recherche des catégories -->
				<form action="index.php" method="GET">
	 			Recherche par catégorie:<br>
	 			<input type="text" name="categorieSearch" list="categories" value="<?php echo $_SESSION['categorieSearch'];?>"><br>
				<datalist id="categories">
					<?php foreach ($this->data['imgDAO']->getAllCategories() as $categorie) {

						print "<option value=\"".$categorie->category."\">";
					};
					?>
				</datalist>
				<input type="hidden" name="controller" value="photo">
				<input type="hidden" name="action" value="catPhoto">
				<input type="submit" name='submit' value="Validation">
 				</fieldset>
				</form>
				<form class="" action="index.php" method="get">
					<input type="hidden" name="controller" value="photo">
					<input type="hidden" name="action" value="viewPhoto">
					<input type="submit" name="reset" value="Annuler la recherche">
				</form>

				<?php
				print "</p>\n";
				# Zoom l'image avec une reaction au click
				print "<a href=\"index.php?controller=photo&action=zoomPlus&imgId=".$this->data['imgId']."&size=".$this->data['size']."\">\n";

				// Réalise l'affichage de l'image
				print "<img src=\"".$this->data['imgUrl']."\" width=\"".$this->data['size']."\">\n";
				print $this->data['imgUrl'];
				print "</a>\n";
				?>

				<!-- Affichage et modification du commentaire -->
				<form class="no-border no-background" action="index.php" method="get">
					<legend>Comment :</legend>
					<input type="text" name="commentaire" value="<?php echo $this->data['imgCommentaire'];?>">
					<legend>Category :</legend>
					<input type="text" name="categorie" value="<?php echo $this->data['imgCategorie'];?>">
					<input type='hidden' name='imgId' value="<?php echo $this->data['imgId'];?>">
					<input type="hidden" name="controller" value="photo">
					<input type="hidden" name="action" value="changeData">
					<input type="submit" name='submit' value='Changer les infos'>
				</form>




			</div>

		<div id="pied_de_page">
		</div>
	</body>
</html>
