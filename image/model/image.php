<?php

  # Notion d'image
  class Image {
    private $url="";
    private $id=0;
    private $category;

    function __construct($url,$id,$category = null) {
      $this->url = $url;
      $this->id = $id;
      $this->category = $category;
    }

    # Retourne l'URL de cette image
    function getURL() {
		return $this->url;
    }
    function getId() {
      return $this->id;
    }
  }


?>
