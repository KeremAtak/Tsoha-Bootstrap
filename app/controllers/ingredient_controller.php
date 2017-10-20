<?php

  require 'app/models/drink.php';
  require 'app/models/ingredient.php';
  
  /**
    * Ainesosien controller.
    */
  class IngredientController extends BaseController{
      
    /**
      * Metodi luo näkymän missä listataan kaikki ainesosat. Parametri $word päättää millaisessa järjestyksessä
      * kysely palauttaa ainesosat käyttäjälle.
      */
    public static function ingredients($word){
        $ingredients = Ingredient::all($word);
   	View::make('ingredients.html', array('ingredients' => $ingredients));
    }
    
    /**
      * Metodi luo näkymän yksittäiselle ainesosan id:n perusteella ja tuo näkymään ainesosaan liittyvät
      * drinkit. 
      */
    public static function ingredient($id){
        $ingredient = Ingredient::single($id);
        $drinks = Drink::find_by_ingredient_id($id);
        
        View::make('ingredient.html', array('ingredient' => $ingredient, 'drinks' => $drinks));
    }
    
  }