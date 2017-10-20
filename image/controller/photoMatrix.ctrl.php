<?php
$imgDAO = new ImageDAO();
include_once 'view/photoMatrix.view.php';

 ?>

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
 					# Utilisation du modèle
 					require_once("model/image.php");
 					require_once("model/imageDAO.php");
 					// Débute l'acces aux images
 					$imgDAO = new ImageDAO();

 					// Construit l'image courante
 					// et l'ID courant
 					if (isset($_GET["imgId"])) {
 						$imgId = $_GET["imgId"];
 						$img = $imgDAO->getImage($imgId);
 					} else {
 						// Pas d'image, se positionne sur la première
 						$img = $imgDAO->getFirstImage();
 						// Conserve son id pour définir l'état de l'interface
 						$imgId = $img->getId();
 					}

 					// Récupère le nombre d'images à afficher
 					if (isset($_GET["nbImg"])) {
 						$nbImg = $_GET["nbImg"];
 					} else {
 						# sinon débute avec 2 images
 						$nbImg = 2;
 					}
 					// Regarde si une taille pour l'image est connue
 					if (isset($_GET["size"])) {
 						$size = $_GET["size"];
 					} else {
 						# sinon place une valeur de taille par défaut
 						$size = 480;
 					}

 					# Calcul la liste des images à afficher
 					$imgLst= $imgDAO->getImageList($img,$nbImg);

 					# Transforme cette liste en liste de couples (tableau a deux valeurs)
 					# contenant l'URL de l'image et l'URL de l'action sur cette image
 					foreach ($imgLst as $i) {
 						# l'identifiant de cette image $i
 						$iId=$i->getId();
 						# Ajoute à imgMatrixURL
 						#  0 : l'URL de l'image
 						#  1 : l'URL de l'action lorsqu'on clique sur l'image : la visualiser seul
 						$imgMatrixURL[] = array($i->getURL(),"viewPhoto.php?imgId=$iId");
 					}

 					# Mise en place du menu
 					$menu['Home']="index.php";
 					$menu['A propos']="aPropos.php";
 					// Pre-calcule la première image
 					$newImg = $imgDAO->getFirstImage();
 					# Change l'etat pour indiquer que cette image est la nouvelle
 					$newImgId=$newImg->getId();
 					$menu['First']="viewPhotoMatrix.php?imgId=$newImgId&nbImg=$nbImg";
 					# Pre-calcule une image au hasard
 					$menu['Random']="nonRealise.php";
 					# Pré-calcule le nouveau nombre d'images à afficher si on en veux plus
 					$newNbImg = $nbImg * 2;
 					$menu['More']="viewPhotoMatrix.php?imgId=$imgId&nbImg=$newNbImg";
 					# Pré-calcule le nouveau nombre d'images à afficher si on en veux moins
 					$newNbImg = $nbImg / 2;
 					$menu['Less']="viewPhotoMatrix.php?imgId=$imgId&nbImg=$newNbImg";
 					// Affichage du menu
 					foreach ($menu as $item => $act) {
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
 				if (isset($_GET["nbImg"])) {
 					$newNbImg = $_GET["nbImg"];
 				}

 				$imgAvancer = $imgDAO->jumpToImage($img,$newNbImg,null,true);
 				$imgIdAvancer = $imgAvancer->getId();

 				print "<a href=\"viewPhotoMatrix.php?imgId=$imgIdAvancer&nbImg=$newNbImg\">Prev</a> "; //ICIIIIII
 				// pre-calcul de la page d'images suivante

 				$imgReculer = $imgDAO->jumpToImage($img,$newNbImg,true,null);
 				$imgIdReculer = $imgReculer->getId();

 				print "<a href=\"viewPhotoMatrix.php?imgId=$imgIdReculer&nbImg=$newNbImg\">Next</a>\n"; //ICIIIIII
 				print "</p>\n";
 				# Affiche de la matrice d'image avec une reaction au click
 				print "<a href=\"zoom.php?zoom=1.25&imgId=$newImgId&size=$size\">\n";
 				// Réalise l'affichage de l'image
 				# Adapte la taille des images au nombre d'images présentes
 				$size = 480 / sqrt(count($imgMatrixURL));
 				# Affiche les images
 				foreach ($imgMatrixURL as $i) {
 					print "<a href=\"".$i[1]."\"><img src=\"".$i[0]."\" width=\"".$size."\" height=\"".$size."\"></a>\n";
 				};
 				?>
 			</div>

 		<div id="pied_de_page">
 			</div>
 		</body>
 	</html>


  <?php
  require_once("model/imageDAO.php");
  require_once('model/image.php');

  class photoMatrix{
    private $action = null;
    private $data = null;

    function __construct() {
      $this->data['imgDAO'] = new ImageDAO();
      //récupération
      if (isset($_GET['action']) && $_GET['action'] !='') {
        $this->action = $_GET['action'];
      }
      else {
        $this->action = 'default';
      }
      if (isset($_GET["imgId"]) && $_GET['imgId'] !='') {
        $this->data['imgId'] = $_GET["imgId"];
        $img = $this->data['imgDAO']->getImage($this->data['imgId']);
        $this->data['imgUrl'] = $img->getURL();
      } else {
        // Pas d'image, se positionne sur la première
        $img = $this->data['imgDAO']->getFirstImage();
        // Conserve son id pour définir l'état de l'interface
        $this->data['imgId'] = $img->getId();
        $this->data['imgUrl'] = $img->getURL();
      }

      // Regarde si une taille pour l'image est connue
      if (isset($_GET["size"]) && $_GET["size"]!='') {
        $this->data['size'] = $_GET["size"];
      } else {
        # sinon place une valeur de taille par défaut
        $this->data['size'] = 480;
      }
      if (isset($_GET["nbImg"])) {
        $this->data['nbImg'] = $_GET["nbImg"];
      } else {
        # sinon débute avec 2 images
        $this->data['nbImg'] = 2;
      }
      # Calcul la liste des images à afficher
      $imgLst= $imgDAO->getImageList($img,$this->data['nbImg']);

//_______________________________________________________________________________________
      # Transforme cette liste en liste de couples (tableau a deux valeurs)
      # contenant l'URL de l'image et l'URL de l'action sur cette image
      foreach ($imgLst as $i) {
        # l'identifiant de cette image $i
        $iId=$i->getId();
        # Ajoute à imgMatrixURL
        #  0 : l'URL de l'image
        #  1 : l'URL de l'action lorsqu'on clique sur l'image : la visualiser seul
        $imgMatrixURL[] = array($i->getURL(),"viewPhoto.php?imgId=$iId");
      }

//_______________________________________________________________________________________




     //on initialise les images adjacentes (next/prev)
     $this->data['imgNext'] = $this->data['imgDAO']->getNextImage($img);
     $this->data['imgIdNext'] = $this->data['imgNext']->getId();
     $this->data['imgUrlNext'] = $this->data['imgNext']->getURL();
     $this->data['imgPrev'] = $this->data['imgDAO']->getPrevImage($img);
     $this->data['imgIdPrev'] = $this->data['imgPrev']->getId();
     $this->data['imgUrlPrev'] = $this->data['imgNext']->getURL();
    }
