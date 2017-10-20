	<?php
		class superController {
			private $controller = null;
			private $action = null;
			private $data = null;

			function __construct() {
				// récupération du controller
				if(isset($_GET['controller']) && $_GET['controller'] !=''){
					$this->controller = $_GET['controller'];
				}
				else{
						$this->controller = 'home';
				}
				//récupération
				if (isset($_GET['action']) && $_GET['action'] !='') {
					$this->action = $_GET['action'];
				}
				else {
					$this->action = 'default';
				}
			}

			// LISTE DES ACTIONS DE CE CONTROLEUR
			public function launchController($controller){
				switch ($controller) {
					case 'photo':
						require_once("controller/photo.ctrl.php");
						$objController = new photo();
						$objController->launchAction();
						break;
					case 'photoMatrix':
						require_once('controller/photoMatrix.ctrl.php');
						$objController = new photoMatrix();
						$objController->launchAction();
						break;
					default:
					require_once("controller/home.ctrl.php");
						$objController = new home();
						$objController->launchAction();
						break;
				}
			}


			public function get($element){
				switch($element){
					case 'controller':
						return $this->controller;
						break;
					case 'action':
						return $this->action;
						break;
					case '$data':
						return $this->$data;
						break;
					default:
						return null;
				}
			}
		}
	?>
