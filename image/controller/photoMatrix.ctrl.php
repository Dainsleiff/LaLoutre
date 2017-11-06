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

      if (isset($_GET['categorieSearch'])) {
        $this->data['imgCategorie'] = $_GET['categorieSearch'];
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

      //on récupère la catégorie séléctionné si elle existe et on charge la categorieSearch dans l'objet imgDAO
      if(isset($_GET['categorieSearch'])){
        $_SESSION['categorieSearch'] = $_GET['categorieSearch'];
        $this->data['imgDAO']->setCategorieSearch($_SESSION['categorieSearch']);
      }
      else{
        if(!isset($_SESSION['categorieSearch'])){
          $_SESSION['categorieSearch'] = '';
        }
      }

      //on récup le nombre d'image à afficher
      if (isset($_GET["nbImg"])) {
        $this->data['nbImg'] = $_GET["nbImg"];
      } else {
        # sinon débute avec 2 images
        $this->data['nbImg'] = 2;
      }
      $this->data['nbImgPlus'] = $this->data['nbImg'] *2;
      $this->data['nbImgMoins'] =$this->data['nbImg'] /2;
      # Calcul la liste des images à afficher (attention images + imageDAO)
      $this->data['imgListe']= $this->data['imgDAO']->getImageList($img,$this->data['nbImg']);
      $temp_images = array();
      foreach ($this->data['imgListe'] as $imageOrimageDAO) {
        if(is_a($imageOrimageDAO, 'Image')) {
          $temp_images[] = $imageOrimageDAO;
        }
      }
      $this->data['imgListe'] = $temp_images;
      #initialise une taille d'image par défaut lorsque l'on choisit d'afficher une seule image en cliquant sur une image
      $size=480;
//_______________________________________________________________________________________
      # Transforme cette liste en liste de couples (tableau a deux valeurs)
      # contenant l'URL de l'image et l'URL de l'action sur cette image
      $this->data['imgMatrixURL'] = array();
      foreach ($this->data['imgListe'] as $i) {
        # l'identifiant de cette image $i
        $iId=$i->getId();
        # Ajoute à imgMatrixURL
        #  0 : l'URL de l'image
        #  1 : l'URL de l'action lorsqu'on clique sur l'image : la visualiser seul
        $this->data['imgMatrixURL'][] = array($i->getURL(),"index.php?controller=photo&action=viewPhoto&imgId=".$iId."&size=".$size);
      }

      # Adapte la taille des images au nombre d'images présentes
      $this->data['size'] = 480 / sqrt(count($this->data['imgMatrixURL']));


//------------------------------------------------------------------------------------------------------------

     $this->data['imgNext'] = $this->data['imgDAO']->jumpToImage($img,$this->data['nbImg'],true,false);
     $this->data['imgIdNext'] = $this->data['imgNext']->getId();
     $this->data['imgUrlNext'] = $this->data['imgNext']->getURL();
     $this->data['imgPrev'] = $this->data['imgDAO']->jumpToImage($img,$this->data['nbImg'],false,true);
     $this->data['imgIdPrev'] = $this->data['imgPrev']->getId();
     $this->data['imgUrlPrev'] = $this->data['imgPrev']->getURL();
    }



    public function launchAction(){
      switch ($this->action) {
        case 'aPropos':
        include_once 'view/aPropos.view.php';
        self::initTableau();
        break;

        case 'prev':
         //pas besoin de beaucoup de traitement car les images précédentes sont initialisées dans le constructeur.
         //on initialise le tableau après avoir mis à jour les données
         self::initTableau();
          include_once "view/photoMatrix.view.php";
          break;

       case 'next':
         //pas besoin de beaucoup de traitement car les images suivantes sont initialisées dans le constructeur.
         self::initTableau();
         include_once "view/photoMatrix.view.php";
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

        case 'more':
              //on augmente le nombre d'images affichées
              self::initTableau();
              include_once "view/photoMatrix.view.php";
        break;

        case 'less':
              //on diminue le nombre d'images affichées
              self::initTableau();
              include_once "view/photoMatrix.view.php";
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
      if (isset($_GET['categorieSearch'])) {
      $this->data['menu']['First']='index.php?controller=photo&action=first&imgId='.$this->data['imgId']."&size=".$this->data['size'].'&categorieSearch='.$this->data['imgCategorie'];
     } else {
      $this->data['menu']['First']='index.php?controller=photo&action=first&imgId='.$this->data['imgId']."&size=".$this->data['size'];
     }
      if (isset($_GET['categorieSearch'])) {
        $this->data['menu']['More']="index.php?controller=photoMatrix&action=more&imgId=".$this->data['imgId']."&size=".$this->data['size']."&nbImg=".$this->data['nbImgPlus']."&categorieSearch=".$this->data['imgCategorie'];
        $this->data['menu']['Less']="index.php?controller=photoMatrix&action=less&imgId=".$this->data['imgId']."&size=".$this->data['size']."&nbImg=".$this->data['nbImgMoins']."&categorieSearch=".$this->data['imgCategorie'];
      } else {
        $this->data['menu']['More']="index.php?controller=photoMatrix&action=more&imgId=".$this->data['imgId']."&size=".$this->data['size']."&nbImg=".$this->data['nbImgPlus'];
        $this->data['menu']['Less']="index.php?controller=photoMatrix&action=less&imgId=".$this->data['imgId']."&size=".$this->data['size']."&nbImg=".$this->data['nbImgMoins'];
      }
    }
}
