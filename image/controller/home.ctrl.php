
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
    $this->data['menu']['Voir photos']='index.php?controller=photo&action=viewPhoto';
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
