<?php

  # Notion d'image
  class Image {
    private $url="";
    private $id=0;
    private $category;
    private $commentaire;

    function __construct($url,$id,$category = null,$commentaire) {
      $this->url = $url;
      $this->id = $id;
      $this->category = $category;
      $this->commentaire = $commentaire;
    }

    # Retourne l'URL de cette image
    function getURL() {
		return $this->url;
    }
    function getId() {
      return $this->id;
    }
    function getCategorie(){
      return $this->category;
    }
    function getCommentaire(){
      return $this->commentaire;
    }
  }


?>
