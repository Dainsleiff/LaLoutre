<?php
// Dans les versions de PHP antiéreures à 4.1.0, la variable $HTTP_POST_FILES
// doit être utilisée à la place de la variable $_FILES.

      // $uploaddir = '/var/www/html/sites/php/LaLoutre/image/model/IMG/jons/uploads/';
      // $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
      // var_dump($uploadfile);
      // echo '<pre>';
      // //$_FILES['userfile']['tmp_name'] = "test.jpg";
      // print_r($_FILES);
      // if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
      //     echo "Le fichier est valide, et a été téléchargé
      //            avec succès. Voici plus d'informations :\n";
      // } else {
      //     echo "Attaque potentielle par téléchargement de fichiers.
      //           Voici plus d'informations :\n";
      // }
      echo $this->data['resultAdd'];
      echo '</pre>';
    //"INSERT into image values(NULL,'url', 'category', 'comment')";
?>
