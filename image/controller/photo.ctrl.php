
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
       $this->data['imgCommentaire'] = $img->getCommentaire();
     } else {
       // Pas d'image, se positionne sur la première
       $img = $this->data['imgDAO']->getFirstImage();
       // Conserve son id pour définir l'état de l'interface
       $this->data['imgId'] = $img->getId();
       $this->data['imgUrl'] = $img->getURL();
       $this->data['imgCommentaire'] = $img->getCommentaire();
     }

     // Regarde si une taille pour l'image est connue
     if (isset($_GET["size"]) && $_GET["size"]!='') {
       $this->data['size'] = $_GET["size"];
     } else {
       # sinon place une valeur de taille par défaut
       $this->data['size'] = 480;
     }
    //on initialise les images adjacentes (next/prev)
    $this->data['imgNext'] = $this->data['imgDAO']->getNextImage($img->getId());
    $this->data['imgIdNext'] = $this->data['imgNext']->getId();
    $this->data['imgUrlNext'] = $this->data['imgNext']->getURL();
    $this->data['imgPrev'] = $this->data['imgDAO']->getPrevImage($img->getId());
    $this->data['imgIdPrev'] = $this->data['imgPrev']->getId();
    $this->data['imgUrlPrev'] = $this->data['imgNext']->getURL();

    //on initialise le commentaire et la catégorie
    $this->data['imgCommentaire'] = $img->getCommentaire();
    $this->data['imgCategorie'] = $img->getCategorie();
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

   }




   public function launchAction(){
     switch ($this->action) {

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
       $this->data['imgCommentaire'] = $firstImg->getCommentaire();
       $this->data['imgCategorie'] = $firstImg->getCategorie();
       self::initTableau();
       include_once "view/viewPhoto.view.php";
       break;

       case 'catPhoto':
       $img = $this->data['imgDAO']->getImage();
       $this->data['imgId'] = $img->getId();
       $this->data['imgUrl'] = $img->getURL();
       $this->data['imgCommentaire'] = $img->getCommentaire();
       $this->data['imgCategorie'] = $img->getCategorie();
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

      case 'nextOfCat':
        $img = $this->data['imgDAO']->getNextImage($this->data['imgId']);
        $this->data['imgId'] = $img->getId();
        $this->data['imgUrl'] = $img->getURL();
        $this->data['imgCommentaire'] = $img->getCommentaire();
        $this->data['imgCategorie'] = $img->getCategorie();
        self::initTableau();
        include_once "view/viewPhoto.view.php";
        break;

      case 'prevOfCat':
        $img = $this->data['imgDAO']->getPrevImage($this->data['imgId']);
        $this->data['imgId'] = $img->getId();
        $this->data['imgUrl'] = $img->getURL();
        $this->data['imgCommentaire'] = $img->getCommentaire();
        $this->data['imgCategorie'] = $img->getCategorie();
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
            $this->data['imgCommentaire'] = $img->getCommentaire();
            $this->data['imgCategorie'] = $img->getCategorie();
            //on initialisele tableau après avoir mis à jour les données
            self::initTableau();
            include_once "view/viewPhoto.view.php";
          break;

        case 'changeData':
            //on change le commentaire et/ou la catégorie
            //on appele la methode du DAO pour changer la categorie
            var_dump($_GET);
            $this->data['imgCommentaire'] = $_GET['commentaire'];
            $this->data['imgCategorie'] = $_GET['categorie'];
            $this->data['imgDAO']->changeCategory($_GET['categorie'],$this->data['imgId']);
            $this->data['imgDAO']->changeComment($_GET['commentaire'],$this->data['imgId']);
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
     if (isset($_GET['categorieSearch'])) {
      $this->data['menu']['First']='index.php?controller=photo&action=first&imgId='.$this->data['imgId']."&size=".$this->data['size'].'&categorieSearch='.$this->data['imgCategorie'];
     } else {
      $this->data['menu']['First']='index.php?controller=photo&action=first&imgId='.$this->data['imgId']."&size=".$this->data['size'];
     }
     $this->data['menu']['Random']="index.php?controller=photo&action=random&imgId=".$this->data['imgId']."&size=".$this->data['size'];
     if (isset($_GET['categorieSearch'])) {
      $this->data['menu']['More']="index.php?controller=photoMatrix&action=more&imgId=".$this->data['imgId']."&size=".$this->data['size']."&categorieSearch=".$this->data['imgCategorie'];
     } else {
      $this->data['menu']['More']="index.php?controller=photoMatrix&action=more&imgId=".$this->data['imgId']."&size=".$this->data['size'];
     }
     $this->data['menu']['Zoom +']="index.php?controller=photo&action=zoomPlus&imgId=".$this->data['imgId']."&size=".$this->data['size'];
     $this->data['menu']['Zoom -']="index.php?controller=photo&action=zoomMoins&imgId=".$this->data['imgId']."&size=".$this->data['size'];
   }
 }
 ?>
