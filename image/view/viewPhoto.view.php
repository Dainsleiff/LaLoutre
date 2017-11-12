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
			<div class="ruban">
				<h1>Site SIL3</h1>
			</div>
			<div class="ruban gauche"></div>
			<div class="ruban droit"></div>
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
			<div class="rechercheCat">
			<!-- Champs de recherche des catégories -->
				<form id="searchthis" action="index.php" method="GET">
		 			<input id="search" type="text" name="categorieSearch" list="categories" value="<?php
		 				if(isset($this->data['categorieSearch'])){
		 					echo $this->data['categorieSearch'];
		 				} ?>" placeholder="Rechercher par catégorie"><br>
					<datalist id="categories">
						<?php foreach ($this->data['ListCategories'] as $categorie) {
							print "<option value=\"".$categorie->category."\">";
						}; ?>
					</datalist>
					<input type="hidden" name="controller" value="photo">
					<input type="hidden" name="action" value="catPhoto">
					<input type="hidden" name="imgId" value="<?php echo $this->data['imgId']; ?>">
					<input class="search-btn" type="submit" name='submit' value="Lancer la recherche">
				</form>
				<form action="index.php" method="get">
					<input type="hidden" name="controller" value="photo">
					<input type="hidden" name="action" value="viewPhoto">
					<input class="search-btn" type="submit" name="reset" value="Annuler la recherche">
				</form>
			</div>
			<?php # mise en place de la vue partielle : le contenu central de la page
				# Mise en place des deux boutons
				print "<p>\n";
				//action pour image précédente
				if (isset($this->data['categorieSearch']) && $this->data['categorieSearch']!='') {
					print "<a href=\"index.php?controller=photo&action=prevOfCat&imgId=".$this->data['imgId']."&size=".$this->data['size']."&categorieSearch=".$this->data['imgCategorie']."\">Prev</a> ";
				} elseif (isset($this->data['popularity']) && $this->data['popularity']!='') {
					print "<a href=\"index.php?controller=photo&action=prevByPopularity&imgId=".$this->data['imgId']."&size=".$this->data['size']."\">Prev</a>\n";
				} else {
					print "<a href=\"index.php?controller=photo&action=prev&imgId=".$this->data['imgId']."&size=".$this->data['size']."\">Prev</a> ";
				}
				//action pour image suivante
				if (isset($this->data['categorieSearch']) && $this->data['categorieSearch']!='') {
					print "<a href=\"index.php?controller=photo&action=nextOfCat&imgId=".$this->data['imgId']."&size=".$this->data['size']."&categorieSearch=".$this->data['imgCategorie']."\">Next</a>\n";
				} elseif (isset($this->data['popularity']) && $this->data['popularity']!='') {
					print "<a href=\"index.php?controller=photo&action=nextByPopularity&imgId=".$this->data['imgId']."&size=".$this->data['size']."\">Next</a>\n";
				} else {
					print "<a href=\"index.php?controller=photo&action=next&imgId=".$this->data['imgId']."&size=".$this->data['size']."\">Next</a>\n";
				}

			?>


				<?php
				print "</p>\n";
				# Zoom l'image avec une reaction au click
				print "<a href=\"index.php?controller=photo&action=zoomPlus&imgId=".$this->data['imgId']."&size=".$this->data['size']."\">\n";

				// Réalise l'affichage de l'image
				print "<img src=\"".$this->data['imgUrl']."\" width=\"".$this->data['size']."\">\n";
				print "</a>\n";
				?>

				<form class="vote" action="index.php" method="get">
					<legend>Aimez vous cette photo ?</legend>
					<input type="radio" name="votes" id="voteOui" value="1">
					<label for="voteOui">Oui</label>
					<input type="radio" name="votes" id="voteNon" value="0">
					<label for="voteNon">Non</label>
					<input type="hidden" name="controller" value="photo">
					<input type="hidden" name="action" value="vote">
					<input type="hidden" name="nbvote" value="<?php echo $this->data['imgNbVotes'] ?>">
					<input type='hidden' name='imgId' value="<?php echo $this->data['imgId'];?>">
					<input class="search-btn" type="submit" name="submit" value="Envoyer le vote">
				</form>
				<table class="vote">
					<tr>
						<th>Nombre de votes</th>
						<th>Note</th>
					</tr>
					<tr>
						<td><?php echo $this->data['imgNbVotes'] ?></td>
						<td><?php echo $this->data['imgVotes'] ?></td>
					</tr>
				</table>

				</form>

				<!-- Affichage et modification du commentaire -->
				<form id="changer" class="no-border no-background" action="index.php" method="get">
					<legend>Comment :</legend>
					<input type="text" name="commentaire" value="<?php echo $this->data['imgCommentaire'];?>">
					<legend>Category :</legend>
					<input type="text" name="categorie" value="<?php echo $this->data['imgCategorie'];?>">
					<input type='hidden' name='imgId' value="<?php echo $this->data['imgId'];?>">
					<input type="hidden" name="controller" value="photo">
					<input type="hidden" name="action" value="changeData">
					<input class="search-btn" type="submit" name='submit' value='Changer les informations de la photo'>
				</form>
			</div>

		<div id="pied_de_page">
		</div>
	</body>
</html>
