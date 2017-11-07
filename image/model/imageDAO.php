<?php
	require_once("image.php");
	# Le 'Data Access Object' d'un ensemble images
	class ImageDAO {

		# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		# A MODIFIER EN FONCTION DE VOTRE INSTALLATION
		# !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		# Chemin LOCAL où se trouvent les images
		private $path="model/IMG";
		# Chemin URL où se trouvent les images
		const urlPath="http://localhost/sites/php/LaLoutre/image/model/IMG";
		# Tableau pour stocker tous les chemins des images
		private $imgEntry;
		private $size = null;
		private $categorieSearch = null;
		//objet PDO
		private $db = null;
		# Lecture récursive d'un répertoire d'images
		# Ce ne sont pas des objets qui sont stockes mais juste
		# des chemins vers les images.
		private function readDir($dir) {
			# build the full path using location of the image base
			$fdir=$this->path.$dir;
			if (is_dir($fdir)) {
				$d = opendir($fdir);
				while (($file = readdir($d)) !== false) {
					if (is_dir($fdir."/".$file)) {
						# This entry is a directory, just have to avoid . and .. or anything starts with '.'
						if (($file[0] != '.')) {
							# a recursive call
							$this->readDir($dir."/".$file);
						}
					} else {
						# a simple file, store it in the file list
						if (($file[0] != '.')) {
							$this->imgEntry[]="$dir/$file";
						}
					}
				}
			}
		}



		function __construct() {
			//$this->readDir("");
			$dsn = 'sqlite:../BD/john.db'; //bd source
			$user = '';
			$pass = '';
			try {
				$this->db = new PDO($dsn,$user,$pass); //initialise l'attribut privé db de $this
				$req = 'SELECT * FROM image';
				$prep = $this->db->query($req);
				$results = $prep->fetchAll(PDO::FETCH_ASSOC);
				$this->size = count($results);
			} catch (PDOException $e) {
				die("Erreur : ".$e->getMessage());
			}
		}

		# Retourne le nombre d'images référencées dans le DAO
		function size() {
			return $this->size;
		}

		# Retourne un objet image correspondant à l'identifiant
		function getImagev1($imgId) {
			# Verifie que cet identifiant est correct
			if(!($imgId >=1 and  $imgId <=$this->size())) {
				$size=$this->size();
				debug_print_backtrace();
				die("<H1>Erreur dans ImageDAO.getImage: imgId=$imgId incorrect</H1>");
			}

			return new Image(self::urlPath.$this->try[$imgId-1],$imgId);;
		}

		function getImage($id = 1, $strict = 0){
			$req = 'SELECT * FROM image WHERE id=:id';
			//si une catégorie est recherchée on l'ajoute à la rechercher
			if(isset($this->categorieSearch) && $this->categorieSearch != ''){
				$req = "SELECT * FROM image WHERE category=:category";
				if ($strict) {
					$req .= " AND id=:id";
				}
			}
			$stmt =$this->db->prepare($req);
			if($stmt == true && ! (isset($this->categorieSearch) && $this->categorieSearch != '')){
				$stmt->BindParam(':id',$id,PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_OBJ);
				$result = $result[0];
				$imgReturned = new Image(self::urlPath.'/'.$result->path,$result->id,$result->category,$result->comment);
			} elseif ($stmt == true && isset($this->categorieSearch) && $this->categorieSearch != '') {
				$stmt->BindParam(':category', $this->categorieSearch, PDO::PARAM_STR);
				if ($strict) {
					$stmt->BindParam(':id',$id,PDO::PARAM_INT);
				}
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_OBJ);
				if ($result) {
					$result = $result[0];
					$imgReturned = new Image(self::urlPath.'/'.$result->path,$result->id,$result->category,$result->comment);
				} else {
					$imgReturned = $this;
				}
			}
			else {
				print "Error in getImage. id=".$id."<br/>";
				$err = $this->db->errorInfo();
				var_dump($err);
				$result = null;
				$imgReturned = null;
			}
			return $imgReturned;
		}

		# Retourne une image au hazard
		function getRandomImage() {
			if(isset($this->categorieSearch) && $this->categorieSearch != ''){
				$req = "SELECT * FROM image WHERE category=:category";
				$stmt = $this->db->prepare($req);
				$stmt->BindParam(':category',$this->categorieSearch,PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_OBJ);
				$id = $result[rand(0,count($result)-1)]->id;
				$img = $this->getImage($id, 1);
			} else {
				$id = rand(0,$this->size()-1);
				$img = $this->getImage($id);
			}
			return $img;
		}

		// Retourne une liste d'images au hasard (photo matrix)
		function getRandomMatrix($nb){
			if(isset($this->categorieSearch) && $this->categorieSearch != ''){
				$req = "SELECT * FROM image WHERE category=:category";
				$stmt = $this->db->prepare($req);
				$stmt->BindParam(':category',$this->categorieSearch,PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_OBJ);
				for ($i=0; $i < $nb; $i++) { 
					$id = $result[rand(0,count($result)-1)]->id;
					$res[] = $this->getImage($id, 1);
				}
			} else {
				for ($i=0; $i < $nb; $i++) {
					$id = rand(0,$this->size()-1);
					$res[] = $this->getImage($id);
				}
			}
			return $res;
		}

		# Retourne l'objet de la premiere image
		function getFirstImage() {
			return $this->getImage();
		}

		# Retourne l'image suivante d'une image
		function getNextImage($id) {
			if (isset($this->categorieSearch) && $this->categorieSearch != '') {
				$req = "SELECT * FROM image WHERE category=:category AND id>:id";
				$stmt = $this->db->prepare($req);
				$stmt->BindParam(':category',$this->categorieSearch,PDO::PARAM_STR);
				$stmt->BindParam(':id',$id,PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_OBJ);
				if ($result == null) {
					$img = $this->getImage();
				} else {
					$img = $this->getImage($result[0]->id, 1);
				}
			} elseif ($id < $this->size()) {
				$img = $this->getImage($id+1);
			}
			return $img;
		}

		# Retourne l'image précédente d'une image
		function getPrevImage($id) {
			if (isset($this->categorieSearch) && $this->categorieSearch != '') {
				$req = "SELECT * FROM image WHERE category=:category AND id<:id ORDER BY id DESC";
				$stmt = $this->db->prepare($req);
				$stmt->BindParam(':category',$this->categorieSearch,PDO::PARAM_STR);
				$stmt->BindParam(':id',$id,PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_OBJ);
				if ($result == null) {
					$img = $this->getImage();
				} else {
					$img = $this->getImage($result[0]->id, 1);
				}
			} elseif($id == 1){
				$img = $this->getImage();
			} else {
				$img = $this->getImage($id-1);
			}
			return $img;
		}

		#Setter del'attribut categorieSearch (catégorie à rechercher)
		#$categorie String
		function setCategorieSearch($categorie){
			$this->categorieSearch = $categorie;
		}
		# saute en avant ou en arrière de $nb images
		# Retourne la nouvelle image
		function jumpToImage(image $img,$nb,$avancer,$reculer) {
			$id = $img->getId();
			if (isset($this->categorieSearch) && $this->categorieSearch != '' && $avancer) {
				$req = "SELECT * FROM image WHERE category=:category AND id>:id";
				$stmt = $this->db->prepare($req);
				$stmt->BindParam(':category',$this->categorieSearch,PDO::PARAM_STR);
				$stmt->BindParam(':id',$id,PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_OBJ);
				if ($result == null || ! isset($result[$nb-1])) {
					$img = $this->getImage();
				} else {
					$img = $this->getImage($result[$nb-1]->id, 1);
				}
			} elseif (isset($this->categorieSearch) && $this->categorieSearch != '' && $reculer) {
				$req = "SELECT * FROM image WHERE category=:category AND id<:id";
				$stmt = $this->db->prepare($req);
				$stmt->BindParam(':category',$this->categorieSearch,PDO::PARAM_STR);
				$stmt->BindParam(':id',$id,PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_OBJ);
				if ($result == null || ! isset($result[$nb-1])) {
					$img = $this->getImage();
				} else {
					$img = $this->getImage($result[$nb-1]->id, 1);
				}
			} elseif($reculer && (($id-$nb) >= 1)){
				$img = $this->getImage($id-$nb);
			} elseif($avancer && (($id+$nb) <= $this->size())){
				$img = $this->getImage($id+$nb);
			}
			return $img;
		}

		# Retourne la liste des images consécutives à partir d'une image
		function getImageList(image $img,$nb) {
			# Verifie que le nombre d'image est non nul
			if ($nb < 1) {
				$nb = 1;
			}
			$id = $img->getId();
			$max = $id+$nb;
			if (isset($this->categorieSearch) && $this->categorieSearch != '') {
				$req = "SELECT * FROM image WHERE category=:category AND id>=:id";
				$stmt = $this->db->prepare($req);
				$stmt->BindParam(':category',$this->categorieSearch,PDO::PARAM_STR);
				$stmt->BindParam(':id',$id,PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetchAll(PDO::FETCH_OBJ);
				$result = array_slice($result, 0, $nb);
				for ($i=0; $i < $nb; $i++) {
					if (!isset($result[$i])) {
						break;
					}
					$res[] = new Image(self::urlPath.'/'.$result[$i]->path,$result[$i]->id,$result[$i]->category,$result[$i]->comment);
				}

			} else {
				while ($id < $this->size() && $id < $max) {
					$res[] = $this->getImage($id);
					$id++;
				}
			}
			return $res;
		}

		//Retourne le nombre de photos contenues dans une catégorie
		function getCatSize($cat){
			$req = "SELECT count(id) FROM image WHERE category=:category";
			$stmt =$this->db->prepare($req);
			if($stmt == true){
				$stmt->BindParam(':category',$cat,PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetchColumn();
			}
			else{
				print "Error in getCatSize <br/>";
				$err = $this->db->errorInfo();
				var_dump($err);
				$result = null;
			}
			return $result;
		}

		function getSizeAll(){
			$req = "SELECT count(id) FROM image";
			$stmt =$this->db->prepare($req);
			if($stmt == true){
				$stmt->execute();
				$result = $stmt->fetchColumn();
			}
			else{
				print "Error in getCatSize <br/>";
				$err = $this->db->errorInfo();
				var_dump($err);
				$result = null;
			}
			return $result;
		}

		//fonction pour afficher l'ensemble des catégories existantes en base
		function getAllCategories(){
			$req = 'SELECT DISTINCT category FROM image';
			$stmt =$this->db->prepare($req);
			if($stmt == true){
				$stmt->execute();
				$results = $stmt->fetchAll(PDO::FETCH_OBJ);
			}
			else{
				print "Error in getAllCategories <br/>";
				$err = $this->db->errorInfo();
				var_dump($err);
				$results = null;
			}
			return $results;
		}

		//fonction pour changer le commentaire d'une image
		function changeComment($comment,$id){
			$req = 'UPDATE image SET comment =:comment WHERE id=:id';
			$stmt =$this->db->prepare($req);
			if($stmt == true){
				$res =$stmt->execute(array(':comment'=>$comment,':id'=>$id));
				if($res == false){
					var_dump($res);
				}
			}
			else {
				print "Error in changeComment while prepare.<br/>";
				$err = $this->db->errorInfo();
				var_dump($err);
			}
		}

		//fonction pour changer la catégorie d'une image
		function changeCategory($category,$id){
			$req = 'UPDATE image SET category = :category WHERE id=:id';
			$stmt =$this->db->prepare($req);
			if($stmt == true){
				var_dump($category);
				var_dump($id);
				$stmt->BindParam(':category',$category,PDO::PARAM_STR);
				$stmt->BindParam(':id',$id,PDO::PARAM_INT);
				$res =$stmt->execute();
			}
			else {
				print "Error in changeCategory while prepare.<br/>";
				$err = $this->db->errorInfo();
				var_dump($err);
			}
		}

		//fonction d'ajout d'image unique
		function addImg($url,$category,$comment){
			$req = "INSERT into image values(NULL,:url,:category,:comment)"; //on mets l'id à null car autoincremente
			$stmt =$this->db->prepare($req);
			if($stmt == true){
				$stmt->BindParam(':url',$url,PDO::PARAM_STR);
				$stmt->BindParam(':category',$category,PDO::PARAM_STR);
				$stmt->BindParam(':comment',$comment,PDO::PARAM_STR);
				$res =$stmt->execute();
				return $res;
			}
			else{
				print "Error in addImg while prepare.<br/>";
				$err = $this->db->errorInfo();
				var_dump($err);
				return null;
			}
		}

}






	# Test unitaire
	# Appeler le code PHP depuis le navigateur avec la variable test
	# Exemple : http://localhost/sites/php/cours1/image/model/imageDAO.php?test
	if (isset($_GET["test"])) {
		echo "<H1>Test de la classe ImageDAO</H1>";
		$imgDAO = new ImageDAO();
		echo "<p>Creation de l'objet ImageDAO.</p>\n";
		echo "<p>La base contient ".$imgDAO->size()." images.</p>\n";
		$img = $imgDAO->getFirstImage("");
		echo "La premiere image est : ".$img->getURL()."</p>\n";
		# Affiche l'image
		echo "<img src=\"".$img->getURL()."\"/>\n";
	}


	?>
