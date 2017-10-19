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
    
    public function validate_string_length($string, $length, $name) {
        $errors = array();
        
        if(strlen($string) > $length){
          $errors[] = $name . ' on liian pitkä (max ' . $length . ' merkkiä.)';
        }
        return $errors;
    }
    
    public function validate_string_length_shortness($string, $length, $name) {
        $errors = array();
        
        if(strlen($string) < $length){
          $errors[] = $name .' on liian lyhyt (vähintään ' . $length . ' merkkiä.)';
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
