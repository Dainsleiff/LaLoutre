
<?php
class home{
  private $action = null;
  private $data = null;
  function __construct() {
    //récupération
    if (isset($_GET['action']) && $_GET['action'] !='') {
      $this->action = $_GET['action'];
    }
    else {
      $this->action = 'default';
    }
    $this->data['menu']['Home']='index.php';
    $this->data['menu']['aPropos']='index.php?controller=home&action=aPropos';
    $this->data['menu']['Voir photos']='index.php?controller=photo&action=viewPhoto&imgId=1';
    $this->data['menu']['Ajouter une image'] = 'index.php?controller=photo&action=addImg';
  }

  public function launchAction(){
    switch ($this->action) {
      case 'aPropos':
      include_once 'view/aPropos.view.php';
      break;

      default:
      include_once 'view/home.view.php';
      break;
    }
  }
}
?>
