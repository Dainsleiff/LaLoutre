
 <?php
 require_once("model/imageDAO.php");
 require_once('model/image.php');

 class photo{
   private $action = null;
   private $data = null;


   function initDataSize(){
     // Regarde si une taille pour l'image est connue
     if (isset($_GET["size"]) && $_GET["size"]!='') {
       $this->data['size'] = $_GET["size"];
     } else {
       # sinon place une valeur de taille par défaut
       $this->data['size'] = 480;
     }
   }

   function initDataCategorieCommentaireVote(){
     //on initialise le commentaire et la catégorie + le nombre de vote et la note
     $this->data['imgCommentaire'] = $this->data['img']->getCommentaire();
     $this->data['imgCategorie'] = $this->data['img']->getCategorie();
     $this->data['imgNbVotes'] = $this->data['img']->getNbVotes();
     $this->data['imgVotes'] = $this->data['img']->getVotes();
   }

   function initDataStart(){
     if (isset($_GET["imgId"]) && $_GET['imgId'] !='') {
       $this->data['imgId'] = $_GET["imgId"];
       $this->data['img'] = $this->data['imgDAO']->getImage($this->data['imgId']);
       $this->data['imgUrl'] = $this->data['img']->getURL();
       $this->data['imgCommentaire'] = $this->data['img']->getCommentaire();
       $this->data['ListCategories'] = $this->data['imgDAO']->getAllCategories();
     } else {
       // Pas d'image, se positionne sur la première
       $this->data['img'] = $this->data['imgDAO']->getFirstImage();
       // Conserve son id pour définir l'état de l'interface
       $this->data['imgId'] = $this->data['img']->getId();
       $this->data['imgUrl'] = $this->data['img']->getURL();
       $this->data['imgCommentaire'] = $this->data['img']->getCommentaire();
     }

     //on initialise le commentaire et la catégorie + le nombre de vote et la note
     self::initDataCategorieCommentaireVote();
   }

   function initDataNextImg(){
     //on initialise les images adjacentes (next/prev)
     $this->data['previousImgId'] = $_GET['imgId'];
     $img = $this->data['imgDAO']->getImage($this->data['previousImgId']);
     $this->data['img'] = $this->data['imgDAO']->getNextImage($img->getId());
     $this->data['imgId'] = $this->data['img']->getId();
     $this->data['imgUrl'] = $this->data['img']->getURL();
   }

   function initDataPrevImg(){
         //on initialise les images adjacentes (next/prev)
         $this->data['previousImgId'] = $_GET['imgId'];
         $img = $this->data['imgDAO']->getImage($this->data['previousImgId']);
         $this->data['img'] = $this->data['imgDAO']->getPrevImage($img->getId());
         $this->data['imgId'] = $this->data['img']->getId();
         $this->data['imgUrl'] = $this->data['img']->getURL();
   }

   function initDataCategorieSearch(){
     //on récupère la catégorie séléctionné si elle existe et on charge la categorieSearch dans l'objet imgDAO
     if(isset($_GET['categorieSearch'])){
       $_SESSION['categorieSearch'] = $_GET['categorieSearch'];
       $this->data['imgDAO']->setCategorieSearch($_SESSION['categorieSearch']);
       $this->data['categorieSearch'] = $_GET['categorieSearch'];
     }
     else{
       //si la variable $_SESSION['categorieSearch'] (au lancement de l'application ou après un reset)
       if(!isset($_SESSION['categorieSearch'])){
         $_SESSION['categorieSearch'] = '';
         $this->data['categorieSearch'] = $_SESSION['categorieSearch'];
       }
       //si il y a une recherche déja en cours (et que la variable dans $_SESSION existe, on la charge dans this->data)
       if(isset($_SESSION['categorieSearch'])){
         $this->data['categorieSearch'] = $_SESSION['categorieSearch'];
       }
     }
     if(isset($_REQUEST['submit'])){
       $this->data['submit'] = $_REQUEST['submit'];
     }
   }

   function __construct() {
     $this->data['imgDAO'] = new ImageDAO();
     //récupération
     if (isset($_REQUEST['action']) && $_REQUEST['action'] !='') {
       $this->action = $_REQUEST['action'];
     }
     else {
       $this->action = 'default';
     }
     $this->data['ListCategories'] = $this->data['imgDAO']->getAllCategories();
   }




   public function launchAction(){
     switch ($this->action) {

       case 'viewPhoto':
        self::initDataStart();
        self::initDataSize();
        self::initDataCategorieSearch();
       $firstImg = $this->data['imgDAO']->getFirstImage();
       $this->data['ImgIdFirst'] = $firstImg->getId();
       self::initTableau();
       include_once "view/viewPhoto.view.php";
       break;

       case 'first':
       //on récupère l'état courant
       self::initDataSize();
       self::initDataCategorieSearch();
       $this->data['img'] = $this->data['imgDAO']->getFirstImage();
       $this->data['imgId'] = $this->data['img']->getId();
       $this->data['imgUrl'] = $this->data['img']->getURL();
       $this->data['imgCommentaire'] = $this->data['img']->getCommentaire();
       $this->data['imgCategorie'] = $this->data['img']->getCategorie();
       self::initDataCategorieCommentaireVote();
       self::initTableau();
       include_once "view/viewPhoto.view.php";
       break;

       case 'catPhoto':
       self::initDataSize();
       self::initDataCategorieSearch();
       $this->data['img'] = $this->data['imgDAO']->getImage();
       $this->data['imgId'] = $this->data['img']->getId();
       $this->data['imgUrl'] = $this->data['img']->getURL();
       $this->data['imgCommentaire'] = $this->data['img']->getCommentaire();
       $this->data['imgCategorie'] = $this->data['img']->getCategorie();
       self::initDataCategorieCommentaireVote();
       self::initTableau();
       include_once "view/viewPhoto.view.php";
       break;

       case 'prev':
        //on initialise le changement des données
        self::initDataPrevImg();
        self::initDataSize();
        self::initDataCategorieCommentaireVote();
        //on initialisele tableau après avoir mis à jour les données
        self::initTableau();
         include_once "view/viewPhoto.view.php";
         break;

      case 'next':
      //on initialise le changement des données (img,imgid,imgurl pour l'image suivante)
        self::initDataNextImg();
        self::initDataSize();
        self::initDataCategorieCommentaireVote();
        //on initialise le menu après avoir mis à jour les données
        self::initTableau();
        include_once "view/viewPhoto.view.php";
        break;

      case 'nextOfCat':
        self::initDataStart();
        self::initDataCategorieSearch();
        self::initDataSize();
        $this->data['img'] = $this->data['imgDAO']->getNextImage($this->data['imgId']);
        $this->data['imgId'] = $this->data['img']->getId();
        $this->data['imgUrl'] = $this->data['img']->getURL();
        $this->data['imgCommentaire'] = $this->data['img']->getCommentaire();
        $this->data['imgCategorie'] = $this->data['img']->getCategorie();
        self::initTableau();
        include_once "view/viewPhoto.view.php";
        break;

      case 'prevOfCat':
        self::initDataStart();
        self::initDataCategorieSearch();
        self::initDataSize();
        $img = $this->data['imgDAO']->getPrevImage($this->data['imgId']);
        $this->data['imgId'] = $img->getId();
        $this->data['imgUrl'] = $img->getURL();
        $this->data['imgCommentaire'] = $img->getCommentaire();
        $this->data['imgCategorie'] = $img->getCategorie();
        self::initTableau();
        include_once "view/viewPhoto.view.php";
        break;

      case 'zoomPlus':
        self::initDataStart();
        self::initDataSize();
        $this->data['size'] = $this->data['size'] * 1.25;
        self::initDataCategorieCommentaireVote();
        //on initialisele tableau après avoir mis à jour les données
        self::initTableau();
        include_once "view/viewPhoto.view.php";
        break;

      case 'zoomMoins':
        //on récupère l'état courant de l'image
        self::initDataStart();
        self::initDataSize();
        //on change la taille
        $this->data['size'] = $this->data['size'] * 0.75;
        self::initDataCategorieCommentaireVote();
        //on initialise le tableau après avoir mis à jour les données
        self::initTableau();
        include_once "view/viewPhoto.view.php";
        break;

      case 'random':
          //on récupère l'état courant de l'image
          self::initDataSize();
          self::initDataCategorieSearch();
          //on sélectionne une image random
          $this->data['img'] = $this->data['imgDAO']->getRandomImage();
          $this->data['imgId'] = $this->data['img']->getId();
          $this->data['imgUrl'] = $this->data['img']->getURL();
          $this->data['imgCommentaire'] = $this->data['img']->getCommentaire();
          $this->data['imgCategorie'] = $this->data['img']->getCategorie();

          self::initDataCategorieCommentaireVote();
          //on initialisele tableau après avoir mis à jour les données
          self::initTableau();
          include_once "view/viewPhoto.view.php";
        break;

        case 'changeData':
            //on change le commentaire et/ou la catégorie
            self::initDataStart();
            self::initDataSize();
            //on appele la methode du DAO pour changer la categorie
            $this->data['imgCommentaire'] = $_GET['commentaire'];
            $this->data['imgCategorie'] = $_GET['categorie'];
            $res = $this->data['imgDAO']->changeCategory($_GET['categorie'],$this->data['imgId']);
            $this->data['imgDAO']->changeComment($_GET['commentaire'],$this->data['imgId']);
            self::initTableau();
            include_once "view/viewPhoto.view.php";
        break;

        case 'addImg' :
        //fonction d'ajout d'image
        self::initDataStart();
        self::initDataSize();
        if(isset($_REQUEST['submit'])){
          $this->data['submit'] = $_REQUEST['submit'];
        }
        self::initTableau();
        unset($this->data['menu']['Zoom -']);
        unset($this->data['menu']['Zoom +']);
        unset($this->data['menu']['Ajouter une image']);
        unset($this->data['menu']['More']);

          //dans le cas ou l'on sort du formulaire d'ajout
          if(isset($this->data['submit'])){
            //on récup les données commentaire/catégorie.
            if(isset($_POST['commentaire'])){
              $this->data['commentaire'] = $_POST['commentaire'];
            }
            else{
              print 'Le commentaire du formulaire d\'ajout d\'image n\'est pas récupéré';
            }
            if(isset($_POST['categorie'])){
              $this->data['categorie'] = $_POST['categorie'];
            }
            else{
              print 'La categorie du formulaire d\'ajout d\'image n\'est pas récupérée';
            }

            //si on upload une image via url
            if(isset($_POST['url'])){
              $this->data['urlImgBDD'] = $_POST['url'];
              $res = $this->data['imgDAO']->addImg($this->data['urlImgBDD'],$this->data['categorie'],$this->data['commentaire'],'false');
              if($res){
                $this->data['resultAdd'] = "Le fichier est valide, et a été téléchargé
                avec succès.\n";
              }
              else{
                  $this->data['resultAdd'] = "Le fichier n'a pas été upload en base.\n";
              }


            }

            //si on upload via le local
            else{
              $this->data['uploadDir'] = '/var/www/html/sites/php/LaLoutre/image/model/IMG/jons/uploads/';
              $this->data['uploadFile'] = $this->data['uploadDir'] . basename($_FILES['userfile']['name']);
              $this->data['uploadDirForBDD'] = 'jons/uploads/'.$_FILES['userfile']['name'];
              if (move_uploaded_file($_FILES['userfile']['tmp_name'], $this->data['uploadFile'])) {
                $res = $this->data['imgDAO']->addImg($this->data['uploadDirForBDD'],$this->data['categorie'],$this->data['commentaire'],'true');
                if($res){
                  $this->data['resultAdd'] = "Le fichier est valide, et a été téléchargé
                  avec succès.\n";
                }
                else{
                  $this->data['resultAdd'] = "Le fichier est valide, a été upload sur le server mais pas en base.\n";
                }
              } else {
                  $this->data['resultAdd'] = "Attaque potentielle par téléchargement de fichiers.Upload bloquée.
                        Voici plus d'informations :\n";
                        print_r($_FILES);
              }
            }
            include_once 'view/validationAddFile.view.php';
          }
          //dans le cas ou on veut accéder au formulaire d'ajout
          else{
            include_once 'view/addPhoto.view.php';
          }
        break;

        case 'vote':
          //on récupère l'état courant de l'image pour la réafficher tel qu'elle était
          self::initDataStart();
          self::initDataSize();
          self::initDataCategorieCommentaireVote();
          //on met à jour le nombre de vote et la note obtenue par la photo
          if (isset($_GET['nbvote'])) {
            $this->data['imgNbVotes'] = $_GET['nbvote']+1;
          }
          //si vote positif
          if (isset($_GET['votes']) && $_GET['votes']==1) {
            $this->data['imgVotes'] = $this->data['imgVotes']+1;
          }
          //si vote negatif
          elseif (isset($_GET['votes']) && $_GET['votes']==0) {
            $this->data['imgVotes'] = $this->data['imgVotes']-1;
          }
          var_dump($this->data['imgId']);
          $this->data['imgDAO']->addVote($this->data['imgId'], $this->data['imgNbVotes'], $this->data['imgVotes']);
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
     if (isset($_GET['categorieSearch'])) {
      $this->data['menu']['Random']="index.php?controller=photo&action=random&imgId=".$this->data['imgId']."&size=".$this->data['size']."&categorieSearch=".$this->data['imgCategorie'];
     } else {
      $this->data['menu']['Random']="index.php?controller=photo&action=random&imgId=".$this->data['imgId']."&size=".$this->data['size'];
     }
     if (isset($_GET['categorieSearch'])) {
      $this->data['menu']['More']="index.php?controller=photoMatrix&action=more&imgId=".$this->data['imgId']."&size=".$this->data['size']."&categorieSearch=".$this->data['imgCategorie'];
     } else {
      $this->data['menu']['More']="index.php?controller=photoMatrix&action=more&imgId=".$this->data['imgId']."&size=".$this->data['size'];
     }
     $this->data['menu']['Zoom +']="index.php?controller=photo&action=zoomPlus&imgId=".$this->data['imgId']."&size=".$this->data['size'];
     $this->data['menu']['Zoom -']="index.php?controller=photo&action=zoomMoins&imgId=".$this->data['imgId']."&size=".$this->data['size'];
     $this->data['menu']['Ajouter une image'] = 'index.php?controller=photo&action=addImg';
   }
 }
 ?>
