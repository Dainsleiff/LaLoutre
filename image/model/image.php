<?php

  # Notion d'image
  class Image {
    private $url="";
    private $id=0;
    private $category;
    private $commentaire;
    private $nbvotes=0;
    private $votes=0;

    function __construct($url,$id,$category = null,$commentaire, $nbvotes=0, $votes=0) {
      $this->url = $url;
      $this->id = $id;
      $this->category = $category;
      $this->commentaire = $commentaire;
      $this->nbvotes = $nbvotes;
      $this->votes = $votes;
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
    function getNbVotes(){
      return $this->nbvotes;
    }
    function getVotes(){
      return $this->votes;
    }
  }


?>
