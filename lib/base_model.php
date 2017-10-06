<?php

  class BaseModel{
    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null){
      // Käydään assosiaatiolistan avaimet läpi
      foreach($attributes as $attribute => $value){
        // Jos avaimen niminen attribuutti on olemassa...
        if(property_exists($this, $attribute)){
          // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
          $this->{$attribute} = $value;
        }
      }
    }
    
    public function validate_string_length($string, $length) {
        $errors = array();
        
        if(strlen($string) > $length){
          $errors[] = 'Merkkijono on liian pitkä (max ' . $length . ' merkkiä.)';
        }
        return $errors;
    }
    
     public function string_not_null($string, $name) {
        $errors = array();
        
        if(strlen($string) == 0){
          $errors[] = $name .' on liian lyhyt.';
        }
        return $errors;
    }

    public function errors(){
      // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
      $errors = array();
      
      foreach($this->validators as $validator){
        $errors = array_merge($errors, $this->{$validator}());
      } 
      return $errors;
    }

  }
