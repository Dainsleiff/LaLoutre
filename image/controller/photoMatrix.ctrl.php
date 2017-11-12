  <?php
  require_once("model/imageDAO.php");
  require_once('model/image.php');

  class photoMatrix{
    private $action = null;
    private $data = null;



    function initDataNbImg(){
      //on récup le nombre d'image à afficher
      if (isset($_GET["nbImg"])) {
        $this->data['nbImg'] = $_GET["nbImg"];
      } else {
        # sinon débute avec 2 images
        $this->data['nbImg'] = 2;
      }
    }

    function initDataRandom(){
      if (isset($_GET['random']) && $_GET['random'] != '') {
        $this->data['imgRandom'] = $_GET['random'];
      } else {
        $this->data['imgRandom'] = 0;
      }
    }

    function initDataCatSize(){
      if (isset($_GET['categorieSearch']) && $_GET['categorieSearch'] != '') {
        $this->data['imgCategorie'] = $_GET['categorieSearch'];
        $this->data['catSize'] = $this->data['imgDAO']->getCatSize($this->data['imgCategorie']);
      } else {
        $this->data['catSize'] = $this->data['imgDAO']->getSizeAll();
      }
    }


    //récupération d'info de l'image courante
    function initDataStart(){
      if (isset($_GET["imgId"]) && $_GET['imgId'] !='') {
        $this->data['imgId'] = $_GET["imgId"];
        $this->data['img'] = $this->data['imgDAO']->getImage($this->data['imgId']);
        $this->data['imgUrl'] = $this->data['img']->getURL();
        $this->data['ListCategories']= $this->data['imgDAO']->getAllCategories();
      } else {
        // Pas d'image, se positionne sur la première
        $this->data['img'] = $this->data['imgDAO']->getFirstImage();
        // Conserve son id pour définir l'état de l'interface
        $this->data['imgId'] = $this->data['img']->getId();
        $this->data['imgUrl'] = $this->data['img']->getURL();
        $this->data['ListCategories']= $this->data['imgDAO']->getAllCategories();
      }
    }

    //récupération d'infos de la catégorie voulu
    function initDataCategorieSearch(){
      //on récupère la catégorie séléctionné si elle existe et on charge la categorieSearch dans l'objet imgDAO
      if(isset($_GET['categorieSearch'])){
        $_SESSION['categorieSearch'] = $_GET['categorieSearch'];
        $this->data['imgDAO']->setCategorieSearch($_SESSION['categorieSearch']);
        $this->data['categorieSearch'] = $_SESSION['categorieSearch'];
      }
      else{
        if(!isset($_SESSION['categorieSearch'])){
          $_SESSION['categorieSearch'] = '';
          $this->data['categorieSearch'] = '';
        }
        else {
          $this->data['categorieSearch'] = $_SESSION['categorieSearch'];
        }
      }
    }

    //mise a jour du nbImage quand on clique sur "More"
    function initDataNbImgPlus(){
      if ($this->data['nbImg'] < $this->data['catSize']) {
        $this->data['nbImg'] = $this->data['nbImg'] *2;
      } else {
        $this->data['nbImg'] = $this->data['nbImg'];
      }
    }

    //mise a jour du nbImage quand on clique sur "Less"
    function initDataNbImgMoins(){
      if ($this->data['nbImg'] > 1) {
        $this->data['nbImg'] = $this->data['nbImg'] /2;
      } else {
        $this->data['nbImg'] = 1;
      }
    }

    //initialisation des données de la liste d'image
    function initDataImgListe(){
      # Calcul la liste des images à afficher (attention images + imageDAO)
      if ($this->data['imgRandom']) {
        $this->data['imgListe']= $this->data['imgDAO']->getRandomMatrix($this->data['nbImg']);
      } else {
        $this->data['imgListe']= $this->data['imgDAO']->getImageList($this->data['img'],$this->data['nbImg']);
      }
      # Transforme cette liste en liste de couples (tableau a deux valeurs)
      # contenant l'URL de l'image et l'URL de l'action sur cette image
      $this->data['imgMatrixURL'] = array();
      foreach ($this->data['imgListe'] as $i) {
        # l'identifiant de cette image $i
        $iId=$i->getId();
        # Ajoute à imgMatrixURL
        #  0 : l'URL de l'image
        #  1 : l'URL de l'action lorsqu'on clique sur l'image : la visualiser seul
        $this->data['imgMatrixURL'][] = array($i->getURL(),"index.php?controller=photo&action=viewPhoto&imgId=".$iId."&size=".$this->data['size']);
      }

      # Adapte la taille des images au nombre d'images présentes
      $this->data['size'] = 480 / sqrt(count($this->data['imgMatrixURL']));
    }

    //maj de l'image courante quand on clique sur suivant
    function initDataNext(){
      $this->data['img'] = $this->data['imgDAO']->jumpToImage($this->data['img'],$this->data['nbImg'],true,false);
      $this->data['imgId'] = $this->data['img']->getId();
      $this->data['imgUrl'] = $this->data['img']->getURL();
    }

    //maj de l'image courante quand on clique sur précédant
    function initDataPrev(){
       $this->data['img'] = $this->data['imgDAO']->jumpToImage($this->data['img'],$this->data['nbImg'],false,true);
       $this->data['imgId'] = $this->data['img']->getId();
       $this->data['imgUrl'] = $this->data['img']->getURL();
    }

    function __construct() {
      $this->data['imgDAO'] = new ImageDAO();
      //récupération
      if (isset($_GET['action']) && $_GET['action'] !='') {
        $this->action = $_GET['action'];
      }
      else {
        $this->action = 'default';
      }
      #initialise une taille d'image par défaut lorsque l'on choisit d'afficher une seule image en cliquant sur une image
      $this->data["size"]=480;
    }



    public function launchAction(){
      switch ($this->action) {

        case 'prev':
        //on initialise les données courantes
        self::initDataNbImg();
        self::initDataStart();
        self::initDataCatSize();
        self::initDataCategorieSearch();
        //on met a jour l'image courante qui servira a séléctionner la liste d'images
        self::initDataPrev();
        //on sélectionne la liste d'images correspondante
        self::initDataRandom();
        self::initDataImgListe();
         //on initialise le tableau après avoir mis à jour les données
         self::initTableau();
          include_once "view/photoMatrix.view.php";
          break;

       case 'next':
       //on initialise les données courantes
       self::initDataNbImg();
       self::initDataStart();
       self::initDataCatSize();
       self::initDataCategorieSearch();
       //on met a jour l'image courante qui servira a séléctionner la liste d'images
       self::initDataNext();
       //on sélectionne la liste d'images correspondante
       self::initDataRandom();
       self::initDataImgListe();
        //on initialise le tableau après avoir mis à jour les données
        self::initTableau();
         include_once "view/photoMatrix.view.php";
         break;

        case 'random':
            //on initialise les données courantes
            self::initDataNbImg();
            self::initDataStart();
            self::initDataCatSize();
            self::initDataCategorieSearch();
             //on sélectionne n images random
             self::initDataRandom();
             self::initDataImgListe();
             //on initialisele tableau après avoir mis à jour les données
             self::initTableau();
             include_once "view/photoMatrix.view.php";
        break;

        case 'more':
              //on initialise les données courantes
              self::initDataNbImg();
              self::initDataStart();
              self::initDataCatSize();
              self::initDataCategorieSearch();
              //on augmente le nombre d'images affichées
              self::initDataNbImgPlus();
              //on récupère la liste d'images correspondantes
              self::initDataRandom();
              self::initDataImgListe();
              //on initilise le tableau
              self::initTableau();
              include_once "view/photoMatrix.view.php";
        break;

        case 'less':
              //on initialise les données courantes
              self::initDataNbImg();
              self::initDataStart();
              self::initDataCatSize();
              self::initDataCategorieSearch();
              //on diminue le nombre d'images affichées
              self::initDataNbImgMoins();
              //on récupère la liste d'images correspondantes
              self::initDataRandom();
              self::initDataImgListe();
              //on initilise le tableau
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
      $this->data['menu']['Home']='index.php?reset=reset';
      $this->data['menu']['aPropos']='index.php?controller=home&action=aPropos';
      if (isset($_GET['categorieSearch'])) {
      $this->data['menu']['First']='index.php?controller=photo&action=first&imgId='.$this->data['imgId']."&size=".$this->data['size'].'&categorieSearch='.$this->data['imgCategorie'];
     } else {
      $this->data['menu']['First']='index.php?controller=photo&action=first&imgId='.$this->data['imgId']."&size=".$this->data['size'];
     }
     if (isset($_GET['categorieSearch'])) {
      $this->data['menu']['Random']="index.php?controller=photoMatrix&action=random&random=1&imgId=".$this->data['imgId']."&size=".$this->data['size']."&nbImg=".$this->data['nbImg']."&categorieSearch=".$this->data['imgCategorie'];
     } else {
      $this->data['menu']['Random']="index.php?controller=photoMatrix&action=random&random=1&imgId=".$this->data['imgId']."&size=".$this->data['size']."&nbImg=".$this->data['nbImg'];
     }
      if (isset($_GET['categorieSearch'])) {
        $this->data['menu']['More']="index.php?controller=photoMatrix&action=more&imgId=".$this->data['imgId']."&size=".$this->data['size']."&nbImg=".$this->data['nbImg']."&categorieSearch=".$this->data['imgCategorie'];
        $this->data['menu']['Less']="index.php?controller=photoMatrix&action=less&imgId=".$this->data['imgId']."&size=".$this->data['size']."&nbImg=".$this->data['nbImg']."&categorieSearch=".$this->data['imgCategorie'];
      } else {
        $this->data['menu']['More']="index.php?controller=photoMatrix&action=more&imgId=".$this->data['imgId']."&size=".$this->data['size']."&nbImg=".$this->data['nbImg'];
        $this->data['menu']['Less']="index.php?controller=photoMatrix&action=less&imgId=".$this->data['imgId']."&size=".$this->data['size']."&nbImg=".$this->data['nbImg'];
      }
      $this->data['menu']['Ajouter une image'] = 'index.php?controller=photo&action=addImg';
    }
}
