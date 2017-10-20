
 <?php
 require_once("model/imageDAO.php");
 require_once('model/image.php');

 class photo{
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
    //on initialise les images adjacentes (next/prev)
    $this->data['imgNext'] = $this->data['imgDAO']->getNextImage($img);
    $this->data['imgIdNext'] = $this->data['imgNext']->getId();
    $this->data['imgUrlNext'] = $this->data['imgNext']->getURL();
    $this->data['imgPrev'] = $this->data['imgDAO']->getPrevImage($img);
    $this->data['imgIdPrev'] = $this->data['imgPrev']->getId();
    $this->data['imgUrlPrev'] = $this->data['imgNext']->getURL();
   }




   public function launchAction(){
     switch ($this->action) {
       case 'aPropos':
       include_once 'view/aPropos.view.php';
       self::initTableau();
       break;

       case 'viewPhoto':
       $firstImg = $this->data['imgDAO']->getFirstImage();
       $this->data['ImgIdFirst'] = $firstImg->getId();
       self::initTableau();
       include_once "view/viewPhoto.view.php";
       break;

       case 'first':
       $firstImg = $this->data['imgDAO']->getFirstImage();
       $this->data['imgId'] = $firstImg->getId();
       $this->data['imgUrl'] = $firstImg->getURL();
       self::initTableau();
       include_once "view/viewPhoto.view.php";
       break;


       case 'prev':
        //pas besoin de beaucoup de traitement car l'image précédente est initialisée dans le constructeur.
        //on initialisele tableau après avoir mis à jour les données
        self::initTableau();
         include_once "view/viewPhoto.view.php";
         break;

      case 'next':
        //pas besoin de beaucoup de traitement car l'image suivante est initialisée dans le constructeur.
        self::initTableau();
        include_once "view/viewPhoto.view.php";
        break;

      case 'zoomPlus':
        //on augmente la taille
        $this->data['size'] = $this->data['size'] * 1.25;
        //on initialisele tableau après avoir mis à jour les données
        self::initTableau();
        include_once "view/viewPhoto.view.php";
        break;

      case 'zoomMoins':
        //on diminue la taille
        $this->data['size'] = $this->data['size'] * 0.75;
        //on initialisele tableau après avoir mis à jour les données
        self::initTableau();
        include_once "view/viewPhoto.view.php";
        break;

        case 'random':
            //on sélectionne une image random
            $img = $this->data['imgDAO']->getRandomImage();
            $this->data['imgId'] = $img->getId();
            $this->data['imgUrl'] = $img->getURL();
            //on initialisele tableau après avoir mis à jour les données
            self::initTableau();
            include_once "view/viewPhoto.view.php";
          break;

       default:
       include_once 'view/home.view.php';
       break;
     }
   }

   private function initTableau(){
     //action du menu
     $this->data['menu']['Home']='index.php';
     $this->data['menu']['aPropos']='index.php?controller=home&action=aPropos';
     $this->data['menu']['First']='index.php?controller=photo&action=first&imgId='.$this->data['imgId']."&size=".$this->data['size'];
     $this->data['menu']['Random']="index.php?controller=photo&action=random&imgId=".$this->data['imgId']."&size=".$this->data['size'];
     $this->data['menu']['More']="index.php?controller=photoMatrix&action=more";
     $this->data['menu']['Zoom +']="index.php?controller=photo&action=zoomPlus&imgId=".$this->data['imgId']."&size=".$this->data['size'];
     $this->data['menu']['Zoom -']="index.php?controller=photo&action=zoomMoins&imgId=".$this->data['imgId']."&size=".$this->data['size'];
   }
 }
 ?>
