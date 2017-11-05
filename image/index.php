<?php
include_once('controller/superController.ctrl.php');
session_start();
//on créer le superController
$main = new superController();

//on récupère l'action et on crée
$controller = $main->get('controller');
if($controller == null || $controller == ''){
  $controller = 'default';
}
//on reset à null la variable de session si bouton reset
if(isset($_REQUEST['reset'])){
  $_SESSION['categorieSearch'] = null;
}
$main->launchController($controller);
 ?>
