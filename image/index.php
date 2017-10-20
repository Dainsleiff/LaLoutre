<?php
include_once('controller/superController.ctrl.php');
//on créer le superController
$main = new superController();

//on récupère l'action et on crée
$controller = $main->get('controller');
if($controller == null || $controller == ''){
  $controller = 'default';
}
$main->launchController($controller);
 ?>
